<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class Encrypt implements CastsAttributes
{
    public function get($model, $key, $value, $attributes): string
    {
        return decrypt($value);
    }

    public function set($model, $key, $value, $attributes): string
    {
        return encrypt($value);
    }
}
