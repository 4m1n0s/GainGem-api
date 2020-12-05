<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ImageOrPath implements Rule
{
    public function passes($attribute, $value): bool
    {
        $isImage = Validator::make(['value' => $value], ['value' => 'image'])->passes();

        if ($isImage) {
            return true;
        }

        if (! is_string($value)) {
            return false;
        }

        return Storage::exists(explode('storage/', $value)[1]);
    }

    public function message(): string
    {
        return 'The :attribute is invalid image';
    }
}
