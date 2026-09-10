<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CMSRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->hasRole('Admin')
            || ($this->user()?->hasRole('Staff') && $this->routeIs('admin.*.store'));
    }

    protected function prepareForValidation(): void
    {
        if ($this->user()?->hasRole('Staff') && ! $this->user()->hasRole('Admin') && $this->routeIs('admin.*.store')) {
            $this->merge([
                'status' => $this->routeIs('admin.blog.store') ? 'draft' : 'inactive',
                'is_featured' => false,
                'published_at' => null,
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:10000'],
            'meta_keywords' => ['nullable', 'string', 'max:10000'],
            'aeo_summary' => ['nullable', 'string', 'max:10000'],
        ];
    }
}
