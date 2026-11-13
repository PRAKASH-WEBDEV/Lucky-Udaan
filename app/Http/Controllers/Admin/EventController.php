<?php

namespace App\Http\Controllers\Admin;

use App\Models\Event;
use Illuminate\Database\Eloquent\Model;

class EventController extends ResourceController
{
    protected string $model = Event::class;

    protected string $route = 'admin.events';

    protected string $title = 'Events';

    protected string $singular = 'Event';

    protected string $icon = 'calendar-event';

    protected string $uploadDir = 'events';

    protected array $searchable = ['title', 'venue'];

    protected string $sort = 'starts_at';

    protected ?string $dateColumn = 'starts_at';

    protected function fields(?Model $item): array
    {
        return [
            ['name' => 'title', 'label' => 'Event title', 'rules' => 'required|string|max:200'],
            $this->slugField($item, 'events'),
            ['name' => 'starts_at', 'label' => 'Starts', 'type' => 'datetime', 'col' => 6, 'rules' => 'required|date'],
            ['name' => 'ends_at', 'label' => 'Ends', 'type' => 'datetime', 'col' => 6, 'rules' => 'nullable|date|after_or_equal:starts_at'],
            ['name' => 'venue', 'label' => 'Venue', 'col' => 6, 'rules' => 'nullable|string|max:255'],
            ['name' => 'registration_link', 'label' => 'Registration link', 'type' => 'url', 'col' => 6, 'rules' => 'nullable|url|max:255'],
            ['name' => 'excerpt', 'label' => 'Short description', 'type' => 'textarea', 'rows' => 3, 'rules' => 'nullable|string|max:500', 'counter' => 200],
            ['name' => 'description', 'label' => 'Details', 'type' => 'editor'],
            ...$this->seoFields(),
            $this->imageField(),
            $this->featuredField(),
            $this->activeField(),
        ];
    }

    protected function filters(): array
    {
        return [];
    }

    protected function columns(): array
    {
        return [
            ['', fn ($e) => static::thumb($e->image, $e->title)],
            ['Event', fn ($e) => static::titleCell($e->title, $e->venue), 'title'],
            ['Date', fn ($e) => $e->starts_at->format('d M Y, h:i A'), 'starts_at'],
            ['When', fn ($e) => $e->isPast() ? static::badge('Past', 'secondary') : static::badge('Upcoming', 'success')],
        ];
    }

    protected function rowActions(Model $item): array
    {
        return [['View on site', route('events.show', $item), 'box-arrow-up-right']];
    }
}
