<?php

namespace GrantHolle\Altcha\Rules;

use Closure;
use GrantHolle\Altcha\Altcha;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidAltcha implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! app(Altcha::class)->validPayload($value)) {
            $fail('Invalid captcha.');
        }
    }
}
