<?php

namespace App\Support;

use Closure;

final class PublicCtaContract
{
    public const HelpLabel = 'Ask for Course Help';

    public const CourseLabel = 'View Course Details';

    public const WhatsappLabel = 'Message on WhatsApp';

    public const SeeCoursesLabel = 'See Courses';

    /**
     * @var array<string, string>
     */
    private const BrandingCtaIntents = [
        'hero_cta_1_text' => 'help',
        'hero_cta_text' => 'help',
        'popup_button_text' => 'help',
        'sticky_cta_text' => 'help',
        'blog_cta_btn' => 'help',
        'faq_btn_text' => 'help',
        'hero_cta_2_text' => 'course',
        'whatsapp_cta_text' => 'whatsapp',
        'whatsapp_button_text' => 'whatsapp',
    ];

    /**
     * @var array<int, string>
     */
    private const ProtectedInternalPrefixes = [
        '/admin',
        '/dashboard',
        '/login',
        '/logout',
        '/register',
        '/settings',
    ];

    /**
     * @var array<int, string>
     */
    private const BrandingTextFields = [
        'site_name',
        'schema_markup',
        'meta_keywords',
        'google_maps_embed',
        'external_review_proof_note',
    ];

    /**
     * @return array<string, mixed>
     */
    public static function normalizeBrandingPayload(array $input): array
    {
        $normalized = [];

        foreach (self::BrandingCtaIntents as $field => $intent) {
            if (array_key_exists($field, $input)) {
                $normalized[$field] = self::normalizeLabel($input[$field] ?? null, $intent);
            }
        }

        if (array_key_exists('popup_register_link', $input)) {
            $normalized['popup_register_link'] = self::normalizeUrlForInput($input['popup_register_link'] ?? null);
        }

        if (array_key_exists('whatsapp_number', $input)) {
            $rawNumber = self::cleanText($input['whatsapp_number'] ?? null);
            $normalized['whatsapp_number'] = self::normalizeWhatsappNumber($rawNumber) ?? $rawNumber;
        }

        if (array_key_exists('whatsapp_prefill_message', $input)) {
            $normalized['whatsapp_prefill_message'] = self::normalizeWhatsappMessage($input['whatsapp_prefill_message'] ?? null);
        }

        foreach (self::BrandingTextFields as $field) {
            if (array_key_exists($field, $input)) {
                $normalized[$field] = self::normalizeBrandText(self::cleanText($input[$field] ?? null));
            }
        }

        return $normalized;
    }

    /**
     * @return array<string, mixed>
     */
    public static function normalizeNoticePayload(array $input): array
    {
        $normalized = [];

        if (array_key_exists('link', $input)) {
            $normalized['link'] = self::normalizeUrlForInput($input['link'] ?? null);
        }

        if (array_key_exists('button_text', $input) || array_key_exists('link', $input)) {
            $link = $normalized['link'] ?? ($input['link'] ?? null);
            $normalized['button_text'] = self::normalizeLabel(
                $input['button_text'] ?? null,
                self::intentForUrl($link)
            );
        }

        return $normalized;
    }

    /**
     * @return array<string, mixed>
     */
    public static function normalizeServicePillarPayload(array $input): array
    {
        $normalized = [];

        if (array_key_exists('cta_url', $input)) {
            $normalized['cta_url'] = self::normalizeUrlForInput($input['cta_url'] ?? null);
        }

        if (array_key_exists('cta_label', $input) || array_key_exists('cta_url', $input)) {
            $url = $normalized['cta_url'] ?? ($input['cta_url'] ?? null);
            $normalized['cta_label'] = self::normalizeLabel(
                $input['cta_label'] ?? null,
                self::intentForUrl($url)
            );
        }

        return $normalized;
    }

    public static function normalizeLabel(mixed $label, string $intent = 'help', bool $allowSeeCourses = false): string
    {
        $value = self::cleanText($label);
        $lower = strtolower($value);

        if ($intent === 'auto') {
            $intent = self::intentForLabel($lower);
        }

        if ($intent === 'whatsapp') {
            return self::WhatsappLabel;
        }

        if ($intent === 'course') {
            if ($allowSeeCourses && $lower === strtolower(self::SeeCoursesLabel)) {
                return self::SeeCoursesLabel;
            }

            return self::CourseLabel;
        }

        return self::HelpLabel;
    }

