<?php

namespace App\Http\Controllers;

use App\Http\Requests\RouteRequest;
use App\Models\Route;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RouteController extends Controller
{
    public function index()
    {
        $routes = Route::with(['terminalFrom', 'terminalTo', 'driver', 'vehicle'])->get();
        return response()->json([
            'status' => true,
            'message' => 'Routes retrieved successfully',
            'data' => $routes,
        ], 200);
    }

    public function store(RouteRequest $request)
    {
        $route = Route::create($request->validated());
        return response()->json([
            'status' => true,
            'message' => 'Route created successfully',
            'data' => $route,
        ], 201);
    }

    public function show(Route $route)
    {
        $route->load(['terminalFrom', 'terminalTo', 'driver', 'vehicle']);
        return response()->json([
            'status' => true,
            'message' => 'Route retrieved successfully',
            'data' => $route,
        ], 200);
    }

    public function update(RouteRequest $request, Route $route)
    {
        $route->update($request->validated());
        return response()->json([
            'status' => true,
            'message' => 'Route updated successfully',
            'data' => $route,
        ], 200);
    }

    public function destroy(Route $route)
    {
        $route->delete();
        return response()->json([
            'status' => true,
            'message' => 'Route deleted successfully',
        ], 200);
    }

    public function getRoutesByDriver($driverId, Request $request)
    {
        $query = Route::with(['terminalFrom', 'terminalTo', 'driver', 'vehicle'])
            ->where('driver_id', $driverId)
            ->orderBy('start_time', 'asc');

        if ($request->has('date')) {
            $date = $request->input('date');
            $query->where('route_date', $date);
        }

        $routes = $query->get();

        return response()->json([
            'status' => true,
            'message' => 'Routes retrieved successfully for driver ID: ' . $driverId,
            'data' => $routes,
        ], 200);
    }

    public function getFutureRoutes(Request $request)
    {
        $currentDate = Carbon::now()->format('Y-m-d');
        $currentTime = Carbon::now()->format('H:i:s');


        $query = Route::with(['terminalFrom', 'terminalTo', 'driver', 'vehicle'])
            ->where('route_date', $currentDate)
            ->where('status', 'pending');
        // ->where('start_time', '>', $currentTime);

        if ($request->has('from_terminal_id') && $request->has('to_terminal_id')) {
            $fromTerminalId = $request->input('from_terminal_id');
            $toTerminalId = $request->input('to_terminal_id');

            $query->where('from_terminal_id', $fromTerminalId)
                ->where('to_terminal_id', $toTerminalId);
        }

        $routes = $query->orderBy('start_time', 'asc')->get();

        return response()->json([
            'status' => true,
            'message' => 'Future routes retrieved successfully',
            'data' => $routes,
        ], 200);
    }
}
