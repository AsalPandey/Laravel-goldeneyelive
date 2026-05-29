<?php

namespace App\Http\Requests\Admin;

use App\Models\SiteSetting;
use App\Support\PublicCtaContract;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class BrandingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasRole('Admin');
    }

    protected function prepareForValidation(): void
    {
        $this->merge(PublicCtaContract::normalizeBrandingPayload($this->all()));
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if (! $this->has('whatsapp_number') || filled($this->input('whatsapp_number'))) {
                return;
            }

            $whatsappConfigured = filled(SiteSetting::getValue('whatsapp_number'))
                || $this->filled('whatsapp_cta_text')
                || $this->filled('whatsapp_button_text')
                || $this->filled('whatsapp_prefill_message');

            if ($whatsappConfigured) {
                $validator->errors()->add('whatsapp_number', 'The whatsapp number is required while the WhatsApp widget is configured.');
            }
        });
    }

    public function rules(): array
    {
        $imageLimit = SiteSetting::getValue('image_size_limit', 2048);

        return [
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
            'twitter_url' => ['nullable', 'url', 'max:255'],
            'google_business_profile_url' => ['nullable', 'url', 'max:255'],
            'external_review_proof_note' => ['nullable', 'string', 'max:500'],
            'google_analytics_id' => ['nullable', 'string', 'regex:/^G-[a-zA-Z0-9-]+$/'],
            'recaptcha_site_key' => ['nullable', 'string', 'max:255'],
            'recaptcha_secret_key' => ['nullable', 'string', 'max:255'],
            'opening_hours' => ['nullable', 'string', 'max:255'],
            'geo_latitude' => ['nullable', 'string', 'max:255'],
            'geo_longitude' => ['nullable', 'string', 'max:255'],
            'google_maps_embed' => ['nullable', 'string'],
            'schema_markup' => ['nullable', 'string'],
            'meta_keywords' => ['nullable', 'string'],
            'image_size_limit' => ['nullable', 'integer', 'min:512', 'max:10240'],
            'hero_cta_1_text' => ['nullable', 'string', 'max:50'],
            'hero_cta_2_text' => ['nullable', 'string', 'max:50'],
            'hero_cta_text' => ['nullable', 'string', 'max:50'],
            'popup_button_text' => ['nullable', 'string', 'max:50'],
            'sticky_cta_text' => ['nullable', 'string', 'max:50'],
            'blog_cta_btn' => ['nullable', 'string', 'max:50'],
            'faq_btn_text' => ['nullable', 'string', 'max:50'],
            'popup_register_link' => ['nullable', 'string', 'max:500', PublicCtaContract::publicUrlRule()],

            // Image paths (vault selection)
            'site_logo_path' => ['nullable', 'string', 'max:255'],
            'site_favicon_path' => ['nullable', 'string', 'max:255'],
            'site_footer_logo_path' => ['nullable', 'string', 'max:255'],
            'breadcrumb_bg_path' => ['nullable', 'string', 'max:255'],
            'cta_bg_path' => ['nullable', 'string', 'max:255'],
            'hero_image_path' => ['nullable', 'string', 'max:255'],
            'about_image_path' => ['nullable', 'string', 'max:255'],
            'founder_image_path' => ['nullable', 'string', 'max:255'],
            'popup_image_path' => ['nullable', 'string', 'max:255'],
            'external_review_screenshot_path' => ['nullable', 'string', 'max:255'],

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
    }
}
