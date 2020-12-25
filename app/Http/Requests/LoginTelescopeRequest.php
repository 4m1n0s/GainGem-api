<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginTelescopeRequest extends FormRequest
{
    public function rules()
    {
        return [
            'token' => [
                'required',
                'string',
            ],
        ];
    }
}
