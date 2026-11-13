<?php

namespace App\Http\Requests\Site;

class ContactRequest extends PublicFormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:120',
            'email' => 'required|email:rfc|max:150',
            'phone' => $this->phoneRules(false),
            'subject' => 'required|string|max:150',
            'message' => 'required|string|min:10|max:3000',
        ];
    }
}
