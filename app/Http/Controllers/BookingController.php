<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookingRequest;
use App\Models\Booking;
use App\Models\Passenger;
use App\Models\Payment;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $pageSize = $request->input('page_size');
        $filter = $request->input('filter');
        $sortColumn = $request->input('sort_column', 'booked_at');
        $sortDesc = $request->input('sort_desc', false) ? 'desc' : 'asc';

        $query = Booking::with(['user', 'trip.driver']);

        if ($filter) {
            $query->where(function ($q) use ($filter) {
                $q->where('booked_at', 'like', "%{$filter}%")
                    ->orWhere('status', 'like', "%{$filter}%")
                    ->orWhere('paid', 'like', "%{$filter}%");
            });
        }

        $query->orderBy('id', 'desc');

        if (in_array($sortColumn, ['booked_at', 'status', 'paid'])) {
            $query->orderBy($sortColumn, $sortDesc);
        }

        if ($pageSize) {
            $bookings = $query->paginate($pageSize);
        } else {
            $bookings = $query->get();
        }

        return response()->json([
            'status' => 'success',
            'message' => __('messages.success.fetched'),
            'data' => $bookings,
        ]);
    }

    public function store(BookingRequest $request)
    {
        $validated = $request->validated();

        $existingBooking = Booking::where('trip_id', $validated['trip_id'])
            ->first();

        if ($existingBooking) {
            return response()->json([
                'status' => false,
                'message' => 'There is already an approved and paid booking for this trip.',
            ]);
        }

        $booking = Booking::create($validated);

        $paymentMethod = $request->input('payment_method');
        if (in_array($paymentMethod, ['gcash', 'paymaya'])) {
            $booking->update([
                'paid' => true,
                'status' => 'approved',
            ]);
        }

        $referenceNo = 'REF' . strtoupper(Str::random(5)) . $booking->id;

        Payment::create([
            'user_id' => $request->input('user_id'),
            'booking_id' => $booking->id,
            'payment_method' => $paymentMethod,
            'amount' => $request->input('total_amount'),
            'reference_no' => $referenceNo,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => __('messages.success.created'),
            'booking' => $booking,
        ]);
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

    public function update(BookingRequest $request, string $id)
    {
        $booking = Booking::findOrFail($id);

        $booking->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => __('messages.success.updated'),
            'booking' => $booking,
        ], 200);
    }

    public function destroy(string $id)
    {
        $booking = Booking::findOrFail($id);
        $booking->delete();

        return response()->json([
            'status' => 'success',
            'message' => __('messages.success.deleted'),
            'booking' => $booking,
        ]);
    }

    public function currentBookingForUser($userId)
    {
        $booking = Booking::with(['trip.terminalFrom', 'trip.terminalTo', 'user'])
            ->where('user_id', $userId)
            ->where('status', 'approved')
            ->where('paid', true)
            ->whereNull('drop_at')
            ->whereHas('trip', function ($query) {
                $query->where('status', 'in_progress');
            })
            ->first();

        if (!$booking) {
            return response()->json([
                'status' => false,
                'message' => 'No approved and paid booking found for the user, or the trip is completed.',
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Current booking retrieved successfully for the user.',
            'data' => $booking,
        ], 200);
    }

    public function dropOffPassenger(Request $request, $id)
    {
        $requestData = $request->all();

        $request->validate([
            'drop_at' => ['required', 'array', 'size:2'],
            'drop_at.*' => ['numeric'],
        ]);

        $longitude = $request->input('drop_at.0');
        $latitude = $request->input('drop_at.1');

        $booking = Booking::findOrFail($id);

        $booking->drop_at = "{$longitude},{$latitude}";
        $booking->dropped_at = now();

        $booking->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Passenger dropped off successfully.',
            'booking' => $booking,
        ]);
    }

    public function markAsPaid(Request $request, string $id)
    {
        $validated = $request->validate([
            'paid' => 'required|boolean',
        ]);

        $booking = Booking::with(['user', 'trip'])->findOrFail($id);

        $referenceNo = 'REF' . strtoupper(Str::random(5)) . $booking->id;

        $fareAmount = $booking->trip->fare_amount;
        $userClassification = $booking->user->classification;

        $discount = 0;

        if (in_array($userClassification, ['student', 'pwd', 'senior_citizen'])) {
            $discount = $fareAmount * 0.20;
        }

        $amountAfterDiscount = $fareAmount - $discount;

        Payment::create([
            'user_id' => $booking->user->id,
            'booking_id' => $booking->id,
            'payment_method' => 'cash',
            'amount' => $amountAfterDiscount,
            'reference_no' => $referenceNo,
        ]);

        $booking->update([
            'paid' => $validated['paid'],
            'status' => 'approved',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => __('Payment successfully recorded and booking updated to paid'),
            'data' => $booking,
        ]);
    }


    public function updateBookingStatus(Request $request, string $id)
    {
        $validated = $request->validate([
            'status' => 'required|string',
            'paid' => 'required|boolean',
        ]);

        $booking = Booking::findOrFail($id);

        $booking->update([
            'status' => $validated['status'],
            'paid' => $validated['paid'],
        ]);

        $this->createPaymentIfRequired($booking, $validated);

        return response()->json([
            'status' => 'success',
            'message' => __('messages.success.updated'),
            'booking' => $booking,
        ], 200);
    }

    public function createPaymentIfRequired(Booking $booking, array $validated)
    {
        if ($booking->status !== 'approved' && $validated['status'] === 'approved' && $validated['paid'] === true) {
            DB::transaction(function () use ($booking, $validated) {
                Payment::create([
                    'user_id' => $booking->user_id,
                    'booking_id' => $booking->id,
                    'payment_method' => $validated['payment_method'] ?? 'cash',
                    'amount' => $validated['amount'],
                    'reference_no' => $validated['reference_no'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });
        }
    }

    public function listBookingsByUser($userId)
    {
        $bookings = Booking::with(['trip.terminalFrom', 'trip.terminalTo', 'trip.driver.vehicle', 'user'])
            ->where('user_id', $userId)
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Bookings retrieved successfully.',
            'data' => $bookings,
        ]);
    }
}
