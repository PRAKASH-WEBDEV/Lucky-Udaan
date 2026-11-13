<?php

namespace App\Http\Requests\Site;

class PartnershipRequest extends PublicFormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:120',
            'email' => 'required|email:rfc|max:150',
            'phone' => $this->phoneRules(),
            'organisation' => 'required|string|max:150',
            'designation' => 'nullable|string|max:120',
            'partnership_type' => ['required', \Illuminate\Validation\Rule::in(array_keys(\App\Support\Options::partnershipTypes()))],
            'website_url' => 'nullable|url|max:255',
            'message' => 'required|string|min:20|max:3000',
        ];
    }
}
