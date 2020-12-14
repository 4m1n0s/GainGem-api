<?php

namespace App\Http\Requests;

use App\Models\Coupon;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCouponRequest extends FormRequest
{
    public function rules(): array
    {
        /** @var Coupon $coupon */
        $coupon = $this->route('coupon');

        return [
            'code' => [
                'required',
                'unique:coupons,code,'.$coupon->id,
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
                'after:'.now()->subDay()->format('Y-m-d'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'expires_at.date' => 'Invalid date!',
            'expires_at.date_format' => 'The format is invalid!',
            'expires_at.after' => 'The date should be today or later',
        ];
    }
}
