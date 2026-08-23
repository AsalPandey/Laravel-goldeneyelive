<?php

namespace App\Http\Requests\Site;

use App\Models\AnalyticsEvent;
use App\Models\Course;
use App\Support\NepalPhone;
use App\Support\Recaptcha;
use Closure;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class JoinNowRequest extends FormRequest
{
    /**
     * @return array<int, string>
     */
    public static function helpTopics(): array
    {
        return [
            'Choosing a course',
            'IELTS / PTE',
            'Japanese / Korean',
            'Computer skills',
            'Web development',
            'Fees and timing',
            'Parent inquiry',
            'Other',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $fullName = trim((string) $this->input('full_name'));

        if ($fullName !== '' && ! $this->filled('firstName')) {
            $parts = preg_split('/\s+/', $fullName, 2);

            $this->merge([
                'firstName' => $parts[0] ?? $fullName,
                'lastName' => $parts[1] ?? '',
            ]);
        }

        $course = $this->input('course')
            ?: $this->input('preferred_course')
            ?: $this->input('selected_course')
            ?: 'undecided';

        $this->merge([
            'course' => $course,
            'selected_course' => $course,
            'email' => trim((string) $this->input('email')),
            'phone' => NepalPhone::normalize($this->input('phone')),
            'help_topic' => $this->input('help_topic') ?: 'Choosing a course',
        ]);
    }

    public function rules(): array
    {
        $rules = [
            'full_name' => ['nullable', 'string', 'max:255'],
            'firstName' => ['required_without:full_name', 'string', 'max:255'],
            'lastName' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => [
                'required',
                'string',
                'max:10',
                'regex:'.NepalPhone::CANONICAL_REGEX,
                function (string $attribute, mixed $value, Closure $fail): void {
                    if (! NepalPhone::isPlausible((string) $value)) {
                        $fail('Please enter a real phone or WhatsApp number so our team can contact you.');
                    }
                },
            ],
            'course' => ['required', 'string', function (string $attribute, mixed $value, Closure $fail): void {
                if ($value === 'undecided') {
                    return;
                }

                $exists = Course::publiclyVisible()
                    ->where('slug', $value)
                    ->exists();

                if (! $exists) {
                    $fail('The selected course is currently not accepting enrollments.');
                }
            }],
            'help_topic' => ['required', 'string', Rule::in(self::helpTopics())],
            'preferred_course' => ['nullable', 'string', 'max:255'],
            'contactMethod' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'queries' => ['nullable', 'string', 'max:5000'],
            'current_education_level' => ['nullable', 'string', 'max:255'],
            'preferred_batch_time' => ['nullable', 'string', 'max:255'],
            'goal' => ['nullable', 'string', 'max:5000'],
            'lead_source' => ['nullable', 'string', 'max:255'],
            'landing_page' => ['nullable', 'string', 'max:500'],
            'cta_id' => ['nullable', 'string', 'max:255'],
            'selected_course' => ['nullable', 'string', 'max:255'],
            'source_page' => ['nullable', 'string', 'max:500'],
            'source_section' => ['nullable', 'string', 'max:255'],
            'audience_type' => ['nullable', 'string', 'max:255'],
            'inquiry_intent' => ['nullable', 'string', 'max:255'],
        ];

        if (Recaptcha::challengeRequired()) {
            $rules['g-recaptcha-response'] = ['required', 'string'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'firstName.required_without' => 'Please enter your name.',
            'help_topic.required' => 'Please choose what you need help with.',
            'help_topic.in' => 'Please choose one of the available help topics.',
            'course.required' => 'Please select a career path to proceed.',
            'phone.regex' => 'Please enter a valid Nepal mobile or landline, such as 98XXXXXXXX, +977 98XXXXXXXX, or 061-572599.',
            'g-recaptcha-response.required' => 'Please complete the security verification.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        if ($validator->errors()->has('phone')) {
            try {
                AnalyticsEvent::record('phone_validation_error', [
                    'source_page' => $this->input('source_page', $this->headers->get('referer', $this->path())),
                    'source_section' => $this->input('source_section', 'join-now-phone'),
                    'cta_label' => 'Phone validation',
                    'selected_course' => $this->input('selected_course', $this->input('course', 'undecided')),
                    'audience_type' => $this->input('audience_type'),
                    'inquiry_intent' => $this->input('inquiry_intent', 'course_guidance'),
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
