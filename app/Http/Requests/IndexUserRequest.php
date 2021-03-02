<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'username' => [
                'string',
            ],
            'filter' => [
                'in:email_verified_at,banned_at,froze_at',
            ],
            'filter_direction' => [
                'string',
                'in:ASC,DESC',
            ],
        ];
    }
}
