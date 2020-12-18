<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBitcoinRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'wallet' => [
                'required',
                'string',
                'min:2',
            ],
            'stock_amount' => [
                'required',
                'integer',
                'min:1',
            ],
        ];
    }
}
