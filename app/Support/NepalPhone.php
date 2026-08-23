<?php

namespace App\Support;

class NepalPhone
{
    public const CANONICAL_REGEX = '/^(?:(?:97|98)\d{8}|0(?:1\d{7}|[2-9]\d{7,8}))$/';

    public const BROWSER_PATTERN = '[+0-9() .-]{9,20}';

    public static function normalize(mixed $phone): string
    {
        $value = trim((string) $phone);

        if ($value === '' || preg_match('/^\+?[0-9() .-]+$/', $value) !== 1) {
            return $value;
        }

        $digits = preg_replace('/\D+/', '', $value) ?? '';

        if (str_starts_with($digits, '977')) {
            $digits = substr($digits, 3);

            if (! str_starts_with($digits, '97') && ! str_starts_with($digits, '98')) {
                $digits = '0'.$digits;
            }
        }

        return $digits;
    }

    public static function isPlausible(string $phone): bool
    {
        if (preg_match(self::CANONICAL_REGEX, $phone) !== 1) {
            return false;
        }

        return preg_match('/^(\d)\1+$/', $phone) !== 1
            && ! in_array($phone, ['9800000000', '9812345678', '1234567890', '0123456789'], true);
    }
}
