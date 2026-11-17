<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/** Staff portal: employee sign-in and self check-in / check-out (attendance source "web"). */
class PortalController extends Controller
{
    public function showLogin()
    {
        return view('staff.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:150'],
            'password' => ['required', 'string', 'max:200'],
        ]);

        $email = Str::lower(trim((string) $request->input('email')));
        $key = 'staff-login|'.$email.'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages([
                'email' => 'Too many login attempts. Please try again in '.ceil(RateLimiter::availableIn($key) / 60).' minute(s).',
            ]);
        }

        $credentials = ['email' => $email, 'password' => (string) $request->input('password'), 'is_active' => true, 'portal_access' => true];
        if (! Auth::guard('employee')->attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::hit($key, 300);
            throw ValidationException::withMessages([
                'email' => 'These details do not match an employee with portal access. Please contact the office.',
            ]);
        }

        RateLimiter::clear($key);
        $request->session()->regenerate();
        Auth::guard('employee')->user()->forceFill(['last_login_at' => now()])->save();

        return redirect()->intended(route('staff.dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::guard('employee')->logout();
        $request->session()->regenerateToken();

        return redirect()->route('staff.login')->with('success', 'You have been signed out.');
    }

    public function dashboard(Request $request)
    {
        $employee = $this->employee($request);
        if (! $employee) {
            return redirect()->route('staff.login');
        }

        $today = $employee->attendances()->whereDate('date', today())->first();
        $month = $employee->attendances()
            ->whereBetween('date', [today()->startOfMonth(), today()->endOfMonth()])
            ->orderByDesc('date')->get();

        return view('staff.dashboard', [
            'employee' => $employee,
            'today' => $today,
            'month' => $month,
            'counts' => $month->countBy('status'),
        ]);
    }

    public function checkIn(Request $request)
    {
        $employee = $this->employee($request);
        if (! $employee) {
            return redirect()->route('staff.login');
        }

        $record = $employee->attendances()->whereDate('date', today())->first();
        if ($record?->check_in) {
            return back()->with('error', 'You have already checked in today at '.substr($record->check_in, 0, 5).'.');
        }
        if ($record && in_array($record->status, ['leave', 'holiday'], true)) {
            return back()->with('error', 'Today is marked as “'.Attendance::STATUSES[$record->status].'” for you. Please contact the office.');
        }

        $now = now();
        $lateAfter = Carbon::createFromTimeString(setting('hr_office_start', '10:00'))->addMinutes((int) setting('hr_grace_minutes', 15));
        $data = [
            'check_in' => $now->format('H:i:s'),
            'status' => $now->gt($lateAfter) ? 'late' : 'present',
            'source' => 'web',
        ];

        $record ? $record->update($data) : $employee->attendances()->create($data + ['date' => today()->toDateString()]);

        return back()->with('success', 'Checked in at '.$now->format('h:i A').($data['status'] === 'late' ? ' (marked late).' : '. Have a good day!'));
    }

    public function checkOut(Request $request)
    {
        $employee = $this->employee($request);
        if (! $employee) {
            return redirect()->route('staff.login');
        }

        $record = $employee->attendances()->whereDate('date', today())->first();
        if (! $record?->check_in) {
            return back()->with('error', 'Please check in first.');
        }
        if ($record->check_out) {
            return back()->with('error', 'You have already checked out today at '.substr($record->check_out, 0, 5).'.');
        }

        $now = now();
        $hours = Carbon::createFromTimeString($record->check_in)->diffInMinutes($now) / 60;
        $data = ['check_out' => $now->format('H:i:s')];
        if (in_array($record->status, ['present', 'late'], true) && $hours < (float) setting('hr_half_day_hours', 6)) {
            $data['status'] = 'half_day';
        }
        $record->update($data);

        return back()->with('success', 'Checked out at '.$now->format('h:i A').' — '.number_format($hours, 1).' hours today.');
    }

    /** The signed-in employee, or null (and signed out) if their access was revoked meanwhile. */
    private function employee(Request $request): ?Employee
    {
        /** @var Employee|null $employee */
        $employee = Auth::guard('employee')->user();
        if ($employee && (! $employee->is_active || ! $employee->portal_access)) {
            Auth::guard('employee')->logout();
            $request->session()->regenerateToken();
            session()->flash('error', 'Your portal access has been turned off. Please contact the office.');

            return null;
        }

        return $employee;
    }
}
