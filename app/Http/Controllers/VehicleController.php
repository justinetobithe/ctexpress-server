<?php

namespace App\Http\Controllers;

use App\Http\Requests\VehicleRequest;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::all();
        return response()->json([
            'status' => 'success',
            'data' => $vehicles,
        ], 200);
    }

    public function store(VehicleRequest $request)
    {
        $validated = $request->validated();

        $vehicle = Vehicle::create([
            'driver_id' => $validated['driver_id'],
            'license_plate' => $validated['license_plate'],
            'make' => $validated['make'],
            'model' => $validated['model'],
            'year' => $validated['year'],
            'capacity' => $validated['capacity'],
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Vehicle created successfully',
            'data' => $vehicle,
        ], 201);
    }

    public function update(VehicleRequest $request, Vehicle $vehicle)
    {
        $validated = $request->validated();

        $vehicle->driver_id = $validated['driver_id'];
        $vehicle->license_plate = $validated['license_plate'];
        $vehicle->make = $validated['make'];
        $vehicle->model = $validated['model'];
        $vehicle->year = $validated['year'];
        $vehicle->capacity = $validated['capacity'];

        $vehicle->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Vehicle updated successfully',
            'data' => $vehicle,
        ], 200);
    }

    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Vehicle deleted successfully',
        ], 200);
    }

    public function getVehiclesByDriver($driver_id)
    {
        $vehicles = Vehicle::with(['driver'])->where('driver_id', $driver_id)->get();

        return response()->json([
            'status' => 'success',
            'data' => $vehicles,
        ], 200);
    }
}
