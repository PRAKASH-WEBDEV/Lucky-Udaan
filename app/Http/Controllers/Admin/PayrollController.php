<?php

namespace App\Http\Controllers\Admin;

use App\Models\Employee;
use App\Models\Payroll;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PayrollController extends ResourceController
{
    protected string $model = Payroll::class;

    protected string $route = 'admin.payroll';

    protected string $title = 'Payroll';

    protected string $singular = 'Payroll entry';

    protected string $icon = 'wallet2';

    protected array $searchable = ['month', 'payment_ref'];

    protected array $with = ['employee'];

    protected string $sort = 'month';

    protected function fields(?Model $item): array
    {
        $employeeId = request('employee_id', $item?->employee_id);
        $money = fn ($name, $label, $help = null) => ['name' => $name, 'label' => $label, 'type' => 'number', 'col' => 3, 'rules' => 'nullable|numeric|min:0|max:9999999', 'help' => $help];

        return [
            ['name' => 'employee_id', 'label' => 'Employee', 'type' => 'select', 'col' => 6, 'empty' => 'Select employee', 'options' => Employee::options(), 'rules' => 'required|exists:employees,id'],
            ['name' => 'month', 'label' => 'Salary month', 'col' => 6, 'default' => now()->subMonthNoOverflow()->format('Y-m'), 'placeholder' => 'YYYY-MM',
                'rules' => ['required', 'date_format:Y-m', Rule::unique('payrolls')->where('employee_id', $employeeId)->ignore($item?->getKey())]],
            ['name' => 'working_days', 'label' => 'Working days', 'type' => 'number', 'col' => 3, 'step' => 1, 'rules' => 'required|integer|min:1|max:31', 'default' => 26],
            ['name' => 'present_days', 'label' => 'Paid days', 'type' => 'number', 'col' => 3, 'step' => '0.5', 'rules' => 'required|numeric|min:0|max:31', 'default' => 26, 'help' => 'Present + paid leave + holidays.'],
            ['type' => 'section', 'label' => 'Earnings (₹, for the month)', 'help' => 'Leave all three blank to calculate from the employee’s salary structure, pro-rated for paid days.'],
            $money('basic', 'Basic'),
            $money('hra', 'HRA'),
            $money('allowances', 'Allowances'),
            ['type' => 'section', 'label' => 'Deductions (₹)', 'help' => 'Blank PF / ESI / tax are calculated from the salary structure.'],
            $money('pf', 'PF'),
            $money('esi', 'ESI'),
            $money('tax', 'Professional tax / TDS'),
            $money('other_deductions', 'Other deductions', 'Advance, loan, etc.'),
            ['type' => 'section', 'label' => 'Payment'],
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'col' => 4, 'options' => Payroll::STATUSES, 'rules' => 'required|in:draft,processed,paid', 'default' => 'draft'],
            ['name' => 'paid_on', 'label' => 'Paid on', 'type' => 'date', 'col' => 4, 'rules' => 'nullable|date|required_if:status,paid'],
            ['name' => 'payment_ref', 'label' => 'Payment reference / UTR', 'col' => 4, 'rules' => 'nullable|string|max:120'],
            ['name' => 'notes', 'label' => 'Notes', 'type' => 'textarea', 'rows' => 2, 'rules' => 'nullable|string|max:1000'],
        ];
    }

    protected function prepare(array $data, ?Model $item, Request $request): array
    {
        return static::calculate($data, Employee::with('salaryStructure')->find($data['employee_id']));
    }

    /** Fill blank earnings/deductions from the employee's structure and compute totals. */
    public static function calculate(array $data, ?Employee $employee): array
    {
        $s = $employee?->salaryStructure;
        $ratio = min(1, (float) $data['present_days'] / max(1, (int) $data['working_days']));

        if (blank($data['basic'] ?? null) && blank($data['hra'] ?? null) && blank($data['allowances'] ?? null) && $employee) {
            if ($employee->monthly_salary || ! $s) {
                $gross = (float) $employee->monthlyGross();
                $data['basic'] = $gross * 0.5;
                $data['hra'] = $gross * 0.2;
                $data['allowances'] = $gross * 0.3;
            } else {
                $data['basic'] = (float) $s->basic;
                $data['hra'] = (float) $s->hra;
                $data['allowances'] = (float) $s->conveyance + (float) $s->other_allowance;
            }
            foreach (['basic', 'hra', 'allowances'] as $k) {
                $data[$k] = round($data[$k] * $ratio, 2);
            }
        }
        foreach (['basic', 'hra', 'allowances', 'other_deductions'] as $k) {
            $data[$k] = (float) ($data[$k] ?? 0);
        }
        $data['gross'] = round($data['basic'] + $data['hra'] + $data['allowances'], 2);

        $data['pf'] = blank($data['pf'] ?? null) ? round($data['basic'] * (float) ($s->pf_percent ?? 0) / 100, 2) : (float) $data['pf'];
        $data['esi'] = blank($data['esi'] ?? null) ? round($data['gross'] * (float) ($s->esi_percent ?? 0) / 100, 2) : (float) $data['esi'];
        $data['tax'] = blank($data['tax'] ?? null) ? (float) ($s->professional_tax ?? 0) : (float) $data['tax'];
        $data['total_deductions'] = round($data['pf'] + $data['esi'] + $data['tax'] + $data['other_deductions'], 2);
        $data['net'] = round($data['gross'] - $data['total_deductions'], 2);

        return $data;
    }

    protected function filters(): array
    {
        return ['status' => ['Status', Payroll::STATUSES], 'employee_id' => ['Employee', Employee::options()]];
    }

    protected function columns(): array
    {
        return [
            ['Month', fn ($p) => $p->monthLabel(), 'month'],
            ['Employee', fn ($p) => static::titleCell($p->employee?->name ?? '—', $p->employee?->designation)],
            ['Gross', fn ($p) => inr($p->gross, true), 'gross'],
            ['Deductions', fn ($p) => inr($p->total_deductions, true)],
            ['Net pay', fn ($p) => new \Illuminate\Support\HtmlString('<strong>'.e(inr($p->net, true)).'</strong>'), 'net'],
            ['Status', fn ($p) => static::badge(Payroll::STATUSES[$p->status] ?? $p->status, match ($p->status) { 'paid' => 'success', 'processed' => 'info', default => 'warning' }), 'status'],
        ];
    }

    protected function rowActions(Model $item): array
    {
        return [['Salary slip', route('admin.payroll.slip', $item->getKey()), 'file-earmark-text']];
    }

    protected function exportColumns(): array
    {
        return [
            'Month' => 'month', 'Employee' => fn ($p) => $p->employee?->name, 'Code' => fn ($p) => $p->employee?->employee_code,
            'Working days' => 'working_days', 'Paid days' => 'present_days', 'Basic' => 'basic', 'HRA' => 'hra', 'Allowances' => 'allowances',
            'Gross' => 'gross', 'PF' => 'pf', 'ESI' => 'esi', 'Tax' => 'tax', 'Other deductions' => 'other_deductions',
            'Net' => 'net', 'Status' => 'status', 'Paid on' => fn ($p) => $p->paid_on?->format('Y-m-d'),
            'Bank account' => fn ($p) => $p->employee?->bank_account, 'IFSC' => fn ($p) => $p->employee?->ifsc,
        ];
    }
}
