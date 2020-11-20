<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsInboundAttributes;

class Bcrypt implements CastsInboundAttributes
{
    public function set($model, $key, $value, $attributes): string
    {
        return bcrypt($value);
    }
}
