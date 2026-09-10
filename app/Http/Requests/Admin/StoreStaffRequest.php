<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use App\Rules\OrganizationEmail;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator;

class StoreStaffRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->hasRole('Admin') ?? false;
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('email'))) {
            $this->merge(['email' => Str::lower(trim($this->input('email')))]);
        }
    }

    /** @return array<callable> */
    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($validator->errors()->has('email')) {
                return;
            }

            if (User::whereRaw('LOWER(email) = ?', [$this->input('email')])->exists()) {
                $validator->errors()->add('email', 'The email has already been taken.');
            }

            if ((new User(['email' => $this->input('email')]))->isPermanentAdmin()) {
                $validator->errors()->add('email', 'This email is reserved for a permanent Admin.');
            }
        }];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', new OrganizationEmail],
            'role' => ['prohibited'],
            'password' => ['prohibited'],
        ];
    }
}
