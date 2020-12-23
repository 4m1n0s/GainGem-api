<?php

namespace App\Http\Requests;

use App\Models\GiftCard;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexGiftCardRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'provider' => [
                'string',
                Rule::in(GiftCard::PROVIDERS),
            ],
        ];
    }
}
