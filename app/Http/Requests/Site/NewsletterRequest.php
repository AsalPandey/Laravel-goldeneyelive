<?php

namespace App\Http\Requests\Site;

use App\Support\Recaptcha;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class NewsletterRequest extends FormRequest
{
    /**
     * @var string
     */
    protected $errorBag = 'newsletter';

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => mb_strtolower(trim((string) $this->input('email'))),
        ]);
    }

    public function rules(): array
    {
        $rules = [
            'email' => ['required', 'email', 'max:255'],
        ];

        if (Recaptcha::challengeRequired()) {
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

    protected function failedValidation(Validator $validator): void
    {
        $this->session()->flash('newsletter_validation_errors', $validator->errors()->toArray());

        parent::failedValidation($validator);
    }
}
