<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePostbackValueRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'postback' => [
                'required',
                'integer',
                'min:1',
                'max:10000',
            ],
        ];
    }
}
