<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCouponRequest extends FormRequest
{
    public function rules(): array
    {
        $coupon = $this->route('coupon');

        return [
            'code' => [
                'required',
                'min:2',
                'max:20',
                Rule::unique('coupons')->ignore($coupon->id),
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
            'expires_at.after' => 'The date should be by tomorrow',
        ];
    }
}
