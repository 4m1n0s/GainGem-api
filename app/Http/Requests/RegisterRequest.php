<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'username' => [
                'required',
                'unique:users',
                'min:6',
                'max:50',
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
            'referred_by' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')->whereNull('banned_at'),
            ],
        ];
    }
}
