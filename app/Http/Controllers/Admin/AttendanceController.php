<?php

namespace App\Http\Controllers\Admin;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class AttendanceController extends ResourceController
{
    protected string $model = Attendance::class;

    protected string $route = 'admin.attendance';

    protected string $title = 'Attendance';

    protected string $singular = 'Attendance entry';

    protected string $icon = 'calendar-check';

    protected array $searchable = ['remarks'];

    protected array $with = ['employee'];

    protected string $sort = 'date';

    protected ?string $dateColumn = 'date';

    protected int $perPage = 30;

    protected function fields(?Model $item): array
    {
        $employeeId = request('employee_id', $item?->employee_id);

        return [
            ['name' => 'employee_id', 'label' => 'Employee', 'type' => 'select', 'col' => 6, 'empty' => 'Select employee', 'options' => Employee::options(), 'rules' => 'required|exists:employees,id'],
            ['name' => 'date', 'label' => 'Date', 'type' => 'date', 'col' => 6, 'default' => today()->format('Y-m-d'),
                'rules' => ['required', 'date', 'before_or_equal:today', Rule::unique('attendances')->where('employee_id', $employeeId)->ignore($item?->getKey())]],
            ['name' => 'check_in', 'label' => 'Check-in time', 'type' => 'time', 'col' => 4, 'rules' => 'nullable|date_format:H:i,H:i:s', 'default' => setting('hr_office_start', '10:00')],
            ['name' => 'check_out', 'label' => 'Check-out time', 'type' => 'time', 'col' => 4, 'rules' => 'nullable|date_format:H:i,H:i:s|after:check_in'],
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'col' => 4, 'options' => Attendance::STATUSES, 'rules' => 'required|in:'.implode(',', array_keys(Attendance::STATUSES)), 'default' => 'present',
                'help' => 'Left as “Present”, late check-ins beyond the grace time are marked “Late” automatically.'],
            ['name' => 'source', 'label' => 'Source', 'type' => 'select', 'col' => 4, 'options' => Attendance::SOURCES, 'rules' => 'required|in:manual,device,web', 'default' => 'manual'],
            ['name' => 'remarks', 'label' => 'Remarks', 'col' => 8, 'rules' => 'nullable|string|max:255', 'placeholder' => 'e.g. Field visit, forgot to punch'],
        ];
    }

    /** Apply the attendance settings: late after office start + grace. */
    protected function prepare(array $data, ?Model $item, Request $request): array
    {
        if (($data['status'] ?? null) === 'present' && ! empty($data['check_in'])) {
            $start = Carbon::createFromTimeString(setting('hr_office_start', '10:00'))->addMinutes((int) setting('hr_grace_minutes', 15));
            if (Carbon::createFromTimeString($data['check_in'])->gt($start)) {
                $data['status'] = 'late';
            }
        }

        return $data;
    }

    protected function filters(): array
    {
        return [
            'employee_id' => ['Employee', Employee::options()],
            'status' => ['Status', Attendance::STATUSES],
            'source' => ['Source', Attendance::SOURCES],
        ];
    }

    protected function columns(): array
    {
        return [
            ['Date', fn ($a) => $a->date->format('D, d M Y'), 'date'],
            ['Employee', fn ($a) => static::titleCell($a->employee?->name ?? '—', $a->remarks)],
            ['In', fn ($a) => $a->check_in ? substr($a->check_in, 0, 5) : '—'],
            ['Out', fn ($a) => $a->check_out ? substr($a->check_out, 0, 5) : '—'],
            ['Status', fn ($a) => static::badge(Attendance::STATUSES[$a->status] ?? $a->status, match ($a->status) {
                'present' => 'success', 'late', 'half_day' => 'warning', 'absent' => 'danger', default => 'info' })],
            ['Source', fn ($a) => Attendance::SOURCES[$a->source] ?? $a->source],
        ];
    }

    protected function exportColumns(): array
    {
        return [
            'Date' => fn ($a) => $a->date->format('Y-m-d'), 'Employee' => fn ($a) => $a->employee?->name,
            'Code' => fn ($a) => $a->employee?->employee_code, 'Check in' => 'check_in', 'Check out' => 'check_out',
            'Status' => fn ($a) => Attendance::STATUSES[$a->status] ?? $a->status, 'Source' => 'source', 'Remarks' => 'remarks',
        ];
    }
}
