<?php

namespace App\Http\Controllers\Admin;

use App\Models\SalaryStructure;
use Illuminate\Database\Eloquent\Model;

class SalaryStructureController extends ResourceController
{
    protected string $model = SalaryStructure::class;

    protected string $route = 'admin.salary-structures';

    protected string $title = 'Salary Structures';

    protected string $singular = 'Salary structure';

    protected string $icon = 'diagram-2';

    protected array $searchable = ['name'];

    protected string $sort = 'name';

    protected string $direction = 'asc';

    protected function fields(?Model $item): array
    {
        $money = fn ($name, $label) => ['name' => $name, 'label' => $label, 'type' => 'number', 'col' => 3, 'rules' => 'nullable|numeric|min:0|max:9999999', 'default' => 0];

        return [
            ['name' => 'name', 'label' => 'Structure name', 'rules' => 'required|string|max:120', 'placeholder' => 'e.g. Field Coordinator – Grade B'],
            ['type' => 'section', 'label' => 'Monthly earnings (₹)'],
            $money('basic', 'Basic'),
            $money('hra', 'HRA'),
            $money('conveyance', 'Conveyance'),
            $money('other_allowance', 'Other allowance'),
            ['type' => 'section', 'label' => 'Deductions'],
            ['name' => 'pf_percent', 'label' => 'PF (% of basic)', 'type' => 'number', 'col' => 4, 'rules' => 'nullable|numeric|min:0|max:100', 'default' => 12],
            ['name' => 'esi_percent', 'label' => 'ESI (% of gross)', 'type' => 'number', 'col' => 4, 'rules' => 'nullable|numeric|min:0|max:100', 'default' => 0],
            ['name' => 'professional_tax', 'label' => 'Professional tax (₹ / month)', 'type' => 'number', 'col' => 4, 'rules' => 'nullable|numeric|min:0|max:99999', 'default' => 0],
            ['name' => 'notes', 'label' => 'Notes', 'type' => 'textarea', 'rows' => 2, 'rules' => 'nullable|string|max:1000'],
            $this->activeField(),
        ];
    }

    protected function prepare(array $data, ?Model $item, \Illuminate\Http\Request $request): array
    {
        foreach (['basic', 'hra', 'conveyance', 'other_allowance', 'pf_percent', 'esi_percent', 'professional_tax'] as $k) {
            $data[$k] = $data[$k] ?? 0;
        }

        return $data;
    }

    protected function columns(): array
    {
        return [
            ['Structure', fn ($s) => static::titleCell($s->name, $s->notes), 'name'],
            ['Basic', fn ($s) => inr($s->basic), 'basic'],
            ['Gross / month', fn ($s) => inr($s->gross())],
            ['PF / ESI', fn ($s) => (float) $s->pf_percent.'% / '.(float) $s->esi_percent.'%'],
            ['Employees', fn ($s) => $s->employees()->count()],
        ];
    }
}
