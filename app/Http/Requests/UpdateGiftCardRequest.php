<?php

namespace App\Http\Requests;

use App\Models\GiftCard;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGiftCardRequest extends FormRequest
{
    public function rules(): array
    {
        /** @var GiftCard $giftCard */
        $giftCard = $this->route('giftCard');

        return [
            'code' => [
                'required',
                'min:2',
                'max:255',
                Rule::unique('gift_cards')->where('provider', $this->input('provider'))->ignore($giftCard->id),
            ],
            'country' => [
                'nullable',
                Rule::in(get_countries()),
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
                'max:999999',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'currency_id.exists' => 'The selected currency is invalid or has no values.',
        ];
    }
}
