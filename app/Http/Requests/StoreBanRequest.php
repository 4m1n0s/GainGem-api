<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBanRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'ban_reason' => [
                'required',
                'string',
                'max:50',
            ],
        ];
    }
}
