<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class SEORequest extends FormRequest
{
    public const ROBOTS_FULL_SITE_BLOCK_WARNING = 'Warning: this robots.txt can block search engines from indexing the whole website. Use this only for staging or maintenance. For production launch, this should normally be removed.';

    public function authorize(): bool
    {
        return auth()->user()->hasRole('Admin');
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if (! self::robotsTxtBlocksFullSite($this->input('robots_txt'))) {
                return;
            }

            if ($this->boolean('robots_txt_deindex_confirm')) {
                return;
            }

            $validator->errors()->add('robots_txt', self::ROBOTS_FULL_SITE_BLOCK_WARNING);
        });
    }

    public static function robotsTxtBlocksFullSite(mixed $robotsTxt): bool
    {
        $content = trim((string) $robotsTxt);

        if ($content === '') {
            return false;
        }

        foreach (preg_split('/\R/', $content) ?: [] as $line) {
            $line = trim((string) preg_replace('/#.*/', '', $line));

            if ($line === '') {
                continue;
            }

            if (preg_match('/^Disallow\s*:\s*(?:\/|\/\*|\*)\s*$/i', $line) === 1) {
                return true;
            }

            if (preg_match('/^(?:Noindex|X-Robots-Tag)\s*:\s*.*(?:noindex|none|\/|\*)/i', $line) === 1) {
                return true;
            }
        }

        return false;
    }

    public function rules(): array
    {
        return [
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_keywords' => ['nullable', 'string'],
            'meta_description' => ['nullable', 'string'],
            'google_analytics_id' => ['nullable', 'string', 'regex:/^G-[a-zA-Z0-9-]+$/'],
            'google_search_console_id' => ['nullable', 'string', 'max:255'],
            'bing_webmaster_id' => ['nullable', 'string', 'max:255'],
            'robots_txt' => ['nullable', 'string'],
            'aeo_summary' => ['nullable', 'string'],
            'speakable_selectors' => ['nullable', 'string'],
            'geo_latitude' => ['nullable', 'string', 'max:50'],
            'geo_longitude' => ['nullable', 'string', 'max:50'],
            'site_name' => ['nullable', 'string', 'max:255'],
            'site_name_suffix' => ['nullable', 'string', 'max:255'],
            'schema_markup' => ['nullable', 'string'],
            'robots_txt_deindex_confirm' => ['sometimes', 'accepted'],
        ];
    }
}
