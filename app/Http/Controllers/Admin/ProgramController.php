<?php

namespace App\Http\Controllers\Admin;

use App\Models\Program;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

class ProgramController extends ResourceController
{
    protected string $model = Program::class;

    protected string $route = 'admin.programs';

    protected string $title = 'Programs';

    protected string $singular = 'Program';

    protected string $icon = 'diagram-3';

    protected string $uploadDir = 'programs';

    protected array $searchable = ['title', 'excerpt'];

    protected string $sort = 'sort_order';

    protected string $direction = 'asc';

    protected function fields(?Model $item): array
    {
        return [
            ['name' => 'title', 'label' => 'Program title', 'rules' => 'required|string|max:150'],
            $this->slugField($item, 'programs'),
            ['name' => 'excerpt', 'label' => 'Short description', 'type' => 'textarea', 'rows' => 3, 'rules' => 'required|string|max:500', 'counter' => 200, 'help' => 'Shown on cards and in search results.'],
            ['name' => 'description', 'label' => 'Full description', 'type' => 'editor'],
            ['name' => 'highlights', 'label' => 'Key activities', 'type' => 'textarea', 'rows' => 5, 'rules' => 'nullable|string|max:3000', 'help' => 'One activity per line. Shown as a checklist.'],
            ...$this->seoFields(),
            $this->imageField(),
            ['name' => 'icon', 'label' => 'Icon', 'type' => 'icon', 'side' => true, 'options' => static::iconOptions(), 'rules' => 'nullable|string|max:60'],
            $this->sortField(),
            $this->featuredField(),
            $this->activeField(),
        ];
    }

    protected function columns(): array
    {
        return [
            ['', fn ($p) => static::thumb($p->image, $p->title)],
            ['Program', fn ($p) => new HtmlString('<i class="bi bi-'.e($p->icon ?: 'heart').' text-primary me-1"></i>'.static::titleCell($p->title, $p->excerpt)), 'title'],
            ['Featured', fn ($p) => $p->is_featured ? static::badge('Featured', 'primary') : static::badge(null)],
            ['Order', fn ($p) => $p->sort_order, 'sort_order'],
        ];
    }

    protected function rowActions(Model $item): array
    {
        return [['View on site', route('programs.show', $item), 'box-arrow-up-right']];
    }
}
