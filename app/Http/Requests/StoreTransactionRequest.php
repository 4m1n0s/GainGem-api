<?php

namespace App\Http\Requests;

use App\Models\GiftCard;
use App\Models\Transaction;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTransactionRequest extends FormRequest
{
    public function rules(): array
    {
        $isGiftCard = ! in_array($this->input('provider'), [Transaction::TYPE_ROBUX, Transaction::TYPE_BITCOIN]);

        $rules = [
            'country' => [
                'nullable',
                Rule::in(get_countries()),
            ],
            'value' => [
                'required',
                'integer',
                'min:1',
                'max:999999',
            ],
            'provider' => [
                'required',
            ],
            'destination' => [
                'string',
                'min:2',
                'max:255',
                Rule::requiredIf(! $isGiftCard),
            ],
        ];

        if (! $isGiftCard) {
            return $rules;
        }

        $rules['provider'][] = Rule::in(GiftCard::PROVIDERS);

        $rules['destination'][] = 'nullable';

        return $rules;
    }
}
