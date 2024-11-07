<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TripRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'driver_id' => 'required|exists:users,id',
            'fare_amount' => 'required|numeric',
            'from_terminal_id' => 'required|exists:terminals,id',
            'to_terminal_id' => 'required|exists:terminals,id',
            'passenger_capacity' => 'required|integer',
            'trip_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'status' => 'required|string',
            'vehicle_id' => 'required|exists:vehicles,id',
        ];
    }
}
