<?php

namespace App\Http\Controllers\Admin;

use App\Models\Report;
use Illuminate\Database\Eloquent\Model;

/** Annual reports, audited financials and impact reports (shown on Annual Reports + Transparency pages). */
class ReportController extends ResourceController
{
    protected string $model = Report::class;

    protected string $route = 'admin.reports';

    protected string $title = 'Annual Reports';

    protected string $singular = 'Report';

    protected string $icon = 'file-earmark-bar-graph';

    protected string $uploadDir = 'reports';

    protected array $searchable = ['title', 'year'];

    protected string $sort = 'year';

    protected function fields(?Model $item): array
    {
        return [
            ['name' => 'title', 'label' => 'Title', 'rules' => 'required|string|max:200'],
            ['name' => 'type', 'label' => 'Type', 'type' => 'select', 'col' => 6, 'options' => Report::TYPES, 'rules' => 'required|in:'.implode(',', array_keys(Report::TYPES))],
            ['name' => 'year', 'label' => 'Financial year', 'col' => 6, 'rules' => 'nullable|string|max:20', 'placeholder' => '2025-26'],
            ['name' => 'summary', 'label' => 'Summary', 'type' => 'textarea', 'rows' => 4, 'rules' => 'nullable|string|max:2000'],
            ['name' => 'file', 'label' => 'Report PDF', 'type' => 'file', 'side' => true, 'mimes' => 'pdf', 'max' => 20480, 'help' => 'PDF up to 20 MB.'],
            $this->imageField('cover', 'Cover image'),
            $this->activeField(),
        ];
    }

    protected function filters(): array
    {
        return ['type' => ['Type', Report::TYPES]];
    }

    protected function columns(): array
    {
        return [
            ['', fn ($r) => static::thumb($r->cover, $r->title)],
            ['Report', fn ($r) => static::titleCell($r->title, Report::TYPES[$r->type] ?? $r->type), 'title'],
            ['Year', fn ($r) => $r->year ?: '—', 'year'],
            ['PDF', fn ($r) => $r->file ? static::badge('Uploaded', 'success') : static::badge('Missing', 'warning')],
        ];
    }
}
