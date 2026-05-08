<?php

namespace App\Http\Requests\Site;

use App\Models\SiteSetting;
use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'lead_source' => ['nullable', 'string', 'max:255'],
            'landing_page' => ['nullable', 'string', 'max:500'],
            'cta_id' => ['nullable', 'string', 'max:255'],
        ];

        if (SiteSetting::getValue('recaptcha_secret_key')) {
            $rules['g-recaptcha-response'] = ['required', 'string'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'g-recaptcha-response.required' => 'Please complete the security verification.',
        ];
    }
}
