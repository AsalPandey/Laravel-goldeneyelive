<?php

namespace App\Console\Commands;

use App\Models\Notice;
use App\Models\ServicePillar;
use App\Models\SiteSetting;
use App\Support\PublicCtaContract;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

#[Signature('goldeneye:normalize-public-settings {--dry-run : Preview changes without saving}')]
#[Description('Normalize stale public CTA, notice, service pillar, and WhatsApp text values.')]
class NormalizePublicSettings extends Command
{
    /**
     * @var array<string, string>
     */
    private const SiteCtaIntents = [
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
     * @var array<string, string>
     */
    private const ExactSiteTextReplacements = [
        'whatsapp_cta_subtext' => '',
        'whatsapp_prefill_message' => 'Hi Golden Eye Academy, I have a question about classes and enrollment.',
    ];

    /**
     * @var array<int, string>
     */
    private const StaleCtaValues = [
        'ask for course guidance',
        'message us on whatsapp',
        'contact us',
        'explore programs',
        'see courses',
    ];

    /**
     * @var array<int, string>
     */
    private const StaleWhatsappSubtexts = [
        'casual questions. quick reply.',
        'casual questions. quick reply',
    ];

    /**
     * @var array<int, string>
     */
    private const CacheKeys = [
        'homepage_data',
        'homepage_data_v2',
        'site_active_notice',
        'site_settings',
        'site_shared_data',
        'service_pillars',
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $rows = [];
        $changedSiteSettingKeys = [];
        $totalChanges = 0;

        [$siteRows, $siteChanges, $siteKeys] = $this->normalizeSiteSettings($dryRun);
        $rows = array_merge($rows, $siteRows);
        $totalChanges += $siteChanges;
        $changedSiteSettingKeys = array_merge($changedSiteSettingKeys, $siteKeys);

        [$noticeRows, $noticeChanges] = $this->normalizeNotices($dryRun);
        $rows = array_merge($rows, $noticeRows);
        $totalChanges += $noticeChanges;

        [$pillarRows, $pillarChanges] = $this->normalizeServicePillars($dryRun);
        $rows = array_merge($rows, $pillarRows);
        $totalChanges += $pillarChanges;

        $rows = array_merge($rows, $this->inspectWhatsappNumber());

        if ($rows === []) {
            $this->info('No public settings, notices, or service pillars were found to inspect.');
        } else {
            $this->table(
                ['Table', 'Key / ID', 'Old value', 'New value', 'Status'],
                $rows,
            );
        }

        if (! $dryRun && $totalChanges > 0) {
            $this->clearAffectedCaches($changedSiteSettingKeys);
        }

        $this->info(($dryRun ? 'Dry-run total changes: ' : 'Total changes: ').$totalChanges);

        return self::SUCCESS;
    }

    /**
     * @return array{0: array<int, array<int, string>>, 1: int, 2: array<int, string>}
     */
    private function normalizeSiteSettings(bool $dryRun): array
    {
        $rows = [];
        $changes = 0;
        $changedKeys = [];
        $keys = array_unique(array_merge(
            array_keys(self::SiteCtaIntents),
            array_keys(self::ExactSiteTextReplacements),
        ));

        foreach ($keys as $key) {
            $setting = SiteSetting::query()->where('key', $key)->first();

            if (! $setting) {
                $rows[] = $this->row('site_settings', $key, '(missing)', '(unchanged)', 'skipped');

                continue;
            }

            $oldValue = (string) ($setting->value ?? '');
            $newValue = $this->normalizeSiteSettingValue($key, $oldValue);

            if ($newValue === null || $newValue === $oldValue) {
                $rows[] = $this->row('site_settings', $key, $oldValue, $oldValue, 'skipped');

                continue;
            }

            $changes++;
            $changedKeys[] = $key;
            $status = $dryRun ? 'would change' : 'changed';

            if (! $dryRun) {
                $setting->forceFill(['value' => $newValue])->save();
            }

            $rows[] = $this->row('site_settings', $key, $oldValue, $newValue, $status);
        }

        return [$rows, $changes, $changedKeys];
    }

    /**
     * @return array{0: array<int, array<int, string>>, 1: int}
     */
    private function normalizeNotices(bool $dryRun): array
    {
        $rows = [];
        $changes = 0;

        foreach (Notice::query()->orderBy('id')->cursor() as $notice) {
            $oldValue = (string) ($notice->button_text ?? '');
            $newValue = $this->normalizeLinkedCtaValue($oldValue, $notice->link);

            if ($newValue === null || $newValue === $oldValue) {
                $rows[] = $this->row('notices', (string) $notice->id, $oldValue, $oldValue, 'skipped');

                continue;
            }

            $changes++;
            $status = $dryRun ? 'would change' : 'changed';

            if (! $dryRun) {
                $notice->forceFill(['button_text' => $newValue])->save();
            }

            $rows[] = $this->row('notices', (string) $notice->id, $oldValue, $newValue, $status);
        }

        return [$rows, $changes];
    }

    /**
     * @return array{0: array<int, array<int, string>>, 1: int}
     */
    private function normalizeServicePillars(bool $dryRun): array
    {
        $rows = [];
        $changes = 0;

        foreach (ServicePillar::query()->orderBy('id')->cursor() as $pillar) {
            $oldValue = (string) ($pillar->cta_label ?? '');
            $newValue = $this->normalizeLinkedCtaValue($oldValue, $pillar->cta_url);

            if ($newValue === null || $newValue === $oldValue) {
                $rows[] = $this->row('service_pillars', (string) $pillar->id, $oldValue, $oldValue, 'skipped');

                continue;
            }

            $changes++;
            $status = $dryRun ? 'would change' : 'changed';

            if (! $dryRun) {
                $pillar->forceFill(['cta_label' => $newValue])->save();
            }

            $rows[] = $this->row('service_pillars', (string) $pillar->id, $oldValue, $newValue, $status);
        }

        return [$rows, $changes];
    }

    /**
     * @return array<int, array<int, string>>
     */
    private function inspectWhatsappNumber(): array
    {
        $setting = SiteSetting::query()->where('key', 'whatsapp_number')->first();

        if (! $setting) {
            return [
                $this->row('site_settings', 'whatsapp_number', '(missing)', '(unchanged)', 'skipped'),
            ];
        }

        $oldValue = (string) ($setting->value ?? '');

        if ($oldValue !== '' && PublicCtaContract::normalizeWhatsappNumber($oldValue) === null) {
            return [
                $this->row('site_settings', 'whatsapp_number', $oldValue, '(unchanged)', 'reported invalid; skipped'),
            ];
        }

        return [
            $this->row('site_settings', 'whatsapp_number', $oldValue, $oldValue, 'skipped'),
        ];
    }

    private function normalizeSiteSettingValue(string $key, string $value): ?string
    {
        $comparisonValue = $this->comparisonValue($value);

        if (isset(self::SiteCtaIntents[$key]) && $this->isStaleCtaValue($comparisonValue)) {
            return PublicCtaContract::normalizeLabel($value, self::SiteCtaIntents[$key]);
        }

        if (
            array_key_exists($key, self::ExactSiteTextReplacements)
            && in_array($comparisonValue, self::StaleWhatsappSubtexts, true)
        ) {
            return self::ExactSiteTextReplacements[$key];
        }

        return null;
    }

    private function normalizeLinkedCtaValue(string $value, mixed $url): ?string
    {
        if (! $this->isStaleCtaValue($this->comparisonValue($value))) {
            return null;
        }

        return PublicCtaContract::normalizeLabel($value, PublicCtaContract::intentForUrl($url));
    }

    private function isStaleCtaValue(string $value): bool
    {
        return in_array($value, self::StaleCtaValues, true);
    }

    /**
     * @return array<int, string>
     */
    private function row(string $table, string $key, string $oldValue, string $newValue, string $status): array
    {
        return [
            $table,
            $key,
            $this->displayValue($oldValue),
            $this->displayValue($newValue),
            $status,
        ];
    }

    private function displayValue(string $value): string
    {
        if ($value === '') {
            return '(blank)';
        }

        return str($value)->limit(90, '...')->toString();
    }

    private function comparisonValue(string $value): string
    {
        return str(strip_tags($value))
            ->squish()
            ->lower()
            ->toString();
    }

    /**
     * @param  array<int, string>  $settingKeys
     */
    private function clearAffectedCaches(array $settingKeys): void
    {
        foreach (array_unique($settingKeys) as $settingKey) {
            Cache::forget("setting_{$settingKey}");
        }

        foreach (self::CacheKeys as $cacheKey) {
            Cache::forget($cacheKey);
        }
    }
}
