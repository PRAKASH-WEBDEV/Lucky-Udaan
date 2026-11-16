<?php

namespace App\Http\Controllers\Admin;

use App\Models\Holiday;
use Illuminate\Database\Eloquent\Model;

class HolidayController extends ResourceController
{
    protected string $model = Holiday::class;

    protected string $route = 'admin.holidays';

    protected string $title = 'Holiday Calendar';

    protected string $singular = 'Holiday';

    protected string $icon = 'calendar-heart';

    protected array $searchable = ['title', 'description'];

    protected string $sort = 'date';

    protected string $direction = 'asc';

    protected ?string $dateColumn = 'date';

    protected int $perPage = 40;

    protected function fields(?Model $item): array
    {
        return [
            ['name' => 'title', 'label' => 'Holiday', 'col' => 6, 'rules' => 'required|string|max:120', 'datalist' => ['Republic Day', 'Holi', 'Independence Day', 'Raksha Bandhan', 'Gandhi Jayanti', 'Dussehra', 'Diwali', 'Christmas']],
            ['name' => 'date', 'label' => 'Date', 'type' => 'date', 'col' => 3, 'rules' => 'required|date'],
            ['name' => 'type', 'label' => 'Type', 'type' => 'select', 'col' => 3, 'options' => Holiday::TYPES, 'rules' => 'required|in:'.implode(',', array_keys(Holiday::TYPES)), 'default' => 'festival'],
            ['name' => 'description', 'label' => 'Note', 'rules' => 'nullable|string|max:500'],
            ['name' => 'is_active', 'label' => 'Office closed', 'type' => 'switch', 'side' => true, 'default' => true],
        ];
    }

    protected function filters(): array
    {
        return ['type' => ['Type', Holiday::TYPES]];
    }

    protected function columns(): array
    {
        return [
            ['Date', fn ($h) => $h->date->format('D, d M Y'), 'date'],
            ['Holiday', fn ($h) => static::titleCell($h->title, $h->description), 'title'],
            ['Type', fn ($h) => static::badge(Holiday::TYPES[$h->type] ?? $h->type, 'info')],
        ];
    }

    protected function exportColumns(): array
    {
        return ['Date' => fn ($h) => $h->date->format('Y-m-d'), 'Holiday' => 'title', 'Type' => 'type', 'Note' => 'description'];
    }
}
