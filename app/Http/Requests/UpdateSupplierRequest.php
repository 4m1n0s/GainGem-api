<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSupplierRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'robux_rate' => [
                'nullable',
                'numeric',
                'min:1',
                'max:100',
            ],
        ];
    }
}
