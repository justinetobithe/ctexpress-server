<?php

namespace App\Http\Controllers;

use App\Http\Requests\TripRequest;
use App\Models\TripRating;
use Illuminate\Http\Request;

class TripRatingController extends Controller
{
    public function store(TripRequest $request)
    {
        $validated = $request->validated();

        $tripRating = TripRating::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => __('messages.success.fetched'),
            'data' => $tripRating,
        ]);
    }
}
