<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RedeemCouponRequest extends FormRequest
{
    public function rules()
    {
        return [
            'code' => [
                'required',
                'exists:coupons',
                'max:255',
            ],
        ];
    }
}
