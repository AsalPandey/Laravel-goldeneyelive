<?php

namespace App\Http\Requests\Admin;

use App\Models\SiteSetting;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryRequest extends CMSRequest
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
        $categoryId = $this->route('category');

        if (is_object($categoryId) && method_exists($categoryId, 'getKey')) {
            $categoryId = $categoryId->getKey();
        }

        return array_merge(parent::rules(), [
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                'alpha_dash:ascii',
                Rule::unique('course_categories', 'slug')->ignore($categoryId),
            ],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', "max:{$imageLimit}"],
            'image_path' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
            'order_priority' => ['nullable', 'integer'],
        ]);
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Please enter a category name.',
            'slug.required' => 'Please enter a URL slug for this category.',
            'slug.alpha_dash' => 'The category slug may only contain letters, numbers, dashes, and underscores.',
            'slug.unique' => 'This category slug is already in use. Please choose a unique slug.',
            'status.required' => 'Please choose whether this category is active or inactive.',
            'status.in' => 'Category status must be active or inactive.',
            'order_priority.integer' => 'Order priority must be a whole number.',
        ];
    }
}
