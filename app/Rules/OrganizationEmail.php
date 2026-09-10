<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class OrganizationEmail implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || strtolower(substr(strrchr($value, '@') ?: '', 1)) !== config('goldeneye.organization_email_domain')) {
            $fail('The :attribute must use @'.config('goldeneye.organization_email_domain').'.');
        }
    }
}
