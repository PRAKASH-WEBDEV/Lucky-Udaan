<?php

namespace App\Http\Requests\Site;

use Illuminate\Foundation\Http\FormRequest;

abstract class PublicFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        // Normalise phone numbers: keep digits and a leading +.
        if ($this->has('phone')) {
            $this->merge(['phone' => preg_replace('/(?!^\+)[^\d]/', '', (string) $this->input('phone'))]);
        }
    }

    protected function phoneRules(bool $required = true): array
    {
        return [$required ? 'required' : 'nullable', 'string', 'regex:/^\+?\d{10,13}$/'];
    }

    public function messages(): array
    {
        return [
            'phone.regex' => 'Please enter a valid 10-digit mobile number.',
            'consent.accepted' => 'Please confirm that we may contact you.',
        ];
    }
}
