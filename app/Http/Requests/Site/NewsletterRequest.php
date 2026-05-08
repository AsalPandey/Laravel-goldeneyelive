<?php

namespace App\Http\Requests\Site;

use App\Models\SiteSetting;
use Illuminate\Foundation\Http\FormRequest;

class NewsletterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'email' => ['required', 'email', 'max:255'],
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
