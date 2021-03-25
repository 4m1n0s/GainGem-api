<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRobuxAccountRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'cookie' => [
                'required',
                'string',
                'min:2',
                'max:1000',
            ],
            'robux_account_id' => [
                'required',
                'integer',
                'min:1',
                'max:9999999999',
                Rule::unique('robux_accounts')->whereNull('deleted_at'),
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'robux_account_id' => 'account',
        ];
    }
}
