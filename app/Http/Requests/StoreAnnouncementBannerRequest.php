<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnnouncementBannerRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'is_disabled' => [
                'required',
                'boolean',
            ],
            'text' => [
                'required',
                'min:6',
                'max:255',
            ],
        ];
    }
}
