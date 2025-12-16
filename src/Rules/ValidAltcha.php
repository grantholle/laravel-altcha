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
        $bypass = config('altcha.testing_bypass');
        if (app()->environment('testing') && $bypass && $bypass === $value) {
            return;
        }

        if (! $this->altcha->verifySolution($value)) {
            $fail('Invalid captcha.');
        }
    }
}
