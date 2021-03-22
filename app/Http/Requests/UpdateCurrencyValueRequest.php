<?php

namespace App\Http\Requests;

use App\Models\GiftCard;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCurrencyValueRequest extends FormRequest
{
    public function rules(): array
    {
        $rules = [];

        foreach (GiftCard::PROVIDERS as $provider) {
            $rules[$provider] = [
                'required',
                'integer',
                'min:1',
                'max:255',
            ];
        }

        return $rules;
    }
}
