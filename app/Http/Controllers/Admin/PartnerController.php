<?php

namespace App\Http\Controllers\Admin;

use App\Models\Partner;
use Illuminate\Database\Eloquent\Model;

class PartnerController extends ResourceController
{
    protected string $model = Partner::class;

    protected string $route = 'admin.partners';

    protected string $title = 'Partners';

    protected string $singular = 'Partner';

    protected string $icon = 'building';

    protected string $uploadDir = 'partners';

    protected array $searchable = ['name', 'type'];

    protected string $sort = 'sort_order';

    protected string $direction = 'asc';

    protected function fields(?Model $item): array
    {
        return [
            ['name' => 'name', 'label' => 'Organisation name', 'rules' => 'required|string|max:150'],
            ['name' => 'type', 'label' => 'Partner type', 'col' => 6, 'rules' => 'nullable|string|max:40', 'datalist' => ['Government', 'CSR Partner', 'Educational Institution', 'NGO', 'Community Partner']],
            ['name' => 'url', 'label' => 'Website', 'type' => 'url', 'col' => 6, 'rules' => 'nullable|url|max:255'],
            $this->imageField('logo', 'Logo', 'Transparent PNG or WebP works best.'),
            $this->sortField(),
            $this->activeField(),
        ];
    }

    protected function columns(): array
    {
        return [
            ['', fn ($p) => static::thumb($p->logo, $p->name)],
            ['Partner', fn ($p) => static::titleCell($p->name, $p->type), 'name'],
            ['Order', fn ($p) => $p->sort_order, 'sort_order'],
        ];
    }
}
