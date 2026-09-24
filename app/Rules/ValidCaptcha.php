<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidCaptcha implements Rule
{
    public function passes($attribute, $value): bool
    {
        return captcha_check($value);
    }

    public function message(): string
    {
        return 'The captcha answer is incorrect.';
    }
}
