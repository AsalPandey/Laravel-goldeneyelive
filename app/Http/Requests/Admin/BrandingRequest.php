<?php

namespace App\Http\Requests\Admin;

use App\Http\Controllers\Admin\BrandingController;
use App\Models\SiteSetting;
use App\Rules\ApprovedMapEmbedUrl;
use App\Rules\PublicMediaPath;
use App\Support\CmsPublicContent;
use App\Support\PublicCtaContract;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class BrandingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasAnyRole(['Admin', 'Staff']) ?? false;
    }

    protected function prepareForValidation(): void
    {
        $payload = PublicCtaContract::normalizeBrandingPayload($this->all());
        $mapEmbed = $payload['google_maps_embed'] ?? null;

        if (is_string($mapEmbed) && str_contains(strtolower($mapEmbed), '<iframe')) {
            preg_match('/src=["\']([^"\']+)["\']/i', $mapEmbed, $match);
            $payload['google_maps_embed'] = $match[1] ?? $mapEmbed;
        }

        $this->merge($payload);
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($this->has('whatsapp_number') && blank($this->input('whatsapp_number'))) {
                $whatsappConfigured = filled(SiteSetting::getValue('whatsapp_number'))
                    || $this->filled('whatsapp_cta_text')
                    || $this->filled('whatsapp_button_text')
                    || $this->filled('whatsapp_prefill_message');

                if ($whatsappConfigured) {
                    $validator->errors()->add('whatsapp_number', 'The whatsapp number is required while the WhatsApp widget is configured.');
                }
            }

            foreach (array_keys(CmsPublicContent::audienceDefinitions()) as $audienceKey) {
                $prefix = "audience_{$audienceKey}";
                $pageStatus = $this->input(
                    "{$prefix}_status",
                    SiteSetting::getValue("{$prefix}_status", 'active'),
                );
                $sectionStatuses = collect(['hero', 'guidance', 'support', 'final'])
                    ->map(fn (string $section): mixed => $this->input(
                        "{$prefix}_{$section}_status",
                        SiteSetting::getValue("{$prefix}_{$section}_status", 'active'),
                    ));

                if ($pageStatus === 'active' && $sectionStatuses->every(fn (mixed $status): bool => $status === 'inactive')) {
                    $validator->errors()->add(
                        "{$prefix}_status",
                        'Keep at least one section visible before making this audience page active.',
                    );
                }
            }
        });
    }

    public function rules(): array
    {
        $imageLimit = SiteSetting::getValue('image_size_limit', 2048);

        $rules = [
            'site_name' => ['nullable', 'string', 'max:255'],
            'site_name_suffix' => ['nullable', 'string', 'max:255'],
            'site_email' => ['nullable', 'email', 'max:255'],
            'site_phone' => ['nullable', 'string', 'max:255'],
            'site_address' => ['nullable', 'string', 'max:500'],
            'whatsapp_number' => ['nullable', 'string', 'max:20', PublicCtaContract::whatsappNumberRule()],
            'whatsapp_cta_text' => ['nullable', 'string', 'max:50'],
            'whatsapp_button_text' => ['nullable', 'string', 'max:50'],
            'whatsapp_cta_subtext' => ['nullable', 'string', 'max:255'],
            'whatsapp_prefill_message' => ['nullable', 'string', 'max:500'],
            'facebook_url' => ['nullable', 'url', 'max:255'],
            'instagram_url' => ['nullable', 'url', 'max:255'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'youtube_url' => ['nullable', 'url', 'max:255'],
            'tiktok_url' => ['nullable', 'url', 'max:255'],
            'twitter_url' => ['nullable', 'url', 'max:255'],
            'google_business_profile_url' => ['nullable', 'url', 'max:255'],
            'external_review_proof_note' => ['nullable', 'string', 'max:500'],
            'google_analytics_id' => ['nullable', 'string', 'regex:/^G-[a-zA-Z0-9-]+$/'],
            'opening_hours' => ['nullable', 'string', 'max:255'],
            'geo_latitude' => ['nullable', 'string', 'max:255'],
            'geo_longitude' => ['nullable', 'string', 'max:255'],
            'google_maps_embed' => ['nullable', 'string', 'max:2048', new ApprovedMapEmbedUrl],
            'meta_keywords' => ['nullable', 'string'],
            'image_size_limit' => ['nullable', 'integer', 'min:512', 'max:10240'],
            'hero_cta_1_text' => ['nullable', 'string', 'max:50'],
            'hero_cta_2_text' => ['nullable', 'string', 'max:50'],
            'hero_cta_text' => ['nullable', 'string', 'max:50'],
            'popup_button_text' => ['nullable', 'string', 'max:50'],
            'popup_status' => ['nullable', 'in:active,inactive'],
            'sticky_cta_text' => ['nullable', 'string', 'max:50'],
            'blog_cta_btn' => ['nullable', 'string', 'max:50'],
            'faq_btn_text' => ['nullable', 'string', 'max:50'],
            'faq_btn_text_expanded' => ['nullable', 'string', 'max:50'],
            'footer_about_text' => ['nullable', 'string', 'max:1000'],
            'course_confirmation_note' => ['nullable', 'string', 'max:500'],
            'popup_register_link' => ['nullable', 'string', 'max:500', PublicCtaContract::publicUrlRule()],

            // Image paths (vault selection)
            'site_logo_path' => ['nullable', 'string', 'max:255', new PublicMediaPath],
            'site_favicon_path' => ['nullable', 'string', 'max:255', new PublicMediaPath],
            'site_footer_logo_path' => ['nullable', 'string', 'max:255', new PublicMediaPath],
            'breadcrumb_bg_path' => ['nullable', 'string', 'max:255', new PublicMediaPath],
            'cta_bg_path' => ['nullable', 'string', 'max:255', new PublicMediaPath],
            'hero_image_path' => ['nullable', 'string', 'max:255', new PublicMediaPath],
            'about_image_path' => ['nullable', 'string', 'max:255', new PublicMediaPath],
            'founder_image_path' => ['nullable', 'string', 'max:255', new PublicMediaPath],
            'popup_image_path' => ['nullable', 'string', 'max:255', new PublicMediaPath],
            'external_review_screenshot_path' => ['nullable', 'string', 'max:255', new PublicMediaPath],

            // Image uploads
            'site_logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', "max:{$imageLimit}"],
            'site_favicon' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', "max:{$imageLimit}"],
            'site_footer_logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', "max:{$imageLimit}"],
            'breadcrumb_bg' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', "max:{$imageLimit}"],
            'cta_bg' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', "max:{$imageLimit}"],
            'hero_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', "max:{$imageLimit}"],
            'about_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', "max:{$imageLimit}"],
            'founder_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', "max:{$imageLimit}"],
            'popup_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', "max:{$imageLimit}"],
            'external_review_screenshot' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', "max:{$imageLimit}"],
        ];

        foreach (CmsPublicContent::imageKeys() as $imageKey) {
            $rules[$imageKey.'_path'] = ['nullable', 'string', 'max:255', new PublicMediaPath];
            $rules[$imageKey] = ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', "max:{$imageLimit}"];
        }

        foreach (BrandingController::REMOVABLE_IMAGE_KEYS as $imageKey) {
            $rules['remove_'.$imageKey] = ['nullable', 'boolean'];
            $rules[$imageKey.'_path'] = [
                'exclude_if:remove_'.$imageKey.',1',
                'nullable',
                'string',
                'max:255',
                new PublicMediaPath,
            ];
        }

        $rules = array_merge($rules, CmsPublicContent::validationRules());

        $isAdmin = $this->user()?->hasRole('Admin') ?? false;

        if (! $isAdmin) {
            foreach (BrandingController::SENSITIVE_KEYS as $key) {
                unset($rules[$key]);
            }
        }

        foreach (array_merge(BrandingController::TEXT_KEYS, CmsPublicContent::textKeys()) as $key) {
            if (! $isAdmin && in_array($key, BrandingController::SENSITIVE_KEYS, true)) {
                continue;
            }

            $rules[$key] ??= ['nullable', 'string'];
        }

        return $rules;
    }
}
