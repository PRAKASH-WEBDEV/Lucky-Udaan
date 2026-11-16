<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\ExpenseClaim;
use App\Models\Holiday;
use App\Models\HrViolation;
use App\Models\LeaveRequest;
use App\Models\OfferLetter;
use App\Models\Payroll;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

/** HR overview, monthly payroll run and printable letterhead documents. */
class HrController extends Controller
{
    public function overview()
    {
        $today = today();
        $active = Employee::where('is_active', true)->count();
        $marked = Attendance::whereDate('date', $today)->get();
        $onLeave = LeaveRequest::where('status', 'approved')->whereDate('from_date', '<=', $today)->whereDate('to_date', '>=', $today)->count();
        $month = now()->subMonthNoOverflow()->format('Y-m');

        return view('admin.hr.overview', [
            'stats' => [
                ['Active employees', $active, 'people', 'primary', Employee::where('is_active', false)->count().' former', route('admin.employees.index')],
                ['Present today', $marked->whereIn('status', ['present', 'late', 'half_day'])->count(), 'calendar-check', 'success', $marked->where('status', 'late')->count().' late · '.max(0, $active - $marked->count() - $onLeave).' not marked', route('admin.attendance.index', ['from' => $today->format('Y-m-d'), 'to' => $today->format('Y-m-d')])],
                ['On leave today', $onLeave, 'calendar-x', 'info', LeaveRequest::where('status', 'pending')->count().' requests pending', route('admin.leaves.index', ['status' => 'pending'])],
                ['Payroll '.Carbon::createFromFormat('Y-m', $month)->format('M Y'), inr(Payroll::where('month', $month)->sum('net')), 'wallet2', 'warning', Payroll::where('month', $month)->where('status', '!=', 'paid')->count().' not yet paid', route('admin.payroll.index')],
                ['Expense claims', ExpenseClaim::where('status', 'pending')->count(), 'receipt', 'danger', inr(ExpenseClaim::where('status', 'approved')->sum('amount')).' approved, unpaid', route('admin.expense-claims.index', ['status' => 'pending'])],
                ['Open violations', HrViolation::where('status', 'open')->count(), 'exclamation-octagon', 'danger', 'this month: '.HrViolation::whereYear('date', now()->year)->whereMonth('date', now()->month)->count(), route('admin.violations.index', ['status' => 'open'])],
            ],
            'pendingLeaves' => LeaveRequest::with(['employee', 'leaveType'])->where('status', 'pending')->orderBy('from_date')->limit(6)->get(),
            'holidays' => Holiday::where('is_active', true)->whereDate('date', '>=', $today)->orderBy('date')->limit(6)->get(),
            'birthdays' => Employee::where('is_active', true)->whereNotNull('dob')->get()
                ->filter(fn ($e) => $e->dob->copy()->year($today->year)->between($today, $today->copy()->addDays(30)))
                ->sortBy(fn ($e) => $e->dob->format('md'))->take(6),
            'payrollMonth' => $month,
        ]);
    }

    /** Create draft payroll entries for every active employee for a month, using attendance. */
    public function generatePayroll(Request $request)
    {
        $data = $request->validate(['month' => 'required|date_format:Y-m', 'working_days' => 'required|integer|min:1|max:31']);
        $start = Carbon::createFromFormat('Y-m', $data['month'])->startOfMonth();
        $end = $start->copy()->endOfMonth();
        $created = 0;

        foreach (Employee::with('salaryStructure')->where('is_active', true)->get() as $employee) {
            if (Payroll::where('employee_id', $employee->id)->where('month', $data['month'])->exists()) {
                continue;
            }
            $rows = Attendance::where('employee_id', $employee->id)->whereBetween('date', [$start, $end])->get();
            // No attendance recorded → assume full month; otherwise count paid statuses.
            $paid = $rows->isEmpty()
                ? (float) $data['working_days']
                : $rows->whereIn('status', ['present', 'late', 'leave', 'holiday'])->count() + 0.5 * $rows->where('status', 'half_day')->count();

            Payroll::create(PayrollController::calculate([
                'employee_id' => $employee->id, 'month' => $data['month'], 'status' => 'draft',
                'working_days' => $data['working_days'], 'present_days' => min($paid, (float) $data['working_days']),
            ], $employee));
            $created++;
        }

        return redirect()->route('admin.payroll.index')->with('success', $created
            ? "Draft payroll created for {$created} employee(s). Review and mark as paid."
            : 'Payroll for this month already exists for all active employees.');
    }

    public function slip(int $id)
    {
        $payroll = Payroll::with('employee.salaryStructure')->findOrFail($id);

        return view('admin.hr.slip', ['p' => $payroll, 'docTitle' => 'Salary slip – '.$payroll->monthLabel()]);
    }

    public function offerLetter(int $id)
    {
        return view('admin.hr.offer-letter', ['o' => OfferLetter::findOrFail($id), 'docTitle' => 'Offer letter']);
    }

    public function letterhead()
    {
        return view('admin.hr.letterhead', ['docTitle' => 'Letterhead']);
    }
}
