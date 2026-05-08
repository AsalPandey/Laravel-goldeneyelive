<?php

namespace App\Http\Requests\Site;

use App\Models\Course;
use App\Models\SiteSetting;
use Closure;
use Illuminate\Foundation\Http\FormRequest;

class JoinNowRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'firstName' => ['required', 'string', 'max:255'],
            'lastName' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
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
            'contactMethod' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'queries' => ['nullable', 'string'],
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
            'course.required' => 'Please select a career path to proceed.',
            'g-recaptcha-response.required' => 'Please complete the security verification.',
        ];
    }
}
