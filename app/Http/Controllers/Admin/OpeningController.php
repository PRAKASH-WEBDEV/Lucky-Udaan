<?php

namespace App\Http\Controllers\Admin;

use App\Models\Opening;
use Illuminate\Database\Eloquent\Model;

/** Job and internship openings shown on the Careers and Internships pages. */
class OpeningController extends ResourceController
{
    protected string $model = Opening::class;

    protected string $route = 'admin.openings';

    protected string $title = 'Jobs & Internships';

    protected string $singular = 'Opening';

    protected string $icon = 'briefcase';

    protected array $searchable = ['title', 'location'];

    protected function fields(?Model $item): array
    {
        return [
            ['name' => 'type', 'label' => 'Type', 'type' => 'select', 'col' => 6, 'options' => ['job' => 'Job / Fellowship', 'internship' => 'Internship'], 'rules' => 'required|in:job,internship'],
            ['name' => 'deadline', 'label' => 'Apply by', 'type' => 'date', 'col' => 6, 'rules' => 'nullable|date'],
            ['name' => 'title', 'label' => 'Role title', 'rules' => 'required|string|max:150'],
            $this->slugField($item, 'openings'),
            ['name' => 'location', 'label' => 'Location', 'col' => 4, 'rules' => 'nullable|string|max:120'],
            ['name' => 'employment_type', 'label' => 'Engagement', 'col' => 4, 'rules' => 'nullable|string|max:60', 'datalist' => ['Full-time', 'Part-time', 'Volunteer', 'Stipend-based', 'Unpaid']],
            ['name' => 'duration', 'label' => 'Duration', 'col' => 4, 'rules' => 'nullable|string|max:60', 'placeholder' => 'e.g. 8 weeks'],
            ['name' => 'summary', 'label' => 'Summary', 'type' => 'textarea', 'rows' => 3, 'rules' => 'required|string|max:800'],
            ['name' => 'description', 'label' => 'Responsibilities & requirements', 'type' => 'editor'],
            $this->activeField(),
        ];
    }

    protected function filters(): array
    {
        return ['type' => ['Type', ['job' => 'Jobs', 'internship' => 'Internships']]];
    }

    protected function columns(): array
    {
        return [
            ['Role', fn ($o) => static::titleCell($o->title, $o->location), 'title'],
            ['Type', fn ($o) => static::badge($o->type === 'job' ? 'Job' : 'Internship', $o->type === 'job' ? 'primary' : 'info'), 'type'],
            ['Apply by', fn ($o) => $o->deadline?->format('d M Y') ?? 'Open', 'deadline'],
        ];
    }
}
