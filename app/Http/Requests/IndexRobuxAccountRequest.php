<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexRobuxAccountRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'user_id' => [
                'int',
                Rule::exists('users', 'id')->where('role', [User::ROLE_SUPPLIER, User::ROLE_SUPER_ADMIN]),
            ],
        ];
    }
}
