<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookingRequest;
use App\Models\Booking;
use Illuminate\Http\Request;

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
        return response()->json([
            'status' => true,
            'message' => 'Booking created successfully',
            'data' => $booking,
        ], 201);
    }

    public function show(Booking $booking)
    {
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
}
