<?php

namespace App\Http\Controllers;

use App\Http\Requests\RouteRequest;
use App\Models\Route;
use Illuminate\Http\Request;

class RouteController extends Controller
{
    public function index()
    {
        $routes = Route::all();
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
}
