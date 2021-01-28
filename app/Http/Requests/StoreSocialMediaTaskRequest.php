<?php

namespace App\Http\Requests;

use App\Models\CompletedTask;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSocialMediaTaskRequest extends FormRequest
{
    public function rules()
    {
        return [
            'social_media' => [
                'required',
                'string',
                Rule::in(array_keys(CompletedTask::SOCIAL_MEDIA_TASK_OFFERS_OPTIONS)),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'social_media.required' => 'Invalid social media!',
            'social_media.string' => 'Invalid social media!',
            'social_media.in' => 'Invalid social media!',
        ];
    }
}
