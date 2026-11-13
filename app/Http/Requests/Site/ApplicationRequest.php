<?php

namespace App\Http\Requests\Site;

class ApplicationRequest extends PublicFormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:120',
            'email' => 'required|email:rfc|max:150',
            'phone' => $this->phoneRules(),
            'opening_id' => 'nullable|exists:openings,id',
            'qualification' => 'required|string|max:150',
            'institution' => 'nullable|string|max:150',
            'city' => 'nullable|string|max:80',
            'portfolio' => 'nullable|url|max:255',
            'message' => 'required|string|min:20|max:3000',
            'cv' => 'nullable|file|mimes:pdf,doc,docx|max:4096',
            'consent' => 'accepted',
        ];
    }
}
