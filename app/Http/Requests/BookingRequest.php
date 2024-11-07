<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookingRequest extends FormRequest
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
            'user_id'   => 'required|exists:users,id',
            'trip_id'  => 'required|exists:routes,id',
            'booked_at' =>  'required|date',
            'status'    => 'required|string',
            'drop_at'   => 'nullable|point',
            'paid' => 'required|boolean'
        ];
    }
}
