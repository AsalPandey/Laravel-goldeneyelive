<?php

namespace App\Support;

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

        if (self::bypassed()) {
            return false;
        }

        return self::enabled() || app()->isProduction();
    }

    public static function siteKey(): ?string
    {
        return self::filledConfig('services.recaptcha.site_key');
    }

    public static function secretKey(): ?string
    {
        return self::filledConfig('services.recaptcha.secret_key');
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

    public static function bypassed(): bool
    {
        return (bool) config('services.recaptcha.bypass', false)
            && app()->environment(['local', 'testing']);
    }

    public static function reportMisconfiguration(): void
    {
        if (self::enabled() || self::bypassed()) {
            return;
        }

        Log::log(app()->isProduction() ? 'critical' : 'warning', 'reCAPTCHA is not fully configured.', [
            'environment' => app()->environment(),
            'verification_mode' => app()->isProduction() ? 'fail_closed' : 'disabled',
            'site_key_present' => self::siteKey() !== null,
            'secret_key_present' => self::secretKey() !== null,
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

    private static function filledConfig(string $key): ?string
    {
        $value = config($key);

        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        return trim($value);
    }
}
