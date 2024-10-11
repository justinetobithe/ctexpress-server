<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RouteController;
use App\Http\Controllers\TerminalController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VehicleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/user', [AuthController::class, 'user']);

    Route::get('/users', [UserController::class, 'index']);
    Route::prefix('/user')->group(function () {
        Route::put('/{id}', [UserController::class, 'update']);
    });

    Route::get('/bookings', [BookingController::class, 'index']);
    Route::prefix('/booking')->group(function () {
        Route::post('/', [BookingController::class, 'store']);
        Route::get('/{id}', [BookingController::class, 'show']);
        Route::put('/{id}', [BookingController::class, 'update']);
        Route::delete('/{id}', [BookingController::class, 'destroy']);
    });

    Route::get('/routes', [RouteController::class, 'index']);
    Route::prefix('/route')->group(function () {
        Route::post('/', [RouteController::class, 'store']);
        Route::get('/{id}', [RouteController::class, 'show']);
        Route::put('/{id}', [RouteController::class, 'update']);
        Route::delete('/{id}', [RouteController::class, 'destroy']);
        Route::get('/driver/{driverId}', [RouteController::class, 'getRoutesByDriver']);
        Route::get('/future/routes', [RouteController::class, 'getFutureRoutes']);
    });

    Route::get('/terminals', [TerminalController::class, 'index']);
    Route::prefix('/terminal')->group(function () {
        Route::post('/', [TerminalController::class, 'store']);
        Route::get('/{terminal}', [TerminalController::class, 'show']);
        Route::put('/{terminal}', [TerminalController::class, 'update']);
        Route::delete('/{terminal}', [TerminalController::class, 'destroy']);
    });

    Route::get('/vehicles', [VehicleController::class, 'index']);
    Route::prefix('/vehicle')->group(function () {
        Route::post('/', [VehicleController::class, 'store']);
        Route::get('/{vehicle}', [VehicleController::class, 'show']);
        Route::put('/{vehicle}', [VehicleController::class, 'update']);
        Route::delete('/{vehicle}', [VehicleController::class, 'destroy']);
        Route::get('/driver/{driverId}', [VehicleController::class, 'getVehiclesByDriver']);
    });

    Route::prefix('/payment')->group(function () {
        Route::post('/', [PaymentController::class, 'store']);
    });
});
