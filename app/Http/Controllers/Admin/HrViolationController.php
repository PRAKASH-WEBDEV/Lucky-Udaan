<?php

namespace App\Http\Controllers\Admin;

use App\Models\Employee;
use App\Models\HrViolation;
use Illuminate\Database\Eloquent\Model;

class HrViolationController extends ResourceController
{
    protected string $model = HrViolation::class;

    protected string $route = 'admin.violations';

    protected string $title = 'Violations';

    protected string $singular = 'Violation';

    protected string $icon = 'exclamation-octagon';

    protected array $searchable = ['description', 'action_taken'];

    protected array $with = ['employee'];

    protected string $sort = 'date';

    protected ?string $dateColumn = 'date';

    protected function fields(?Model $item): array
    {
        return [
            ['name' => 'employee_id', 'label' => 'Employee', 'type' => 'select', 'col' => 6, 'empty' => 'Select employee', 'options' => Employee::options(), 'rules' => 'required|exists:employees,id'],
            ['name' => 'date', 'label' => 'Date', 'type' => 'date', 'col' => 3, 'rules' => 'required|date', 'default' => today()->format('Y-m-d')],
            ['name' => 'type', 'label' => 'Type', 'type' => 'select', 'col' => 3, 'options' => HrViolation::TYPES, 'rules' => 'required|in:'.implode(',', array_keys(HrViolation::TYPES))],
            ['name' => 'description', 'label' => 'What happened', 'type' => 'textarea', 'rows' => 3, 'rules' => 'nullable|string|max:2000'],
            ['name' => 'action_taken', 'label' => 'Action taken', 'col' => 8, 'rules' => 'nullable|string|max:255'],
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'col' => 4, 'options' => HrViolation::STATUSES, 'rules' => 'required|in:open,warned,resolved', 'default' => 'open'],
        ];
    }

    protected function filters(): array
    {
        return ['type' => ['Type', HrViolation::TYPES], 'status' => ['Status', HrViolation::STATUSES], 'employee_id' => ['Employee', Employee::options()]];
    }

    protected function columns(): array
    {
        return [
            ['Date', fn ($v) => $v->date->format('d M Y'), 'date'],
            ['Employee', fn ($v) => static::titleCell($v->employee?->name ?? '—', $v->description)],
            ['Type', fn ($v) => static::badge(HrViolation::TYPES[$v->type] ?? $v->type, 'danger')],
            ['Status', fn ($v) => static::badge(HrViolation::STATUSES[$v->status] ?? $v->status, match ($v->status) { 'resolved' => 'success', 'warned' => 'info', default => 'warning' }), 'status'],
        ];
    }
}
