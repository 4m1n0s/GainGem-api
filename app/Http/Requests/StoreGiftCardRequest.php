<?php

namespace App\Http\Requests;

use App\Models\GiftCard;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGiftCardRequest extends FormRequest
{
    public function rules(): array
    {
        $file = file_get_contents(base_path()."\\vendor\samayo\country-json\src\country-by-name.json");
        $countries = array_column(json_decode((string) $file, true), 'country');

        return [
            'code' => [
                'required',
                'min:2',
                'max:255',
                Rule::unique('gift_cards')->where('provider', $this->input('provider')),
            ],
            'country' => [
                'nullable',
                Rule::in($countries),
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
}
