<?php

namespace App\Http\Controllers;

use App\Http\Requests\TripRequest;
use App\Models\Trip;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TripController extends Controller
{

    public function index()
    {
        $trips = Trip::with(['terminalFrom', 'terminalTo', 'driver', 'vehicle'])->get();
        return response()->json([
            'status' => true,
            'message' => 'Trips retrieved successfully',
            'data' => $trips,
        ], 200);
    }

    public function store(TripRequest $request)
    {
        $trip = Trip::create($request->validated());
        return response()->json([
            'status' => true,
            'message' => 'Trip created successfully',
            'data' => $trip,
        ], 201);
    }

    public function show($id)
    {
        $trip = Trip::with(['terminalFrom', 'terminalTo', 'driver', 'vehicle', 'passengers.booking.user'])->where('id', $id)->first();

        return response()->json([
            'status' => true,
            'message' => 'Trip retrieved successfully',
            'data' => $trip,
        ], 200);
    }

    public function update(TripRequest $request, Trip $trip)
    {
        $trip->update($request->validated());
        return response()->json([
            'status' => true,
            'message' => 'Trip updated successfully',
            'data' => $trip,
        ], 200);
    }

    public function destroy(Trip $trip)
    {
        $trip->delete();
        return response()->json([
            'status' => true,
            'message' => 'Trip deleted successfully',
        ], 200);
    }

    public function getTripsByDriver($driverId, Request $request)
    {
        $query = Trip::with(['terminalFrom', 'terminalTo', 'driver', 'vehicle'])
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


        $query = Trip::with(['terminalFrom', 'terminalTo', 'driver', 'vehicle'])
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
}
