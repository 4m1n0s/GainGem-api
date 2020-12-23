<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCouponRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'code' => [
                'required',
                'unique:coupons',
                'min:2',
                'max:20',
            ],
            'points' => [
                'required',
                'integer',
                'min:1',
                'max:999999',
            ],
            'max_usages' => [
                'required',
                'integer',
                'min:0',
                'max:999999',
            ],
            'expires_at' => [
                'required',
                'date',
                'date_format:Y-m-d',
                'after:'.date('Y-m-d'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'expires_at.date' => 'Invalid date!',
            'expires_at.date_format' => 'The format is invalid!',
            'expires_at.after' => 'The date should be tomorrow or later',
        ];
    }
}
