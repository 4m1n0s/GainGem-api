<?php

namespace App\Http\Requests;

use App\Models\GiftCard;
use App\Models\Transaction;
use App\Rules\BitcoinAddress;
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
                'max:5000',
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
            if ($this->input('provider') === Transaction::TYPE_BITCOIN) {
                $rules['destination'][] = new BitcoinAddress;
            } else {
                $rules['group_id'] = [
                    'integer',
                ];
            }

            return $rules;
        }

        $rules['provider'][] = Rule::in(GiftCard::PROVIDERS);

        $rules['destination'][] = 'nullable';

        return $rules;
    }

    public function messages(): array
    {
        $isTypeRobux = $this->input('provider') === Transaction::TYPE_ROBUX;
        $isTypeBitcoin = $this->input('provider') === Transaction::TYPE_BITCOIN;

        if ($isTypeRobux) {
            return [
                'destination.string' => 'You must enter a username!',
                'destination.required' => 'You must enter a username!',
                'destination.min' => 'The username must be at least 2 characters.',
                'destination.max' => 'The username may not be greater than 255 characters.',
            ];
        }

        if ($isTypeBitcoin) {
            return [
                'destination.string' => 'You must enter a wallet!',
                'destination.required' => 'You must enter a wallet!',
                'destination.min' => 'The wallet must be at least 2 characters.',
                'destination.max' => 'The wallet may not be greater than 255 characters.',
            ];
        }

        return [];
    }
}
