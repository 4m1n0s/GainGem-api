<?php

namespace App\Http\Requests;

use App\Rules\ImageOrPath;
use App\Rules\UniqueOrCurrentEmail;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'password' => [
                'min:6',
                'max:255',
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                new UniqueOrCurrentEmail,
            ],
            'profile_image' => [
                new ImageOrPath,
            ],
        ];
    }
}
