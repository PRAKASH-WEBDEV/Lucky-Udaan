<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoginRequest;
use App\Models\Admin;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('admin.auth.login');
    }

    public function login(LoginRequest $request)
    {
        $request->authenticate();
        $request->session()->regenerate();

        /** @var Admin $admin */
        $admin = Auth::guard('admin')->user();
        $admin->forceFill(['last_login_at' => now(), 'last_login_ip' => $request->ip()])->save();

        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'You have been signed out.');
    }

    public function showForgot()
    {
        return view('admin.auth.forgot');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email|max:150']);

        $key = 'admin-reset|'.$request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return back()->withErrors(['email' => 'Too many requests. Please wait a few minutes.']);
        }
        RateLimiter::hit($key, 600);

        Password::broker('admins')->sendResetLink($request->only('email'));

        // Same message whether or not the email exists, so accounts can't be discovered.
        return back()->with('success', 'If that email belongs to an admin account, a reset link has been sent.');
    }

    public function showReset(Request $request, string $token)
    {
        return view('admin.auth.reset', ['token' => $token, 'email' => $request->query('email')]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', PasswordRule::min(8)->letters()->numbers()],
        ]);

        $status = Password::broker('admins')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (Admin $admin, string $password) {
                $admin->forceFill(['password' => $password, 'remember_token' => Str::random(60)])->save();
                event(new PasswordReset($admin));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('admin.login')->with('success', 'Password updated. Please sign in.')
            : back()->withErrors(['email' => __($status)]);
    }
}
