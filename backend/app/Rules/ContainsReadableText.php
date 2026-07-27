<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class ContainsReadableText implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            return;
        }

        $plainText = html_entity_decode(
            strip_tags($value),
            ENT_QUOTES | ENT_HTML5,
            'UTF-8',
        );

        $normalizedText = preg_replace(
            '/[\s\x{00A0}]+/u',
            '',
            $plainText,
        );

        if (
            $normalizedText === null
            || $normalizedText === ''
        ) {
            $fail(
                'The :attribute field must contain readable text.',
            );
        }
    }
}
