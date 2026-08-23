<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CMSRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['Admin', 'Staff']);
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
            'schema_markup' => [Rule::prohibitedIf(fn (): bool => ! $this->user()?->hasRole('Admin')), 'nullable', 'string', 'max:10000', function ($attribute, $value, $fail) {
                if ($value && ! $this->isValidJson($value)) {
                    $fail('The '.$attribute.' must be a valid JSON-LD string.');
                }
            }],
        ];
    }

    /**
     * Check if string is valid JSON
     */
    protected function isValidJson(string $string): bool
    {
        json_decode($string);

        return json_last_error() === JSON_ERROR_NONE;
    }
}
