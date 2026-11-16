<?php

namespace App\Http\Controllers\Admin;

use App\Models\MembershipPlan;
use Illuminate\Database\Eloquent\Model;

class MembershipPlanController extends ResourceController
{
    protected string $model = MembershipPlan::class;

    protected string $route = 'admin.membership-plans';

    protected string $title = 'Membership Plans';

    protected string $singular = 'Plan';

    protected string $icon = 'card-checklist';

    protected array $searchable = ['name', 'benefits'];

    protected string $sort = 'sort_order';

    protected string $direction = 'asc';

    protected function fields(?Model $item): array
    {
        return [
            ['name' => 'name', 'label' => 'Plan name', 'col' => 6, 'rules' => 'required|string|max:120', 'datalist' => ['General', 'Student', 'Life', 'Patron']],
            ['name' => 'fee', 'label' => 'Fee (₹)', 'type' => 'number', 'col' => 3, 'rules' => 'required|numeric|min:0|max:9999999', 'default' => 0],
            ['name' => 'duration_months', 'label' => 'Validity (months)', 'type' => 'number', 'col' => 3, 'step' => 1, 'rules' => 'required|integer|min:0|max:1200', 'default' => 12, 'help' => '0 = lifetime'],
            ['name' => 'benefits', 'label' => 'Benefits (one per line)', 'type' => 'textarea', 'rows' => 5, 'rules' => 'nullable|string|max:2000'],
            $this->sortField(),
            $this->activeField(),
        ];
    }

    protected function columns(): array
    {
        return [
            ['Plan', fn ($p) => static::titleCell($p->name, $p->benefits), 'name'],
            ['Fee', fn ($p) => (float) $p->fee ? inr($p->fee) : 'Free', 'fee'],
            ['Validity', fn ($p) => $p->duration_months ? $p->duration_months.' months' : 'Lifetime', 'duration_months'],
            ['Members', fn ($p) => \App\Models\Member::where('membership_type', \Illuminate\Support\Str::slug($p->name, '_'))->count()],
        ];
    }
}
