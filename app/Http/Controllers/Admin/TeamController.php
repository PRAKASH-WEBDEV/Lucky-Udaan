<?php

namespace App\Http\Controllers\Admin;

use App\Models\TeamMember;
use Illuminate\Database\Eloquent\Model;

class TeamController extends ResourceController
{
    protected string $model = TeamMember::class;

    protected string $route = 'admin.team';

    protected string $title = 'Team';

    protected string $singular = 'Team member';

    protected string $icon = 'people';

    protected string $uploadDir = 'team';

    protected array $searchable = ['name', 'designation'];

    protected string $sort = 'sort_order';

    protected string $direction = 'asc';

    protected function fields(?Model $item): array
    {
        return [
            ['name' => 'name', 'label' => 'Full name', 'col' => 6, 'rules' => 'required|string|max:120'],
            ['name' => 'designation', 'label' => 'Designation', 'col' => 6, 'rules' => 'nullable|string|max:120'],
            ['name' => 'group', 'label' => 'Group', 'type' => 'select', 'col' => 6, 'options' => TeamMember::GROUPS, 'rules' => 'required|in:board,team,advisor'],
            ['name' => 'email', 'label' => 'Email (optional, shown publicly)', 'type' => 'email', 'col' => 6, 'rules' => 'nullable|email|max:150'],
            ['name' => 'bio', 'label' => 'Short bio', 'type' => 'textarea', 'rows' => 4, 'rules' => 'nullable|string|max:1500'],
            ['name' => 'linkedin', 'label' => 'LinkedIn URL', 'type' => 'url', 'col' => 6, 'rules' => 'nullable|url|max:255'],
            ['name' => 'facebook', 'label' => 'Facebook URL', 'type' => 'url', 'col' => 6, 'rules' => 'nullable|url|max:255'],
            $this->imageField('photo', 'Photo', 'Square or portrait photo, face centred.'),
            $this->sortField(),
            $this->activeField(),
        ];
    }

    protected function filters(): array
    {
        return ['group' => ['Group', TeamMember::GROUPS]];
    }

    protected function columns(): array
    {
        return [
            ['', fn ($t) => static::thumb($t->photo, $t->name)],
            ['Name', fn ($t) => static::titleCell($t->name, $t->designation), 'name'],
            ['Group', fn ($t) => static::badge(TeamMember::GROUPS[$t->group] ?? $t->group, 'info'), 'group'],
            ['Order', fn ($t) => $t->sort_order, 'sort_order'],
        ];
    }
}
