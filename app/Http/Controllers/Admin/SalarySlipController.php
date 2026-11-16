<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Database\Eloquent\Builder;

/** Salary slips: processed and paid payroll entries, each with a printable slip. */
class SalarySlipController extends PayrollController
{
    protected string $route = 'admin.salary-slips';

    protected string $title = 'Salary Slips';

    protected string $singular = 'Salary slip';

    protected string $icon = 'file-earmark-text';

    protected bool $canCreate = false;

    protected bool $canEdit = false;

    protected bool $canDelete = false;

    protected function newQuery(): Builder
    {
        return parent::newQuery()->whereIn('status', ['processed', 'paid']);
    }
}
