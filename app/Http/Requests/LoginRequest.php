<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'username' => [
                'required',
                'exists:users',
                'regex:/^[a-zA-Z0-9]+$/u',
                'min:6',
                'max:50',
            ],
            'password' => [
                'required',
                'min:6',
                'max:255',
            ],
            'two_factor_code' => [
                'string',
                'nullable',
                'min:6',
                'max:6',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'username.regex' => 'Usernames may only contain letters and numbers.',
        ];
    }
}
