<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Get the number of passengers by counting users with the 'passenger' role.
     */
    public function numberOfPassengers()
    {
        $passengerCount = User::where('role', 'passenger')->count();

        return response()->json([
            'status' => true,
            'message' => 'Number of passengers fetched successfully.',
            'data' => $passengerCount
        ]);
    }

    /**
     * Get the number of drivers by counting users with the 'driver' role.
     */
    public function numberOfDrivers()
    {
        $driverCount = User::where('role', 'driver')->count();

        return response()->json([
            'status' => true,
            'message' => 'Number of drivers fetched successfully.',
            'data' => $driverCount
        ]);
    }

    /**
     * Get the number of vehicles by counting entries in the vehicles table.
     */
    public function numberOfVehicles()
    {
        $vehicleCount = Vehicle::count();

        return response()->json([
            'status' => true,
            'message' => 'Number of vehicles fetched successfully.',
            'data' => $vehicleCount
        ]);
    }
}
