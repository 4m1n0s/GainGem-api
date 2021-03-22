<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCurrencyRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'currency' => [
                'required',
                'string',
                'size:3',
                'unique:currencies',
            ],
            'name' => [
                'required',
                'string',
                'min:2',
                'max:20',
            ],
            'symbol' => [
                'required',
                'string',
                'min:1',
                'max:4',
            ],
        ];
    }
}
