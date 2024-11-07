<?php

namespace App\Http\Controllers;

use App\Models\Passenger;
use Illuminate\Http\Request;

class PassengerController extends Controller
{
    public function confirm($id)
    {
        $passenger = Passenger::find($id);

        if (!$passenger) {
            return response()->json(['message' => 'Passenger not found.'], 404);
        }

        $passenger->status = 'confirmed';
        $passenger->save();

        $booking = $passenger->booking;
        if ($booking) {
            $booking->status = 'approved';
            $booking->paid = true;
            $booking->save();
        }


        return response()->json(['status' => true, 'message' => 'Passenger status confirmed successfully.'], 200);
    }

    public function cancel($id)
    {
        $passenger = Passenger::find($id);

        if (!$passenger) {
            return response()->json(['message' => 'Passenger not found.'], 404);
        }

        $passenger->status = 'canceled';
        $passenger->save();

        $booking = $passenger->booking;
        if ($booking) {
            $booking->status = 'expired';
            $booking->save();
        }

        return response()->json(['status' => true, 'message' => 'Passenger status canceled successfully.'], 200);
    }
}
