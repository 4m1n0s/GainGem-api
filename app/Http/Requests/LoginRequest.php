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
}
