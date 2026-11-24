<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Holiday;
use App\Models\LeaveType;
use App\Models\StaffNotice;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
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
            'notices' => $this->noticeFeed($employee)->take(3),
            'holidays' => Holiday::where('is_active', true)->whereDate('date', '>=', today())->orderBy('date')->limit(3)->get(),
        ]);
    }

    public function leave(Request $request)
    {
        $employee = $this->employee($request);
        if (! $employee) {
            return redirect()->route('staff.login');
        }

        $requests = $employee->leaveRequests()->with('leaveType')->orderByDesc('from_date')->limit(50)->get();
        $types = LeaveType::where('is_active', true)->orderBy('name')->get();
        $used = $employee->leaveRequests()->where('status', 'approved')->whereYear('from_date', now()->year)
            ->selectRaw('leave_type_id, sum(days) as used')->groupBy('leave_type_id')->pluck('used', 'leave_type_id');

        return view('staff.leave', compact('employee', 'requests', 'types', 'used'));
    }

    public function applyLeave(Request $request)
    {
        $employee = $this->employee($request);
        if (! $employee) {
            return redirect()->route('staff.login');
        }

        $data = $request->validate([
            'leave_type_id' => ['required', Rule::exists('leave_types', 'id')->where('is_active', true)],
            'from_date' => 'required|date|after_or_equal:'.today()->subDays(7)->toDateString(),
            'to_date' => 'required|date|after_or_equal:from_date|before_or_equal:'.today()->addYear()->toDateString(),
            'half_day' => 'nullable|boolean',
            'reason' => 'required|string|max:1000',
        ], [], ['leave_type_id' => 'leave type', 'from_date' => 'from date', 'to_date' => 'to date']);

        $from = Carbon::parse($data['from_date']);
        $to = Carbon::parse($data['to_date']);
        $days = $request->boolean('half_day') && $from->eq($to) ? 0.5 : $from->diffInDays($to) + 1;

        $overlap = $employee->leaveRequests()->whereIn('status', ['pending', 'approved'])
            ->whereDate('from_date', '<=', $to)->whereDate('to_date', '>=', $from)->exists();
        if ($overlap) {
            return back()->withInput()->withErrors(['from_date' => 'You already have a leave request for some of these dates.']);
        }

        $leave = $employee->leaveRequests()->create([
            'leave_type_id' => $data['leave_type_id'],
            'from_date' => $from->toDateString(),
            'to_date' => $to->toDateString(),
            'days' => $days,
            'reason' => $data['reason'],
            'status' => 'pending',
        ]);

        AdminNotification::notify('leave', 'Leave request from '.$employee->name,
            $from->format('d M').($to->ne($from) ? ' – '.$to->format('d M') : '').' · '.$data['reason'],
            route('admin.leaves.edit', $leave->id));

        return redirect()->route('staff.leave')->with('success', 'Leave request sent. You will see the decision here once the office reviews it.');
    }

    public function cancelLeave(Request $request, int $id)
    {
        $employee = $this->employee($request);
        if (! $employee) {
            return redirect()->route('staff.login');
        }

        $leave = $employee->leaveRequests()->where('status', 'pending')->findOrFail($id);
        $leave->delete();

        return back()->with('success', 'Leave request cancelled.');
    }

    public function notices(Request $request)
    {
        $employee = $this->employee($request);
        if (! $employee) {
            return redirect()->route('staff.login');
        }

        return view('staff.notices', [
            'employee' => $employee,
            'notices' => $this->noticeFeed($employee),
            'holidays' => Holiday::where('is_active', true)->whereDate('date', '>=', today())->orderBy('date')->limit(10)->get(),
        ]);
    }

    /** Office notices plus this employee's leave decisions, newest first. */
    private function noticeFeed(Employee $employee): Collection
    {
        $notices = StaffNotice::where('is_active', true)->latest()->limit(30)->get()->map(fn ($n) => (object) [
            'icon' => $n->priority === 'important' ? 'exclamation-triangle' : 'megaphone',
            'tone' => $n->priority === 'important' ? 'danger' : 'primary',
            'title' => $n->title,
            'message' => $n->message,
            'at' => $n->created_at,
        ]);

        $decisions = $employee->leaveRequests()->whereIn('status', ['approved', 'rejected'])->where('updated_at', '>=', now()->subDays(60))
            ->latest('updated_at')->get()->map(fn ($l) => (object) [
                'icon' => $l->status === 'approved' ? 'check-circle' : 'x-circle',
                'tone' => $l->status === 'approved' ? 'success' : 'danger',
                'title' => 'Your leave for '.$l->from_date->format('d M').($l->to_date->ne($l->from_date) ? ' – '.$l->to_date->format('d M') : '').' was '.$l->status,
                'message' => $l->admin_note,
                'at' => $l->updated_at,
            ]);

        return $notices->concat($decisions)->sortByDesc('at')->values();
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
