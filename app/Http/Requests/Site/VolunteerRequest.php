<?php

namespace App\Http\Requests\Site;

class VolunteerRequest extends PublicFormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:120',
            'email' => 'required|email:rfc|max:150',
            'phone' => $this->phoneRules(),
            'city' => 'required|string|max:80',
            'occupation' => 'nullable|string|max:120',
            'availability' => ['required', \Illuminate\Validation\Rule::in(array_keys(\App\Support\Options::availability()))],
            'interests' => 'required|array|min:1|max:16',
            'interests.*' => [\Illuminate\Validation\Rule::in(array_keys(\App\Support\Options::interests()))],
            'skills' => 'nullable|string|max:1000',
            'message' => 'nullable|string|max:2000',
            'consent' => 'accepted',
        ];
    }
}
