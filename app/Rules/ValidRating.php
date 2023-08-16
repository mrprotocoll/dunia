<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidRating implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        //
        if(is_float($value) && $value >= 1.0 && $value <= 5.0 && fmod($value, 0.1) === 0.0) {
            $fail($this->message($attribute));
        }

    }

    public function message($attribute)
    {
        return "The {$attribute} must be a valid rating between 1.0 and 5.0 with increments of 0.1.";
    }
}
