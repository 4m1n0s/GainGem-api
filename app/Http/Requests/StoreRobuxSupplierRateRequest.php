<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRobuxSupplierRateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'rate' => [
                'numeric',
                'min:1',
                'max:100',
            ],
        ];
    }
}
