<?php

namespace App\Http\Controllers\Admin;

use App\Models\Employee;
use App\Models\SalaryStructure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class EmployeeController extends ResourceController
{
    protected string $model = Employee::class;

    protected string $route = 'admin.employees';

    protected string $title = 'Employees';

    protected string $singular = 'Employee';

    protected string $icon = 'person-vcard';

    protected string $uploadDir = 'employees';

    protected array $searchable = ['name', 'employee_code', 'email', 'phone', 'designation', 'department'];

    protected array $with = ['salaryStructure'];

    protected string $sort = 'name';

    protected string $direction = 'asc';

    protected function fields(?Model $item): array
    {
        return [
            ['type' => 'section', 'label' => 'Personal details'],
            ['name' => 'name', 'label' => 'Full name', 'col' => 8, 'rules' => 'required|string|max:150'],
            ['name' => 'employee_code', 'label' => 'Employee code', 'col' => 4, 'rules' => ['nullable', 'string', 'max:30', Rule::unique('employees', 'employee_code')->ignore($item?->getKey())], 'placeholder' => 'LUF-001'],
            ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'col' => 4, 'rules' => 'nullable|email|max:150'],
            ['name' => 'phone', 'label' => 'Phone', 'col' => 4, 'rules' => 'nullable|string|max:30'],
            ['name' => 'gender', 'label' => 'Gender', 'type' => 'select', 'col' => 2, 'empty' => '—', 'options' => ['male' => 'Male', 'female' => 'Female', 'other' => 'Other'], 'rules' => 'nullable|in:male,female,other'],
            ['name' => 'dob', 'label' => 'Date of birth', 'type' => 'date', 'col' => 2, 'rules' => 'nullable|date|before:today'],
            ['name' => 'address', 'label' => 'Address', 'type' => 'textarea', 'rows' => 2, 'rules' => 'nullable|string|max:500'],
            ['type' => 'section', 'label' => 'Job'],
            ['name' => 'designation', 'label' => 'Designation', 'col' => 4, 'rules' => 'nullable|string|max:120', 'datalist' => ['Programme Manager', 'Field Coordinator', 'Accountant', 'Teacher', 'Counsellor', 'Office Assistant']],
            ['name' => 'department', 'label' => 'Department', 'col' => 4, 'rules' => 'nullable|string|max:120', 'datalist' => ['Programmes', 'Administration', 'Finance', 'Education', 'Healthcare', 'Field Operations']],
            ['name' => 'employment_type', 'label' => 'Employment type', 'type' => 'select', 'col' => 4, 'options' => Employee::TYPES, 'rules' => 'required|in:'.implode(',', array_keys(Employee::TYPES)), 'default' => 'full_time'],
            ['name' => 'joining_date', 'label' => 'Joining date', 'type' => 'date', 'col' => 4, 'rules' => 'nullable|date'],
            ['name' => 'salary_structure_id', 'label' => 'Salary structure', 'type' => 'select', 'col' => 4, 'empty' => '— None —', 'options' => SalaryStructure::orderBy('name')->pluck('name', 'id')->all(), 'rules' => 'nullable|exists:salary_structures,id'],
            ['name' => 'monthly_salary', 'label' => 'Monthly gross salary (₹)', 'type' => 'number', 'col' => 4, 'rules' => 'nullable|numeric|min:0|max:9999999', 'help' => 'Leave blank to use the salary structure total.'],
            ['name' => 'device_user_id', 'label' => 'Attendance device user ID', 'col' => 4, 'rules' => 'nullable|string|max:40', 'help' => 'ID enrolled on the biometric machine.'],
            ['type' => 'section', 'label' => 'Bank & tax (for salary)'],
            ['name' => 'bank_name', 'label' => 'Bank', 'col' => 4, 'rules' => 'nullable|string|max:120'],
            ['name' => 'bank_account', 'label' => 'Account number', 'col' => 4, 'rules' => 'nullable|string|max:40'],
            ['name' => 'ifsc', 'label' => 'IFSC', 'col' => 2, 'rules' => 'nullable|string|max:20'],
            ['name' => 'pan', 'label' => 'PAN', 'col' => 2, 'rules' => 'nullable|string|max:20'],
            $this->imageField('photo', 'Photo'),
            ['name' => 'is_active', 'label' => 'Currently employed', 'type' => 'switch', 'side' => true, 'default' => true],
        ];
    }

    protected function filters(): array
    {
        return ['employment_type' => ['Type', Employee::TYPES]];
    }

    protected function columns(): array
    {
        return [
            ['', fn ($e) => static::thumb($e->photo, $e->name)],
            ['Employee', fn ($e) => static::titleCell($e->name, trim($e->employee_code.' · '.$e->designation, ' ·')), 'name'],
            ['Department', fn ($e) => $e->department ?: '—', 'department'],
            ['Type', fn ($e) => static::badge(Employee::TYPES[$e->employment_type] ?? $e->employment_type, 'info')],
            ['Monthly gross', fn ($e) => $e->monthlyGross() ? inr($e->monthlyGross()) : '—'],
            ['Joined', fn ($e) => $e->joining_date?->format('d M Y') ?? '—', 'joining_date'],
        ];
    }

    protected function exportColumns(): array
    {
        return [
            'Code' => 'employee_code', 'Name' => 'name', 'Email' => 'email', 'Phone' => 'phone',
            'Designation' => 'designation', 'Department' => 'department',
            'Type' => fn ($e) => Employee::TYPES[$e->employment_type] ?? $e->employment_type,
            'Joining date' => fn ($e) => $e->joining_date?->format('Y-m-d'),
            'Monthly gross' => fn ($e) => $e->monthlyGross(),
            'Bank' => 'bank_name', 'Account' => 'bank_account', 'IFSC' => 'ifsc', 'PAN' => 'pan',
            'Active' => fn ($e) => $e->is_active ? 'Yes' : 'No',
        ];
    }
}
