<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBitcoinRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'guid' => [
                'required',
                'string',
                'min:2',
            ],
            'password' => [
                'required',
                'string',
                'min:2',
                'max:255',
            ],
            'stock_amount' => [
                'required',
                'integer',
                'min:0',
                'max:999999',
            ],
        ];
    }
}
