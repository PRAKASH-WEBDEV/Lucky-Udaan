<?php

namespace App\Http\Controllers\Admin;

use App\Models\Employee;
use App\Models\ExpenseClaim;
use Illuminate\Database\Eloquent\Model;

class ExpenseClaimController extends ResourceController
{
    protected string $model = ExpenseClaim::class;

    protected string $route = 'admin.expense-claims';

    protected string $title = 'Expense Claims';

    protected string $singular = 'Expense claim';

    protected string $icon = 'receipt';

    protected string $uploadDir = 'claims';

    protected array $searchable = ['category', 'description'];

    protected array $with = ['employee'];

    protected string $sort = 'claim_date';

    protected ?string $dateColumn = 'claim_date';

    public const CATEGORIES = ['Travel', 'Food', 'Field visit', 'Stationery', 'Medical camp', 'Communication', 'Accommodation', 'Other'];

    protected function fields(?Model $item): array
    {
        return [
            ['name' => 'employee_id', 'label' => 'Employee', 'type' => 'select', 'col' => 6, 'empty' => 'Select employee', 'options' => Employee::options(), 'rules' => 'required|exists:employees,id'],
            ['name' => 'claim_date', 'label' => 'Expense date', 'type' => 'date', 'col' => 3, 'rules' => 'required|date|before_or_equal:today', 'default' => today()->format('Y-m-d')],
            ['name' => 'amount', 'label' => 'Amount (₹)', 'type' => 'number', 'col' => 3, 'rules' => 'required|numeric|min:1|max:9999999'],
            ['name' => 'category', 'label' => 'Category', 'col' => 6, 'rules' => 'nullable|string|max:60', 'datalist' => self::CATEGORIES],
            ['name' => 'description', 'label' => 'Details', 'type' => 'textarea', 'rows' => 3, 'rules' => 'nullable|string|max:2000'],
            ['name' => 'receipt', 'label' => 'Bill / receipt', 'type' => 'file', 'mimes' => 'pdf,jpg,jpeg,png,webp', 'rules' => 'nullable', 'max' => 5120],
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'side' => true, 'options' => ExpenseClaim::STATUSES, 'rules' => 'required|in:pending,approved,rejected,paid', 'default' => 'pending'],
            ['name' => 'paid_on', 'label' => 'Reimbursed on', 'type' => 'date', 'side' => true, 'rules' => 'nullable|date|required_if:status,paid'],
        ];
    }

    protected function filters(): array
    {
        return ['status' => ['Status', ExpenseClaim::STATUSES], 'employee_id' => ['Employee', Employee::options()]];
    }

    protected function columns(): array
    {
        return [
            ['Date', fn ($c) => $c->claim_date->format('d M Y'), 'claim_date'],
            ['Employee', fn ($c) => static::titleCell($c->employee?->name ?? '—', $c->description)],
            ['Category', fn ($c) => $c->category ?: '—', 'category'],
            ['Amount', fn ($c) => inr($c->amount, true), 'amount'],
            ['Status', fn ($c) => static::badge(ExpenseClaim::STATUSES[$c->status] ?? $c->status, $c->status === 'paid' ? 'success' : static::statusTone($c->status)), 'status'],
        ];
    }

    protected function exportColumns(): array
    {
        return [
            'Date' => fn ($c) => $c->claim_date->format('Y-m-d'), 'Employee' => fn ($c) => $c->employee?->name,
            'Category' => 'category', 'Amount' => 'amount', 'Status' => 'status', 'Paid on' => fn ($c) => $c->paid_on?->format('Y-m-d'), 'Details' => 'description',
        ];
    }
}
