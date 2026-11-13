<?php

namespace App\Http\Requests\Site;

class DonationRequest extends PublicFormRequest
{
    public function rules(): array
    {
        return [
            'amount' => 'required|numeric|min:'.max(1, (int) setting('donation_min', 100)).'|max:10000000',
            'name' => 'required|string|max:120',
            'email' => 'required|email:rfc|max:150',
            'phone' => $this->phoneRules(),
            'pan' => ['nullable', 'required_if:wants_80g,1', 'regex:/^[A-Za-z]{5}[0-9]{4}[A-Za-z]$/'],
            'address' => 'nullable|required_if:wants_80g,1|string|max:500',
            'city' => 'nullable|string|max:80',
            'method' => 'required|in:upi,bank_transfer',
            'transaction_ref' => 'nullable|string|max:120',
            'campaign_id' => 'nullable|exists:campaigns,id',
            'message' => 'nullable|string|max:1000',
            'wants_80g' => 'nullable|boolean',
            'consent' => 'accepted',
        ];
    }

    public function messages(): array
    {
        return parent::messages() + [
            'pan.regex' => 'Please enter a valid PAN, e.g. ABCDE1234F.',
            'pan.required_if' => 'PAN is required for an 80G tax-exemption receipt.',
            'address.required_if' => 'Address is required for an 80G receipt.',
            'amount.min' => 'The minimum amount is ₹'.setting('donation_min', 100).'.',
        ];
    }}
