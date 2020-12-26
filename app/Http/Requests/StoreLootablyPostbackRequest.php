<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
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
                'in:'.hash('sha256', $this->input('user_id').$this->input('user_ip').$this->input('payout').$this->input('payout') * $pointsValue.'WNveOaxj5FTpNJhM5IL4QZIhtmHTkiQiMCaeD4zg36BjtVeKAxrC5NK0kKT6dGoCyQ1qeND0PRBQD4HPX51ZLA'),
            ],
        ];
    }
}
