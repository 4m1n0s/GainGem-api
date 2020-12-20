<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ForgotPasswordRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email',
                'exists:users',
                'max:255',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'email.exists' => "Email doesn't exist in the system",
        ];
    }
}
