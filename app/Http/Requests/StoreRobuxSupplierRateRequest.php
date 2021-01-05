<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRobuxSupplierRateRequest extends FormRequest
{
    public function rules()
    {
        return [
            'rate' => [
                'integer',
                'min:1',
                'max:100',
            ],
        ];
    }
}
