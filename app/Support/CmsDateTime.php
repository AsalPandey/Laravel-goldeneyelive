<?php

namespace App\Support;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;

final class CmsDateTime
{
    public const DISPLAY_TIMEZONE = 'Asia/Kathmandu';

    public const STORAGE_TIMEZONE = 'UTC';

    public static function fromStaffInput(?string $value, ?CarbonInterface $currentValue = null): ?CarbonImmutable
    {
        if (blank($value)) {
            return null;
        }

        if ($currentValue !== null && trim((string) $value) === self::forStaffInput($currentValue)) {
            return CarbonImmutable::instance($currentValue)
                ->setTimezone(self::STORAGE_TIMEZONE);
        }

        return CarbonImmutable::parse($value, self::DISPLAY_TIMEZONE)
            ->setTimezone(self::STORAGE_TIMEZONE);
    }

    public static function forStaffInput(?CarbonInterface $value): string
    {
        if ($value === null) {
            return '';
        }

        return CarbonImmutable::instance($value)
            ->setTimezone(self::DISPLAY_TIMEZONE)
            ->format('Y-m-d\TH:i');
    }

    public static function forStaffDisplay(?CarbonInterface $value, string $format = 'M d, Y H:i'): string
    {
        if ($value === null) {
            return '';
        }

        return CarbonImmutable::instance($value)
            ->setTimezone(self::DISPLAY_TIMEZONE)
            ->format($format);
    }
}
