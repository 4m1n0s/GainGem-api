<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ImageOrPath implements Rule
{
    private string $message;

    public function __construct()
    {
        $this->message = 'The :attribute is invalid image';
    }

    public function passes($attribute, $value): bool
    {
        $isImage = Validator::make(['value' => $value], ['value' => 'image'])->passes();

        if ($isImage) {
            $isSize = Validator::make(['value' => $value], ['size' => '5120'])->passes();

            if (! $isSize) {
                $this->message = 'The :attribute must be smaller than 5MB.';

                return false;
            }

            return true;
        }

        if (! is_string($value)) {
            return false;
        }

        return Storage::exists(explode('storage/', $value)[1]);
    }

    public function message(): string
    {
        return $this->message;
    }
}
