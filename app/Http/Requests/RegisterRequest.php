<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'username' => [
                'required',
                'unique:users',
                'min:6',
                'max:20',
            ],
            'password' => [
                'required',
                'min:6',
                'max:255',
            ],
            'email' => [
                'required',
                'unique:users',
                'email',
                'max:255',
            ],
            'referral_token' => [
                'nullable',
                'string',
            ],
        ];
    }
}
