<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRobuxGroupRequest extends FormRequest
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
            'robux_group_id' => [
                'required',
                'integer',
                'min:1',
                'max:9999999999',
                Rule::unique('robux_groups')->whereNull('deleted_at'),
            ],
        ];
    }
}
