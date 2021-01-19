<?php

namespace App\Http\Requests;

use App\Models\SupplierPayment;
use App\Rules\BitcoinAddress;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSupplierPaymentRequest extends FormRequest
{
    public function rules(): array
    {
        $rules = [
            'method' => [
                'required',
                'string',
                Rule::in([SupplierPayment::METHOD_PAYPAL, SupplierPayment::METHOD_BITCOIN]),
            ],
            'destination' => [
                'required',
                'string',
                'min:2',
                'max:255',
            ],
            'value' => [
                'required',
                'integer',
                'min:5',
                'max:10000',
            ],
        ];

        if ($this->input('method') === SupplierPayment::METHOD_BITCOIN) {
            $rules['destination'][] = new BitcoinAddress;

            return $rules;
        }

        $rules['destination'][] = 'email';

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'destination' => $this->input('method') === SupplierPayment::METHOD_BITCOIN ? 'address' : 'email',
            'value' => 'amount',
        ];
    }
}
