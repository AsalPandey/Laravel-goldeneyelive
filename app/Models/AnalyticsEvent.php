<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class AnalyticsEvent extends Model
{
    use HasFactory;

    public const ALLOWED_EVENTS = [
        'cta_click',
        'course_detail_view',
        'inquiry_step_1_submit',
        'inquiry_step_2_submit',
        'course_help_submit',
        'add_more_details_click',
        'whatsapp_click',
        'phone_click',
        'course_filter_used',
        'parent_inquiry_click',
        'study_abroad_inquiry_click',
        'admin_lead_status_change',
        'phone_validation_error',
    ];

    protected $fillable = [
        'event_name',
        'source_page',
        'source_section',
        'cta_label',
        'selected_course',
        'audience_type',
        'inquiry_intent',
        'device_type',
        'occurred_at',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'occurred_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function record(string $eventName, array $payload = []): ?self
    {
        if (! in_array($eventName, self::ALLOWED_EVENTS, true)) {
            return null;
        }

        try {
            if (SiteSetting::getValue('analytics_tracking_enabled', 'active') === 'disabled') {
                return null;
            }
        } catch (\Throwable) {
            return null;
        }

        return self::create([
            'event_name' => $eventName,
            'source_page' => self::shortText($payload['source_page'] ?? null, 500),
            'source_section' => self::shortText($payload['source_section'] ?? null),
            'cta_label' => self::shortText($payload['cta_label'] ?? null),
            'selected_course' => self::shortText($payload['selected_course'] ?? null),
            'audience_type' => self::shortText($payload['audience_type'] ?? null),
            'inquiry_intent' => self::shortText($payload['inquiry_intent'] ?? null),
            'device_type' => self::shortText($payload['device_type'] ?? null, 32),
            'occurred_at' => self::eventTime($payload['timestamp'] ?? $payload['occurred_at'] ?? null),
            'metadata' => self::safeMetadata($payload['metadata'] ?? []),
        ]);
    }

    protected function eventName(): Attribute
    {
        return Attribute::make(
            set: fn (string $value): string => Str::of($value)->lower()->snake()->toString(),
        );
    }

    private static function shortText(mixed $value, int $limit = 255): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return Str::limit(trim((string) $value), $limit, '');
    }

    private static function eventTime(mixed $value): CarbonInterface
    {
        if ($value === null || $value === '') {
            return now();
        }

        try {
            return Carbon::parse((string) $value);
        } catch (\Throwable) {
            return now();
        }
    }

    /**
     * @return array<string, string>
     */
    private static function safeMetadata(mixed $metadata): array
    {
        if (! is_array($metadata)) {
            return [];
        }

        $blockedKeys = [
            'address',
            'email',
            'firstname',
            'full_name',
            'goal',
            'lastname',
            'message',
            'name',
            'phone',
            'queries',
        ];

        $safe = [];

        foreach ($metadata as $key => $value) {
            $normalizedKey = Str::of((string) $key)->lower()->replace(['-', ' '], '_')->toString();

            if (in_array($normalizedKey, $blockedKeys, true) || ! is_scalar($value)) {
                continue;
            }

            $safe[$normalizedKey] = self::shortText($value) ?? '';
        }

        return $safe;
    }
}
