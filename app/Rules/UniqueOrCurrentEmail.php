<?php

namespace App\Rules;

use App\Models\User;
use Illuminate\Contracts\Validation\Rule;

class UniqueOrCurrentEmail implements Rule
{
    public function passes($attribute, $value)
    {
        $userId = request()->route('user')['id'];

        return ! User::where('email', $value)
            ->whereNotIn('id', [$userId])
            ->exists();
    }

    public function message()
    {
        return 'This :attribute has already been taken';
    }
}
