<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePostbackRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'app' => [
                'required',
                'string',
                'min:1',
                'max:255',
            ],
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
                Rule::unique('completed_tasks', 'data->transaction_id')->where('provider', $this->get('app')),
            ],
        ];
    }
}
