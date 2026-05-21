<?php

namespace App\Http\Requests\Admin;

use App\Models\SiteSetting;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CourseRequest extends CMSRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->has('slug')) {
            $this->merge([
                'slug' => Str::slug((string) $this->input('slug')),
            ]);
        }
    }

    public function rules(): array
    {
        $imageLimit = SiteSetting::getValue('image_size_limit', 2048);
        $courseId = $this->route('course');

        if (is_object($courseId) && method_exists($courseId, 'getKey')) {
            $courseId = $courseId->getKey();
        }

        return array_merge(parent::rules(), [
            'name' => ['required', 'string', 'max:255'],
            'badge_text' => ['nullable', 'string', 'max:50'],
            'slug' => [
                'required',
                'string',
                'max:255',
                'alpha_dash:ascii',
                Rule::unique('courses', 'slug')->ignore($courseId),
            ],
            'category_id' => ['required', 'exists:course_categories,id'],
            'price' => ['required', 'string'],
            'duration' => ['required', 'string'],
            'instructor' => ['required', 'string'],
            'capacity' => ['required', 'string'],
            'description' => ['required', 'string'],
            'course_outline' => ['required', 'string'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', "max:{$imageLimit}"],
            'photo_path' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
            'is_featured' => ['nullable', 'boolean'],
            'display_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ]);
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Please enter a course name.',
            'slug.required' => 'Please enter a URL slug for this course.',
            'slug.alpha_dash' => 'The course slug may only contain letters, numbers, dashes, and underscores.',
            'slug.unique' => 'This course slug is already in use. Please choose a unique slug.',
            'category_id.required' => 'Please choose a course category before saving.',
            'category_id.exists' => 'The selected category no longer exists. Please choose another category.',
            'price.required' => 'Please enter the course fee or fee guidance.',
            'duration.required' => 'Please enter the course duration.',
            'instructor.required' => 'Please enter the instructor or teaching team.',
            'capacity.required' => 'Please enter the batch capacity.',
            'description.required' => 'Please add a course description.',
            'course_outline.required' => 'Please add the course outline or learning outcomes.',
        ];
    }
}
