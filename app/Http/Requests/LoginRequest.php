<?php

namespace App\Http\Requests;

use App\Rules\ValidUsername;
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
                new ValidUsername,
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
}
