<?php

namespace GrantHolle\Altcha\Rules;

use Closure;
use GrantHolle\Altcha\Altcha;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidAltcha implements ValidationRule
{
    protected Altcha $altcha;

    public function __construct()
    {
        $this->altcha = app(Altcha::class);
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (app()->environment('testing') && $value === 'valid') {
            return;
        }

        if (! $this->altcha->verifySolution($value)) {
            $fail('Invalid captcha.');
        }
    }
}
