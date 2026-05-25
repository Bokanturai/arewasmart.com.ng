<?php

namespace App\Http\Requests\Action;

use Illuminate\Foundation\Http\FormRequest;

class BuyAirtimeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization is handled by the auth middleware and controller
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'network'   => ['required', 'string', 'in:mtn,airtel,glo,etisalat,9mobile'],
            'mobileno'  => ['required', 'string', 'regex:/^[0-9]{11}$/'],
            'amount'    => ['required', 'numeric', 'min:50', 'max:5000'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'mobileno.regex' => 'The mobile number must be exactly 11 digits.',
            'network.in'     => 'Invalid network selected.',
        ];
    }
}
