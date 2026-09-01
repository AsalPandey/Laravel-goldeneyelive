<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class PublicMediaPath implements ValidationRule
{
    /** @var array<int, string> */
    private const IMAGE_EXTENSIONS = ['gif', 'jpeg', 'jpg', 'png', 'svg', 'webp'];

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail('The :attribute must be a valid Media Vault image path.');

            return;
        }

        $path = ltrim(str_replace('\\', '/', trim($value)), '/');
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        if (
            $path === ''
            || str_contains($path, '..')
            || preg_match('/^[A-Za-z]:/', $path)
            || filter_var($value, FILTER_VALIDATE_URL)
            || ! str_starts_with($path, 'site/img/')
            || ! in_array($extension, self::IMAGE_EXTENSIONS, true)
        ) {
            $fail('The :attribute must be an existing image inside the Media Vault.');

            return;
        }

        $publicRoot = realpath(public_path());
        $assetPath = realpath(public_path($path));

        if (
            $publicRoot === false
            || $assetPath === false
            || ! is_file($assetPath)
            || ! str_starts_with(strtolower($assetPath), strtolower($publicRoot.DIRECTORY_SEPARATOR))
        ) {
            $fail('The selected :attribute does not exist in the Media Vault.');
        }
    }
}
