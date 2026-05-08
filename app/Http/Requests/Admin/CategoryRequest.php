<?php

namespace App\Http\Requests\Admin;

use App\Models\SiteSetting;

class CategoryRequest extends CMSRequest
{
    public function rules(): array
    {
        $imageLimit = SiteSetting::getValue('image_size_limit', 2048);

        return array_merge(parent::rules(), [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', "max:{$imageLimit}"],
            'image_path' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
            'order_priority' => ['nullable', 'integer'],
        ]);
    }
}
