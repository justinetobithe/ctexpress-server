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
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'fare_amount' => 'required|numeric',
            'from_terminal_id' => 'required|exists:terminals,id',
            'to_terminal_id' => 'required|exists:terminals,id',
            'passenger_capacity' => 'required|integer',
            'trip_date' => 'required|date',
            'start_time' => 'required|regex:/^\d{2}:\d{2}(:\d{2})?$/',
            'status' => 'required|string',
            'driver_id' => 'required|exists:users,id',
        ];
    }
}
