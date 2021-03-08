<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidUsername implements Rule
{
    public function passes($attribute, $value): bool
    {
        return preg_match('/^\w+$/u', $value) && substr_count($value, '_') <= 1;
    }

    public function message()
    {
        return 'Usernames may only contain letters, numbers, and at most one underscore.';
    }
}
