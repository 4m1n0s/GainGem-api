<?php

namespace App\Rules;

use App\Services\Bitcoin;
use Illuminate\Contracts\Validation\Rule;

class BitcoinAddress implements Rule
{
    public function passes($attribute, $value): bool
    {
        return Bitcoin::isAddressValid($value);
    }

    public function message(): string
    {
        return 'The address is invalid';
    }
}
