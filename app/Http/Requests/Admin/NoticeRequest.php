<?php

namespace App\Http\Requests\Admin;

use App\Models\SiteSetting;
use App\Rules\PublicMediaPath;
use App\Support\PublicCtaContract;

class NoticeRequest extends CMSRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge(PublicCtaContract::normalizeNoticePayload($this->all()));
    }

    public function rules(): array
    {
        $imageLimit = SiteSetting::getValue('image_size_limit', 2048);

        return array_merge(parent::rules(), [
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'badge' => ['nullable', 'string', 'max:50'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', "max:{$imageLimit}"],
            'image_path' => ['exclude_if:remove_image,1', 'nullable', 'string', 'max:255', new PublicMediaPath],
            'remove_image' => ['nullable', 'boolean'],
            'link' => ['nullable', 'string', 'max:500', PublicCtaContract::publicUrlRule()],
            'button_text' => ['nullable', 'string', 'max:50'],
            'status' => ['required', 'in:active,inactive'],
            'display_type' => ['nullable', 'in:popup,bar,standard'],
            'is_urgent' => ['nullable', 'boolean'],
            'starts_at' => ['nullable', 'date', 'after_or_equal:1970-01-01', 'before_or_equal:2038-01-18 23:59:59'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:starts_at', 'before_or_equal:2038-01-18 23:59:59'],
        ]);
    }
}
