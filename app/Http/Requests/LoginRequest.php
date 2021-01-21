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
        ];
    }

    public function messages(): array
    {
        return [
            'username.regex' => 'Usernames may only contain letters and numbers.',
        ];
    }
}
