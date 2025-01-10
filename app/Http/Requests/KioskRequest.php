<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KioskRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^(\+?\d{1,4}[\s-]?)?\d{10,15}$/'],
            'trip_id' => ['required', 'integer', 'exists:trips,id'],
            'payment_method' => ['required', 'string', 'in:Cash,Card,Online'],
            'amount_to_pay' => ['required', 'numeric', 'min:0.01'],
        ];
    }
}
