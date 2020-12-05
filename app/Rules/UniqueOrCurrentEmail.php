<?php

namespace App\Rules;

use App\Models\User;
use Illuminate\Contracts\Validation\Rule;

class UniqueOrCurrentEmail implements Rule
{
    public function passes($attribute, $value): bool
    {
        /** @var User $user */
        $user = request()->route('user');

        return ! User::where('email', $value)
            ->whereNotIn('id', [$user->id])
            ->exists();
    }

    public function message(): string
    {
        return 'This :attribute has already been taken';
    }
}
