<?php

namespace App\Http\Controllers\Admin;

use App\Models\LeaveType;
use Illuminate\Database\Eloquent\Model;

/** "Leave Quotas": leave types and their yearly allowance. */
class LeaveTypeController extends ResourceController
{
    protected string $model = LeaveType::class;

    protected string $route = 'admin.leave-types';

    protected string $title = 'Leave Quotas';

    protected string $singular = 'Leave type';

    protected string $icon = 'calendar2-range';

    protected array $searchable = ['name', 'code'];

    protected string $sort = 'name';

    protected string $direction = 'asc';

    protected function fields(?Model $item): array
    {
        return [
            ['name' => 'name', 'label' => 'Leave type', 'col' => 8, 'rules' => 'required|string|max:80', 'datalist' => ['Casual Leave', 'Sick Leave', 'Earned Leave', 'Maternity Leave', 'Paternity Leave', 'Leave Without Pay']],
            ['name' => 'code', 'label' => 'Short code', 'col' => 4, 'rules' => 'nullable|string|max:10', 'placeholder' => 'CL'],
            ['name' => 'annual_quota', 'label' => 'Days per year', 'type' => 'number', 'col' => 4, 'step' => 1, 'rules' => 'required|integer|min:0|max:365', 'default' => 12],
            ['name' => 'is_paid', 'label' => 'Paid leave', 'type' => 'switch', 'col' => 4, 'default' => true],
            ['name' => 'carry_forward', 'label' => 'Unused days carry forward', 'type' => 'switch', 'col' => 4],
            $this->activeField(),
        ];
    }

    protected function columns(): array
    {
        return [
            ['Leave type', fn ($t) => static::titleCell($t->name, $t->code), 'name'],
            ['Quota', fn ($t) => $t->annual_quota.' days / year', 'annual_quota'],
            ['Paid', fn ($t) => static::badge($t->is_paid ? 'Paid' : 'Unpaid', $t->is_paid ? 'success' : 'secondary')],
            ['Carry forward', fn ($t) => $t->carry_forward ? 'Yes' : 'No'],
        ];
    }
}
