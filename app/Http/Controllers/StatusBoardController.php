<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Passenger;
use App\Models\Trip;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StatusBoardController extends Controller
{
    public function vehiclesAvailable()
    {
        $now = Carbon::now();

        $vehiclesCount = Trip::with(['driver.vehicle'])
            ->where('trip_date', '=', $now->toDateString())
            ->whereTime('start_time', '<=', $now->toTimeString())
            ->whereNull('drop_at')
            ->count();

        return response()->json([
            'status' => true,
            'message' => 'Available vehicles count fetched successfully.',
            'data' =>  $vehiclesCount
        ]);
    }


    public function ongoingVehicles()
    {
        $now = Carbon::now();

        $ongoingTrips = Trip::with(['driver.vehicle'])
            ->where('trip_date', '=', $now->toDateString())
            // ->whereTime('start_time', '<=', $now->toTimeString())
            ->where('status', 'in_progress')
            ->count();

        return response()->json([
            'status' => true,
            'message' => 'Ongoing vehicles fetched successfully.',
            'data' => $ongoingTrips
        ]);
    }

    public function nextTrip()
    {
        $now = Carbon::now();

        $nextTrip = Trip::with(['driver.vehicle'])
            ->where('trip_date', '=', $now->toDateString())
            ->whereTime('start_time', '>=', $now->toTimeString())
            ->orderBy('start_time', 'asc')
            ->first();

        if ($nextTrip) {
            $passengerCount = Passenger::where('trip_id', $nextTrip->id)->count();
            $capacity = $nextTrip->driver->vehicle->passenger_capacity ?? 0;
            $nextTrip->passenger_count = $passengerCount;
        }

        return response()->json([
            'status' => true,
            'message' => 'Next trip fetched successfully.',
            'data' => $nextTrip
        ]);
    }

    public function awaitingVehicles()
    {
        $now = Carbon::now();

        $awaitingTrips = Trip::with(['driver.vehicle'])
            ->where('trip_date', '=', $now->toDateString())
            ->where('status', 'pending')
            ->get()
            ->map(function ($trip) {
                $passengerCount = Passenger::where('trip_id', $trip->id)->count();
                $capacity = $trip->driver->vehicle->capacity ?? 0;
                $trip->passenger_count = $passengerCount;
                $trip->remaining_capacity = $capacity - $passengerCount;

                return $trip;
            });

        return response()->json([
            'status' => true,
            'message' => 'Awaiting vehicles fetched successfully.',
            'data' => $awaitingTrips
        ]);
    }

    public function bookingsWithPassengers()
    {
        $now = Carbon::now();

        $bookings = Booking::with(['trip', 'trip.driver.vehicle', 'passengers'])
            ->whereHas('trip', function ($query) use ($now) {
                $query->where('trip_date', '=', $now->toDateString())
                    ->whereTime('start_time', '>=', $now->toTimeString());
            })
            ->get()
            ->map(function ($booking) {
                $trip = $booking->trip;
                $passengerCount = $booking->passengers->count();
                $capacity = $trip->driver->vehicle->passenger_capacity ?? 0;
                $booking->passenger_count = $passengerCount;
                $booking->remaining_capacity = $capacity - $passengerCount;

                return $booking;
            });

        return response()->json([
            'status' => true,
            'message' => 'Bookings with passengers fetched successfully.',
            'data' => $bookings
        ]);
    }
}
