<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;

class StoreLootablyPostbackRequest extends FormRequest
{
    public function rules(): array
    {
        $pointsValue = (int) Cache::get('points-value');

        if (! $pointsValue) {
            $pointsValue = 40;
        }

        return [
            'payout' => [
                'required',
                'numeric',
                'min:0',
                'not_in:0',
            ],
            'user_id' => [
                'required',
                'exists:users,id',
            ],
            'user_ip' => [
                'required',
                'ip',
            ],
            'offername' => [
                'required',
                'string',
                'min:1',
                'max:255',
            ],
            'transaction_id' => [
                'required',
                Rule::unique('completed_tasks', 'data->transaction_id')->where('provider', 'lootably'),
            ],
            'key' => [
                'required',
                'string',
                'in:'.hash('sha256', $this->input('user_id').$this->input('user_ip').$this->input('payout').$this->input('payout') * $pointsValue),
            ],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
