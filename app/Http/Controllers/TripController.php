<?php

namespace App\Http\Controllers;

use App\Events\TripAssignEvent;
use App\Http\Requests\TripRequest;
use App\Models\Trip;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;

class TripController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $pageSize = $request->input('page_size');
        $filter = $request->input('filter');
        $sortColumn = $request->input('sort_column', 'start_time');
        $sortDesc = $request->input('sort_desc', false) ? 'desc' : 'asc';

        $query = Trip::with(['terminalFrom', 'terminalTo', 'driver.vehicle']);

        if ($filter) {
            $query->where(function ($q) use ($filter) {
                $q->where('passenger_capacity', 'like', "%{$filter}%")
                    ->orWhere('start_time', 'like', "%{$filter}%")
                    ->orWhere('trip_date', 'like', "%{$filter}%")
                    ->orWhere('fare_amount', 'like', "%{$filter}%")
                    ->orWhere('status', 'like', "%{$filter}%");
            });
        }

        $query->orderBy('id', 'desc');

        if (in_array($sortColumn, ['passenger_capacity', 'start_time', 'trip_date', 'fare_amount', 'status'])) {
            $query->orderBy($sortColumn, $sortDesc);
        }

        if ($pageSize) {
            $trips = $query->paginate($pageSize);
        } else {
            $trips = $query->get();
        }

        return $this->success($trips);
    }

    public function show(string $id)
    {
        $trip = Trip::with(['terminalFrom', 'terminalTo', 'driver.vehicle', 'bookings.user', 'kiosks'])->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'message' => __('messages.success.fetched'),
            'data' => $trip,
        ]);
    }

    public function store(TripRequest $request)
    {
        $validated = $request->validated();

        $startTime = Carbon::parse($validated['start_time']);
        $tripDate = Carbon::parse($validated['trip_date']);
        $fullStartTime = $tripDate->setTimeFromTimeString($startTime->toTimeString());

        if ($fullStartTime->isBefore(Carbon::now())) {
            return response()->json([
                'status' => 'error',
                'message' => 'The trip cannot be scheduled in the past.',
            ]);
        }

        $upcomingTrips = Trip::where('driver_id', $validated['driver_id'])
            ->where('start_time', '>', Carbon::now())
            ->orderBy('start_time', 'asc')
            ->get();

        if ($upcomingTrips->isNotEmpty()) {
            foreach ($upcomingTrips as $trip) {
                $tripStartTime = Carbon::parse($trip->trip_date . ' ' . $trip->start_time);
                $tripEndTime = $tripStartTime->copy()->addHours(3);

                if ($fullStartTime->isBetween($tripStartTime->subHours(3), $tripEndTime->addHours(3))) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'The new trip cannot be scheduled within 3 hours of existing trips.',
                    ]);
                }
            }
        }

        $trip = Trip::create($validated);

        broadcast(new TripAssignEvent([
            'driver_id' => $trip->driver_id
        ]));

        return response()->json([
            'status' => 'success',
            'message' => __('messages.success.created'),
            'data' => $trip,
        ]);
    }


    public function update(TripRequest $request, string $id)
    {
        $trip = Trip::findOrFail($id);

        $previousDriverId = $trip->driver_id;
        $newDriverId = $request->input('driver_id');

        if ($newDriverId && $newDriverId !== $previousDriverId) {
            $startTime = Carbon::parse($request->input('start_time'));
            $tripDate = Carbon::parse($request->input('trip_date'));

            $existingTrip = Trip::where('driver_id', $newDriverId)
                ->where('trip_date', $tripDate->format('Y-m-d'))
                ->where(function ($query) use ($startTime) {
                    $query->whereBetween('start_time', [
                        $startTime->copy()->subHours(3)->format('H:i:s'),
                        $startTime->copy()->addHours(3)->format('H:i:s')
                    ])
                        ->orWhere(function ($subQuery) use ($startTime) {
                            $subQuery->where('start_time', '<', $startTime->copy()->addHours(3)->format('H:i:s'))
                                ->where('start_time', '>', $startTime->copy()->subHours(3)->format('H:i:s'));
                        });
                })
                ->first();

            if ($existingTrip) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'The new driver is already assigned to a trip within the next 3 hours.',
                ]);
            }

            broadcast(new TripAssignEvent([
                'driver_id' => $newDriverId
            ]));
        }

        $trip->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => __('messages.success.updated'),
            'data' => $trip,
        ], 200);
    }


    public function destroy(string $id)
    {
        $trip = Trip::findOrFail($id);
        $trip->delete();

        return response()->json([
            'status' => 'success',
            'message' => __('messages.success.deleted'),
            'data' => $trip,
        ]);
    }

    public function getTripsByDriver($driverId, Request $request)
    {
        $query = Trip::with(['terminalFrom', 'terminalTo', 'driver.vehicle'])
            ->where('driver_id', $driverId)
            ->orderBy('start_time', 'asc');

        if ($request->has('date')) {
            $date = $request->input('date');
            $query->where('trip_date', $date);
        }

        $trips = $query->get();

        return response()->json([
            'status' => true,
            'message' => 'Trips retrieved successfully for driver ID: ' . $driverId,
            'data' => $trips,
        ], 200);
    }

    public function getFutureTrips(Request $request)
    {
        $currentDate = Carbon::now()->format('Y-m-d');
        $currentTime = Carbon::now()->format('H:i:s');


        $query = Trip::with(['terminalFrom', 'terminalTo', 'driver.vehicle'])
            ->where('trip_date', $currentDate)
            ->where('status', 'pending')
            ->where('start_time', '>', $currentTime);

        if ($request->has('from_terminal_id') && $request->has('to_terminal_id')) {
            $fromTerminalId = $request->input('from_terminal_id');
            $toTerminalId = $request->input('to_terminal_id');

            $query->where('from_terminal_id', $fromTerminalId)
                ->where('to_terminal_id', $toTerminalId);
        }

        $trips = $query->orderBy('start_time', 'asc')->get();

        return response()->json([
            'status' => true,
            'message' => 'Future trips retrieved successfully',
            'data' => $trips,
        ], 200);
    }

    public function updateTripStatus(Request $request, $id)
    {
        $trip = Trip::findOrFail($id);

        $trip->status = $request->status;
        $trip->save();

        return response()->json([
            'status' => true,
            'message' => 'Trip status updated to in_progress successfully.',
            'data' => $trip,
        ], 200);
    }

    public function updateDriverDecision(Request $request, $id, $driverId)
    {
        $request->validate([
            'decision' => 'required|in:approved,rejected',
        ]);

        $trip = Trip::where('id', $id)->where('driver_id', $driverId)->first();

        if (!$trip) {
            return response()->json([
                'status' => 'error',
                'message' => 'Trip not found.',
            ]);
        }

        if ($request->decision === 'approved') {
            $trip->is_driver_accepted = 1;
            $trip->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Trip has been successfully approved.',
                'data' => $trip,
            ], 200);
        } elseif ($request->decision === 'rejected') {
            $trip->driver_id = null;
            $trip->is_driver_accepted = 0;
            $trip->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Trip has been successfully rejected.',
                'data' => $trip,
            ], 200);
        }
    }

    public function getTripsByTerminals(Request $request)
    {
        // return $request->all();
        $fromTerminalId = $request->query('fromTerminal');
        $toTerminalId = $request->query('toTerminal');

        if (!$fromTerminalId || !$toTerminalId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Both from_terminal_id and to_terminal_id are required.',
            ]);
        }

        $currentDate = Carbon::now()->format('Y-m-d');
        $currentTime = Carbon::now()->addMinutes(30)->format('H:i:s');

        $trips = Trip::with(['terminalFrom', 'terminalTo', 'driver.vehicle'])
            ->where('from_terminal_id', $fromTerminalId)
            ->where('to_terminal_id', $toTerminalId)
            ->where('trip_date', $currentDate)
            ->where('start_time', '>=', $currentTime)
            ->orderBy('start_time', 'asc')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Trips retrieved successfully for the specified terminals and time frame.',
            'data' => $trips,
        ], 200);
    }

    public function getTripsToday()
    {
        $currentDate = Carbon::now()->format('Y-m-d');
        $currentTime = Carbon::now()->addMinutes(30)->format('H:i:s');

        $trips = Trip::with(['terminalFrom', 'terminalTo', 'driver.vehicle'])
            ->where('trip_date', $currentDate)
            ->where('start_time', '>=', $currentTime)
            ->orderBy('start_time', 'asc')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Trips retrieved successfully for today and 30 minutes before the current time.',
            'data' => $trips,
        ], 200);
    }

    public function getTripsTodayWithTerminals(Request $request)
    {
        $fromTerminalId = $request->input('from_terminal_id');
        $toTerminalId = $request->input('to_terminal_id');

        if (!$fromTerminalId || !$toTerminalId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Both from_terminal_id and to_terminal_id are required.',
            ]);
        }

        $currentDate = Carbon::now()->format('Y-m-d');
        $currentTime = Carbon::now()->addMinutes(30)->format('H:i:s');

        $trips = Trip::with(['terminalFrom', 'terminalTo', 'driver.vehicle'])
            ->where('trip_date', $currentDate)
            ->where('from_terminal_id', $fromTerminalId)
            ->where('to_terminal_id', $toTerminalId)
            ->where('start_time', '>=', $currentTime)
            ->orderBy('start_time', 'asc')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Trips retrieved successfully for today, including those starting 30 minutes before now.',
            'data' => $trips,
        ], 200);
    }
}
