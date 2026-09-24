<?php

namespace App\Rules;

use App\Services\MathCaptchaService;
use Illuminate\Contracts\Validation\Rule;

class ValidMathCaptcha implements Rule
{
    public function passes($attribute, $value): bool
    {
        /** @var MathCaptchaService $captcha */
        $captcha = app(MathCaptchaService::class);

        return $captcha->verify((string) $value);
    }

    public function message(): string
    {
        return 'The captcha answer is incorrect or has expired.';
    }
}
