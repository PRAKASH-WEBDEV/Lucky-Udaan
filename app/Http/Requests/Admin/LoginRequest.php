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

        // Emails are stored in lowercase; Postgres compares case-sensitively, so normalise first.
        $email = Str::lower(trim((string) $this->input('email')));
        $credentials = ['email' => $email, 'password' => (string) $this->input('password'), 'is_active' => true];

        if (! Auth::guard('admin')->attempt($credentials, $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey(), 300);
            $this->logFailure($email);

            throw ValidationException::withMessages([
                'email' => 'These credentials do not match an active account.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /** Record why a login failed (never the password) so it can be diagnosed from the server logs. */
    private function logFailure(string $email): void
    {
        try {
            $admin = \App\Models\Admin::with('role')->whereRaw('lower(email) = ?', [$email])->first();
            $reason = match (true) {
                ! $admin => 'no admin with this email',
                ! $admin->is_active => 'account is inactive',
                ! \Illuminate\Support\Facades\Hash::check((string) $this->input('password'), $admin->password) => 'password does not match (length '.strlen((string) $this->input('password')).')',
                default => 'credentials valid but guard rejected login',
            };
            logger()->warning('Admin login failed: '.$reason, ['email' => $email, 'ip' => $this->ip(), 'db' => config('database.default')]);
        } catch (\Throwable $e) {
            logger()->warning('Admin login failed; diagnosis error: '.$e->getMessage());
        }
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
