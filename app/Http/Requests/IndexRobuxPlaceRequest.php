<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexRobuxPlaceRequest extends FormRequest
{
    public function rules()
    {
        return [
            'value' => [
                'required',
                'integer',
                'min:1',
                'max:5000',
            ],
            'username' => [
                'required',
                'string',
                'min:2',
                'max:255',
            ],
        ];
    }
}
