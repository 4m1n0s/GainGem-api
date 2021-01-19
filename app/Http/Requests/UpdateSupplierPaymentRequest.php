<?php

namespace App\Http\Requests;

use App\Models\SupplierPayment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSupplierPaymentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'status' => [
                'required',
                'string',
                Rule::in([SupplierPayment::STATUS_PAID, SupplierPayment::STATUS_PENDING, SupplierPayment::STATUS_DENIED]),
            ],
            'denial_reason' => [
                'nullable',
                'string',
                'min:1',
                'max:255',
                'required_if:status,'.SupplierPayment::STATUS_DENIED,
            ],
        ];
    }
}
