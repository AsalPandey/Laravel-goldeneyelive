<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class ApprovedMapEmbedUrl implements ValidationRule
{
    public static function isValid(mixed $value): bool
    {
        if (! is_string($value) || trim($value) === '' || filter_var($value, FILTER_VALIDATE_URL) === false) {
            return false;
        }

        $parts = parse_url(trim($value));
        $host = strtolower((string) ($parts['host'] ?? ''));
        $path = (string) ($parts['path'] ?? '');

        return strtolower((string) ($parts['scheme'] ?? '')) === 'https'
            && in_array($host, ['www.google.com', 'maps.google.com'], true)
            && (str_starts_with($path, '/maps/embed') || str_starts_with($path, '/maps/d/embed'));
    }

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! self::isValid($value)) {
            $fail('The :attribute must be an HTTPS Google Maps embed URL.');
        }
    }
}
