<?php

namespace App\Support;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

final class Recaptcha
{
    public const Disabled = 'disabled';

    public const Enabled = 'enabled';

    public const Misconfigured = 'misconfigured';

    public static function challengeRequired(): bool
    {
        self::reportMisconfiguration();

        return self::status() === self::Enabled;
    }

    public static function siteKey(): ?string
    {
        return self::filledSetting('recaptcha_site_key');
    }

    public static function secretKey(): ?string
    {
        return self::filledSetting('recaptcha_secret_key');
    }

    public static function status(): string
    {
        $hasSiteKey = self::siteKey() !== null;
        $hasSecretKey = self::secretKey() !== null;

        if ($hasSiteKey && $hasSecretKey) {
            return self::Enabled;
        }

        if (! $hasSiteKey && ! $hasSecretKey) {
            return self::Disabled;
        }

        return self::Misconfigured;
    }

    public static function enabled(): bool
    {
        return self::status() === self::Enabled;
    }

    public static function reportMisconfiguration(): void
    {
        if (self::status() !== self::Misconfigured) {
            return;
        }

        Log::warning('reCAPTCHA configuration is incomplete; public challenges are disabled.', [
            'missing_key' => self::siteKey() === null
                ? 'recaptcha_site_key'
                : 'recaptcha_secret_key',
        ]);
    }

    public static function verify(?string $response, ?string $remoteIp = null): bool
    {
        self::reportMisconfiguration();

        if (! self::enabled() || blank($response)) {
            return false;
        }

        try {
            $verificationResponse = Http::asForm()
                ->timeout(5)
                ->connectTimeout(2)
                ->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret' => self::secretKey(),
                    'response' => $response,
                    'remoteip' => $remoteIp,
                ]);

            return $verificationResponse->successful()
                && (bool) data_get($verificationResponse->json(), 'success', false);
        } catch (\Throwable $exception) {
            Log::warning('reCAPTCHA verification request failed.', [
                'exception' => $exception::class,
            ]);

            return false;
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
