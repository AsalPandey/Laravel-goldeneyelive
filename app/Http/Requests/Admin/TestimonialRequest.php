<?php

namespace App\Http\Requests\Admin;

use App\Models\SiteSetting;

class TestimonialRequest extends CMSRequest
{
    public function rules(): array
    {
        $imageLimit = SiteSetting::getValue('image_size_limit', 2048);
        $rules = parent::rules();
        unset(
            $rules['meta_title'],
            $rules['meta_description'],
            $rules['meta_keywords'],
            $rules['aeo_summary'],
            $rules['schema_markup'],
        );

        return array_merge($rules, [
            'student_name' => ['required', 'string', 'max:255'],
            'course_name' => ['nullable', 'string', 'max:255'],
            'course_id' => ['nullable', 'integer', 'exists:courses,id'],
            'content' => ['required', 'string', 'max:10000'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', "max:{$imageLimit}"],
            'photo_path' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
            'is_featured' => ['nullable', 'boolean'],
        ]);
    }
}
