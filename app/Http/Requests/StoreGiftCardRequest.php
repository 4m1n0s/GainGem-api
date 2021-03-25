<?php

namespace App\Http\Requests;

use App\Models\GiftCard;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGiftCardRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'codes' => [
                'required',
                'array',
            ],
            'codes.*.code' => [
                'required',
                'string',
                'min:2',
                'max:255',
                Rule::unique('gift_cards')->where('provider', $this->input('provider')),
            ],
            'country' => [
                'nullable',
                'present',
                Rule::in(array_merge(get_continents(), get_countries())),
            ],
            'currency_id' => [
                'required',
                'exists:currencies,id',
                'exists:currency_values',
            ],
            'provider' => [
                'required',
                Rule::in(GiftCard::PROVIDERS),
            ],
            'value' => [
                'required',
                'integer',
                'min:1',
                'max:5000',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'currency_id.exists' => 'The selected currency is invalid or has no values.',
            'country.present' => 'The :attribute field is required.',
        ];
    }

    public function attributes(): array
    {
        return [
            'country' => 'region',
        ];
    }
}
