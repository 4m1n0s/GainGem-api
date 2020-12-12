<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BanRequest extends FormRequest
{
    public function rules()
    {
        return [
            'ban_reason' => [
                'required',
                'max:50',
            ],
        ];
    }
}
