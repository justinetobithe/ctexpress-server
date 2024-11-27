<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\MeController;
use App\Http\Controllers\PassengerController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RouteController;
use App\Http\Controllers\StatusBoardController;
use App\Http\Controllers\TerminalController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VehicleController;
use App\Services\PaymongoService;
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

// Route::group(['prefix' => 'auth'], function () {
//     Route::post('login', [AuthController::class, 'login']);
//     Route::post('register', [AuthController::class, 'register']);
//     // Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
// });

Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::post('validate/google', 'validateGoogleLogin');
    Route::post('login/google', 'loginWithGoogle');
});


Route::middleware('auth:sanctum', 'throttle:60,1')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/me', MeController::class);

    Route::get('/auth/user', [AuthController::class, 'user']);

    Route::get('/users', [UserController::class, 'index']);
    Route::prefix('/user')->group(function () {
        Route::put('/{id}', [UserController::class, 'update']);
        Route::post('/', [UserController::class, 'store']);
        Route::get('/{user}', [UserController::class, 'show']);
        Route::delete('/{user}', [UserController::class, 'destroy']);

        Route::get('/passengers', [UserController::class, 'showPassengers']);
    });


    Route::get('/drivers', [UserController::class, 'showDrivers']);

    Route::get('/bookings', [BookingController::class, 'index']);
    Route::prefix('/booking')->group(function () {
        Route::post('/', [BookingController::class, 'store']);
        Route::get('/{id}', [BookingController::class, 'show']);
        Route::put('/{id}', [BookingController::class, 'update']);
        Route::delete('/{id}', [BookingController::class, 'destroy']);

        Route::get('/current/{user_id}', [BookingController::class, 'currentBookingForUser']);
        Route::put('/drop-off/booking/{id}', [BookingController::class, 'dropOffPassenger']);

        Route::put('/status/{id}', [BookingController::class, 'updateBookingStatus']);
    });

    Route::get('/trips', [TripController::class, 'index']);
    Route::prefix('/trip')->group(function () {
        Route::post('/', [TripController::class, 'store']);
        Route::get('/{id}', [TripController::class, 'show']);
        Route::put('/{id}', [TripController::class, 'update']);
        Route::delete('/{id}', [TripController::class, 'destroy']);
        Route::get('/driver/{driverId}', [TripController::class, 'getTripsByDriver']);
        Route::get('/future/trips', [TripController::class, 'getFutureTrips']);
        Route::put('/status/{id}', [TripController::class, 'updateTripStatus']);

        Route::put('/{id}/decision/{driverId}', [TripController::class, 'updateDriverDecision']);
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
        Route::get('/{id}', [VehicleController::class, 'show']);
        Route::put('/{id}', [VehicleController::class, 'update']);
        Route::delete('/{id}', [VehicleController::class, 'destroy']);
        Route::get('/driver/{driverId}', [VehicleController::class, 'getVehiclesByDriver']);
    });

    Route::get('/passengers', [PassengerController::class, 'index']);
    Route::prefix('/passenger')->group(function () {
        Route::put('/{id}/confirm', [PassengerController::class, 'confirm']);
        Route::put('/{id}/cancel', [PassengerController::class, 'cancel']);
    });

    Route::get('/payments', [PaymentController::class, 'index']);
    Route::prefix('/payment')->group(function () {
        Route::post('/', [PaymentController::class, 'store']);
        Route::post('/checkout', [PaymentController::class, 'checkout']);
    });
});


Route::prefix('/status-board')->group(function () {
    Route::get('/vehicles-available', [StatusBoardController::class, 'vehiclesAvailable']);
    Route::get('/ongoing-vehicles', [StatusBoardController::class, 'ongoingVehicles']);
    Route::get('/next-trip', [StatusBoardController::class, 'nextTrip']);
    Route::get('/awaiting-vehicles', [StatusBoardController::class, 'awaitingVehicles']);
    Route::get('/bookings-with-passengers', [StatusBoardController::class, 'bookingsWithPassengers']);
});

Route::get('/test', function(PaymongoService $paymongoService) {
    return $paymongoService->createCheckoutSession(request()->query('payment_method', 'gcash'), request()->query('description', 'Calinan - Terminal 1A'), request()->query('amount', 20.50));
});