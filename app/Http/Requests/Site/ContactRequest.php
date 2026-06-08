<?php

namespace App\Http\Requests\Site;

use App\Models\AnalyticsEvent;
use App\Support\Recaptcha;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
{
    public const PHONE_REGEX = '/^(?:\+977(?:97|98)\d{8}|(?:97|98)\d{8}|0\d{9})$/';

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20', 'regex:'.self::PHONE_REGEX],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
            'lead_source' => ['nullable', 'string', 'max:255'],
            'landing_page' => ['nullable', 'string', 'max:500'],
            'cta_id' => ['nullable', 'string', 'max:255'],
        ];

        if (Recaptcha::challengeRequired()) {
            $rules['g-recaptcha-response'] = ['required', 'string'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'phone.regex' => 'Please enter a valid Nepal phone number, such as 98XXXXXXXX, 97XXXXXXXX, +97798XXXXXXXX, or 0XXXXXXXXX.',
            'g-recaptcha-response.required' => 'Please complete the security verification.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        if ($validator->errors()->has('phone')) {
            try {
                AnalyticsEvent::record('phone_validation_error', [
                    'source_page' => $this->input('landing_page', $this->headers->get('referer', $this->path())),
                    'source_section' => $this->input('lead_source', 'contact-form-phone'),
                    'cta_label' => 'Phone validation',
                    'inquiry_intent' => 'course_guidance',
                    'device_type' => 'server',
                    'metadata' => [
                        'field' => 'phone',
                        'validation' => 'server',
                    ],
                ]);
            } catch (\Throwable $exception) {
                report($exception);
            }
        }

        parent::failedValidation($validator);
    }
}
