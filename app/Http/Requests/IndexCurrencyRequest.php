<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexCurrencyRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'no_pagination' => [
                'boolean',
            ],
        ];
    }
}
