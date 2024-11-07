<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookingRequest;
use App\Models\Booking;
use App\Models\Passenger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::all();
        return response()->json([
            'status' => true,
            'message' => 'Bookings retrieved successfully',
            'data' => $bookings,
        ], 200);
    }

    public function store(BookingRequest $request)
    {
        $booking = Booking::create($request->validated());

        Passenger::create([
            'booking_id' => $booking->id,
            'trip_id' => $booking->trip_id,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Booking created successfully',
            'data' => $booking,
        ], 201);
    }

    public function show($id)
    {
        $booking = Booking::with(['route.terminalFrom', 'route.terminalTo', 'user'])->where('id', $id)->first();

        return response()->json([
            'status' => true,
            'message' => 'Booking retrieved successfully',
            'data' => $booking,
        ], 200);
    }

    public function update(BookingRequest $request, Booking $booking)
    {
        $booking->update($request->validated());
        return response()->json([
            'status' => true,
            'message' => 'Booking updated successfully',
            'data' => $booking,
        ], 200);
    }

    public function destroy(Booking $booking)
    {
        $booking->delete();
        return response()->json([
            'status' => true,
            'message' => 'Booking deleted successfully',
        ], 200);
    }

    public function currentBookingForUser($userId)
    {
        $booking = Booking::with(['route.terminalFrom', 'route.terminalTo', 'user'])
            ->where('user_id', $userId)
            ->where('status', 'approved')
            ->where('paid', true)
            ->whereNull('drop_at')
            ->first();

        if (!$booking) {
            return response()->json([
                'status' => false,
                'message' => 'No approved and paid booking found for the user.',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Current booking retrieved successfully for the user.',
            'data' => $booking,
        ], 200);
    }

    public function dropOffPassenger(Request $request, $id)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $booking = Booking::findOrFail($id);

        $booking->drop_at = DB::raw("POINT({$request->longitude}, {$request->latitude})");
        $booking->dropped_at = now();

        $booking->save();

        return response()->json([
            'message' => 'Passenger dropped off successfully.',
            'booking' => $booking,
        ]);
    }
}
