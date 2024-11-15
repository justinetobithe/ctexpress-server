<?php

namespace App\Http\Controllers;

use App\Http\Requests\VehicleRequest;
use App\Models\Vehicle;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $pageSize = $request->input('page_size');
        $filter = $request->input('filter');
        $sortColumn = $request->input('sort_column', 'brand');
        $sortDesc = $request->input('sort_desc', false) ? 'desc' : 'asc';

        $query = Vehicle::with('driver');

        if ($filter) {
            $query->where(function ($q) use ($filter) {
                $q->where('license_plate', 'like', "%{$filter}%")
                    ->orWhere('brand', 'like', "%{$filter}%")
                    ->orWhere('model', 'like', "%{$filter}%")
                    ->orWhere('year', 'like', "%{$filter}%")
                    ->orWhere('capacity', 'like', "%{$filter}%");
            });
        }

        if (in_array($sortColumn, ['license_plate', 'brand', 'model', 'year', 'capacity'])) {
            $query->orderBy($sortColumn, $sortDesc);
        }

        if ($pageSize) {
            $vehicles = $query->paginate($pageSize);
        } else {
            $vehicles = $query->get();
        }

        return $this->success($vehicles);
    }

    public function show(string $id)
    {
        $vehicle = Vehicle::with('driver')->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'message' => __('messages.success.success'),
            'terminal' => $vehicle,
        ]);
    }

    public function store(VehicleRequest $request)
    {
        $validated = $request->validated();

        $vehicle = Vehicle::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => __('messages.success.created'),
            'data' => $vehicle,
        ]);
    }

    public function update(VehicleRequest $request, string $id)
    {
        $vehicle = Vehicle::findOrFail($id);
 
        $vehicle->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => __('messages.success.updated'),
            'data' => $vehicle,
        ], 200);
    }

    public function destroy(string $id)
    {
        $vehicle = Vehicle::findOrFail($id);
        $vehicle->delete();

        return response()->json([
            'status' => 'success',
            'message' => __('messages.success.deleted'),
            'terminal' => $vehicle,
        ]);
    }

    public function getVehiclesByDriver($driver_id)
    {
        $vehicles = Vehicle::with(['driver'])->where('driver_id', $driver_id)->get();

        return response()->json([
            'status' => 'success',
            'message' => __('messages.success.deleted'),
            'data' => $vehicles,
        ], 200);
    }
}
