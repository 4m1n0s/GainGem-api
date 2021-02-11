<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Rules\ImageOrPath;
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
                    'unique:users,email,'.$user->id,
                    'email',
                    'max:255',
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

        $roles = [User::ROLE_SPONSOR, User::ROLE_SUPPLIER, User::ROLE_USER];

        if ($authenticatedUser->isSuperAdminRole()) {
            $roles[] = User::ROLE_ADMIN;
        }

        $rules['role'][] = Rule::in($roles);

        return $rules;
    }
}
