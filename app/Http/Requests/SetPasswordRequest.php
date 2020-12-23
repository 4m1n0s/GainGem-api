<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SetPasswordRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'token' => [
                'required',
                'exists:url_tokens',
            ],
            'password' => [
                'required',
                'min:6',
                'max:255',
            ],
        ];
    }
}
