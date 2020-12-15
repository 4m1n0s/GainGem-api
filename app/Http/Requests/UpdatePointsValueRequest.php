<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePointsValueRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'points' => [
                'required',
                'integer',
                'min:1',
                'max:10000',
            ],
        ];
    }
}