    public static function normalizeWhatsappNumber(mixed $number): ?string
    {
        $value = self::cleanText($number);

        if ($value === '') {
            return null;
        }

        $value = preg_replace('/[\s\-\(\)]+/', '', $value) ?? $value;

        if (preg_match('/^\+977(97|98)\d{8}$/', $value) === 1) {
            return ltrim($value, '+');
        }

        if (preg_match('/^977(97|98)\d{8}$/', $value) === 1) {
            return $value;
        }

        if (preg_match('/^(97|98)\d{8}$/', $value) === 1) {
            return '977'.$value;
        }

        return null;
    }

    public static function normalizeWhatsappMessage(mixed $message): string
    {
        $value = trim(strip_tags((string) $message));

        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/', '', $value) ?? '';

        return self::normalizeBrandText($value);
    }

    public static function isSafePublicUrl(mixed $url): bool
    {
        $value = self::cleanText($url);

        if ($value === '') {
            return true;
        }

        if (preg_match('/[\x00-\x1F\x7F<>`]/', $value) === 1) {
            return false;
        }

        if (str_starts_with($value, '//') || str_contains($value, '\\')) {
            return false;
        }

        if (str_starts_with($value, '/')) {
            $path = parse_url($value, PHP_URL_PATH);

            if (! is_string($path) || str_contains($path, '..')) {
                return false;
            }

            foreach (self::ProtectedInternalPrefixes as $prefix) {
                if ($path === $prefix || str_starts_with($path, $prefix.'/')) {
                    return false;
                }
            }

            return true;
        }

        $parts = parse_url($value);

        return is_array($parts)
            && isset($parts['scheme'], $parts['host'])
            && in_array(strtolower((string) $parts['scheme']), ['http', 'https'], true);
    }

    public static function publicUrlRule(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail): void {
            if (! self::isSafePublicUrl($value)) {
                $fail('The '.$attribute.' must be a safe internal path or an http/https URL.');
            }
        };
    }

    public static function whatsappNumberRule(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail): void {
            if (self::cleanText($value) === '') {
                return;
            }

            if (self::normalizeWhatsappNumber($value) === null) {
                $fail('The '.$attribute.' must be a valid Nepal WhatsApp number.');
            }
        };
    }

    public static function intentForUrl(mixed $url): string
    {
        $value = self::cleanText($url);

        if ($value === '') {
            return 'help';
        }

        $lower = strtolower($value);
        $host = parse_url($lower, PHP_URL_HOST);
        $path = parse_url($value, PHP_URL_PATH);
        $path = is_string($path) ? strtolower($path) : strtolower($value);

        if (is_string($host) && str_contains($host, 'whatsapp')) {
            return 'whatsapp';
        }

        if (is_string($host) && $host === 'wa.me') {
            return 'whatsapp';
        }

        if (str_starts_with($path, '/join-now') || str_starts_with($path, '/contact')) {
            return 'help';
        }

        if (str_contains($lower, 'course_help') || str_contains($lower, 'course-help')) {
            return 'help';
        }

        if ($path === '/courses' || str_starts_with($path, '/courses/') || str_starts_with($path, '/courses-all')) {
            return 'course';
        }

        return self::intentForLabel($lower);
    }

    private static function normalizeUrlForInput(mixed $url): ?string
    {
        $value = self::cleanText($url);

        if ($value === '') {
            return null;
        }

        return $value;
    }

    private static function intentForLabel(string $label): string
    {
        if (str_contains($label, 'whatsapp')) {
            return 'whatsapp';
        }

        if (in_array($label, [
            'view course details',
            'view courses',
            'see courses',
            'explore programs',
            'explore courses',
            'all courses',
            'course details',
            'learn more',
        ], true)) {
            return 'course';
        }

        return 'help';
    }

    private static function cleanText(mixed $value): string
    {
        $text = trim(strip_tags((string) $value));

        return preg_replace('/\s+/', ' ', $text) ?? '';
    }

    public static function normalizeBrandText(string $value): string
    {
        return str_replace(
            ['GoldenEye Academy', 'GoldenEye%20Academy', 'GoldenEye'],
            ['Golden Eye Academy', 'Golden%20Eye%20Academy', 'Golden Eye'],
            $value,
        );
    }
}
