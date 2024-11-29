<?php

namespace App\Http\Controllers;

use App\Events\TripAssignEvent;
use App\Events\TripStatusUpdatedEvent;
use App\Http\Requests\TripRequest;
use App\Models\Booking;
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
        $trip = Trip::with(['terminalFrom', 'terminalTo', 'driver.vehicle', 'passengers.booking.user'])->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'message' => __('messages.success.fetched'),
            'data' => $trip,
        ]);
    }

    public function store(TripRequest $request)
    {
        $validated = $request->validated();

        $trip = Trip::create($validated);

        broadcast(new TripAssignEvent([
            'user_id' => 1
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

        $user_id = Booking::select(['user_id'])->where('trip_id', $trip->id)->pluck('user_id');

        foreach ($user_id as $id) {
            broadcast(new TripStatusUpdatedEvent([
                'user_id' => $id
            ]));
        }

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
}
