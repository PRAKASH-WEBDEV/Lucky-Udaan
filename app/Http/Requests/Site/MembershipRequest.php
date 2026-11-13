<?php

namespace App\Http\Requests\Site;

class MembershipRequest extends PublicFormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:120',
            'email' => 'required|email:rfc|max:150',
            'phone' => $this->phoneRules(),
            'gender' => 'nullable|in:Female,Male,Other',
            'dob' => 'nullable|date|before:-10 years|after:1900-01-01',
            'occupation' => 'nullable|string|max:120',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:80',
            'membership_type' => ['required', \Illuminate\Validation\Rule::in(array_keys(\App\Models\Member::TYPES))],
            'message' => 'nullable|string|max:2000',
            'consent' => 'accepted',
        ];
    }
}
