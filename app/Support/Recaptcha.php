<?php

namespace App\Support;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Log;

final class Recaptcha
{
    public static function challengeRequired(): bool
    {
        self::reportProductionMisconfiguration();

        return self::hasConfiguredKey();
    }

    public static function secretKey(): ?string
    {
        return self::filledSetting('recaptcha_secret_key');
    }

    public static function hasConfiguredKey(): bool
    {
        return self::filledSetting('recaptcha_site_key') !== null
            || self::secretKey() !== null;
    }

    public static function reportProductionMisconfiguration(): void
    {
        if (! app()->isProduction()) {
            return;
        }

        $missingKeys = [];

        if (self::filledSetting('recaptcha_site_key') === null) {
            $missingKeys[] = 'recaptcha_site_key';
        }

        if (self::secretKey() === null) {
            $missingKeys[] = 'recaptcha_secret_key';
        }

        if ($missingKeys !== []) {
            Log::warning('Production reCAPTCHA is not fully configured.', [
                'missing_keys' => $missingKeys,
            ]);
        }
    }

    private static function filledSetting(string $key): ?string
    {
        $value = SiteSetting::getValue($key);

        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        return trim($value);
    }
}
