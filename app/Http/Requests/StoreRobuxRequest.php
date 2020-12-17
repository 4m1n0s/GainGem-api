<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRobuxRequest extends FormRequest
{
    public function rules()
    {
        return [
            'cookie' => [
                'required',
                'string',
                'min:2',
                'max:255',
            ],
            'group_id' => [
                'required',
                'integer',
                'min:1',
            ],
        ];
    }
}
