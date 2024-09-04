<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RouteRequest extends FormRequest
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
            'driver_id' => 'required|exists:users,id',
            'start_location' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'passenger_capacity' => 'required|integer|min:1',
            'start_time' => 'required|date',
            'estimated_arrival_time' => 'nullable|date',
        ];
    }
}
