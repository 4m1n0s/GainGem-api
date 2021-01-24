<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBitcoinValueRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'bitcoin' => [
                'required',
                'integer',
                'min:1',
                'max:10000',
            ],
        ];
    }
}
