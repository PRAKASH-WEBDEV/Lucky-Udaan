<?php

namespace App\Http\Requests\Admin;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'max:150'],
            'password' => ['required', 'string', 'max:200'],
        ];
    }

    /** Attempt login with rate limiting (5 attempts per email + IP, then a lockout). */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $credentials = $this->only('email', 'password') + ['is_active' => true];

        if (! Auth::guard('admin')->attempt($credentials, $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey(), 300);

            throw ValidationException::withMessages([
                'email' => 'These credentials do not match an active account.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));
        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => 'Too many login attempts. Please try again in '.ceil($seconds / 60).' minute(s).',
        ]);
    }

    public function throttleKey(): string
    {
        return 'admin-login|'.Str::transliterate(Str::lower($this->string('email'))).'|'.$this->ip();
    }
}
