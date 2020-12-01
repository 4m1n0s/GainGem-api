<?php

namespace App\Http\Requests;

use App\Models\CompletedTask;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DailyTasksStoreRequest extends FormRequest
{
    public function rules()
    {
        return [
            'offers_count' => [
                'required',
                'integer',
                Rule::in(array_keys(CompletedTask::TASKS)),
            ],
        ];
    }

    public function messages()
    {
        return [
            'offers_count.required' => 'Invalid task!',
            'offers_count.integer' => 'Invalid task!',
            'offers_count.in' => 'Invalid task!',
        ];
    }
}
