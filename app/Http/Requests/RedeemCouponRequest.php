<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RedeemCouponRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'code' => [
                'required',
                'exists:coupons',
                'max:255',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'You must enter a promo code!',
            'code.exists' => 'Invalid promo code!',
        ];
    }
}
