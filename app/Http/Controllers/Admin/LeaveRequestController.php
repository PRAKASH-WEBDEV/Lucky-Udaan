<?php

namespace App\Http\Controllers\Admin;

use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

/** "Leave Management": leave applications and approvals. */
class LeaveRequestController extends ResourceController
{
    protected string $model = LeaveRequest::class;

    protected string $route = 'admin.leaves';

    protected string $title = 'Leave Management';

    protected string $singular = 'Leave request';

    protected string $icon = 'calendar-x';

    protected array $searchable = ['reason'];

    protected array $with = ['employee', 'leaveType'];

    protected string $sort = 'from_date';

    protected ?string $dateColumn = 'from_date';

    protected function fields(?Model $item): array
    {
        $balance = $item?->exists ? $this->balanceText($item) : null;

        return array_values(array_filter([
            ['name' => 'employee_id', 'label' => 'Employee', 'type' => 'select', 'col' => 6, 'empty' => 'Select employee', 'options' => Employee::options(), 'rules' => 'required|exists:employees,id'],
            ['name' => 'leave_type_id', 'label' => 'Leave type', 'type' => 'select', 'col' => 6, 'empty' => 'Select type', 'options' => LeaveType::options(), 'rules' => 'required|exists:leave_types,id'],
            ['name' => 'from_date', 'label' => 'From', 'type' => 'date', 'col' => 4, 'rules' => 'required|date'],
            ['name' => 'to_date', 'label' => 'To', 'type' => 'date', 'col' => 4, 'rules' => 'required|date|after_or_equal:from_date'],
            ['name' => 'days', 'label' => 'Days', 'type' => 'number', 'col' => 4, 'step' => '0.5', 'rules' => 'nullable|numeric|min:0.5|max:365', 'help' => 'Blank = calendar days between the dates. Use 0.5 for half day.'],
            ['name' => 'reason', 'label' => 'Reason', 'type' => 'textarea', 'rows' => 2, 'rules' => 'nullable|string|max:1000'],
            $balance ? ['type' => 'static', 'label' => 'Leave used this year', 'value' => fn () => $balance] : null,
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'col' => 4, 'options' => LeaveRequest::STATUSES, 'rules' => 'required|in:pending,approved,rejected', 'default' => 'pending'],
            ['name' => 'admin_note', 'label' => 'Note from approver', 'col' => 8, 'type' => 'textarea', 'rows' => 2, 'rules' => 'nullable|string|max:1000'],
        ]));
    }

    protected function prepare(array $data, ?Model $item, Request $request): array
    {
        if (empty($data['days'])) {
            $data['days'] = Carbon::parse($data['from_date'])->diffInDays(Carbon::parse($data['to_date'])) + 1;
        }

        return $data;
    }

    /** Approved days of this type used by the employee in the current year vs quota. */
    private function balanceText(LeaveRequest $r): string
    {
        $used = LeaveRequest::where('employee_id', $r->employee_id)->where('leave_type_id', $r->leave_type_id)
            ->where('status', 'approved')->whereYear('from_date', now()->year)->sum('days');
        $quota = $r->leaveType?->annual_quota;

        return rtrim(rtrim(number_format((float) $used, 1), '0'), '.').' days used'.($quota !== null ? " of {$quota} ({$r->leaveType->name})" : '');
    }

    protected function filters(): array
    {
        return [
            'status' => ['Status', LeaveRequest::STATUSES],
            'employee_id' => ['Employee', Employee::options()],
            'leave_type_id' => ['Leave type', LeaveType::options()],
        ];
    }

    protected function columns(): array
    {
        return [
            ['Employee', fn ($r) => static::titleCell($r->employee?->name ?? '—', $r->reason)],
            ['Type', fn ($r) => $r->leaveType?->name ?? '—'],
            ['Dates', fn ($r) => $r->from_date->format('d M').($r->to_date->ne($r->from_date) ? ' – '.$r->to_date->format('d M Y') : ' '.$r->from_date->format('Y')), 'from_date'],
            ['Days', fn ($r) => rtrim(rtrim(number_format($r->days, 1), '0'), '.'), 'days'],
            ['Status', fn ($r) => static::badge(LeaveRequest::STATUSES[$r->status] ?? $r->status, static::statusTone($r->status)), 'status'],
        ];
    }

    protected function exportColumns(): array
    {
        return [
            'Employee' => fn ($r) => $r->employee?->name, 'Type' => fn ($r) => $r->leaveType?->name,
            'From' => fn ($r) => $r->from_date->format('Y-m-d'), 'To' => fn ($r) => $r->to_date->format('Y-m-d'),
            'Days' => 'days', 'Status' => 'status', 'Reason' => 'reason',
        ];
    }
}
