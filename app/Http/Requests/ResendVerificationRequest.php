<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResendVerificationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'exists:users',
                'email',
                'max:255',
            ],
        ];
    }
}
