<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Rules\ImageOrPath;
use App\Rules\UniqueOrCurrentEmail;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function rules(): array
    {
        /** @var User $authenticatedUser */
        $authenticatedUser = auth()->user();

        /** @var User $user */
        $user = $this->route('user');

        if ($authenticatedUser->id === $user->id) {
            return [
                'password' => [
                    'min:6',
                    'max:255',
                ],
                'email' => [
                    'required',
                    'email',
                    'max:255',
                    new UniqueOrCurrentEmail,
                ],
                'profile_image' => [
                    new ImageOrPath,
                ],
            ];
        }

        $rules = [
            'points' => [
                'nullable',
                'integer',
                'min:1',
                'max:999999',
            ],
            'role' => ['required'],
        ];

        if ($authenticatedUser->isSuperAdminRole()) {
            $rules['role'][] = Rule::in([User::ROLE_ADMIN, User::ROLE_SUPPLIER, User::ROLE_USER]);
        } else {
            $rules['role'][] = Rule::in([User::ROLE_SUPPLIER, User::ROLE_USER]);
        }

        return $rules;
    }
}
