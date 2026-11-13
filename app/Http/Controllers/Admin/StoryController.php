<?php

namespace App\Http\Controllers\Admin;

use App\Models\SuccessStory;
use Illuminate\Database\Eloquent\Model;

class StoryController extends ResourceController
{
    protected string $model = SuccessStory::class;

    protected string $route = 'admin.stories';

    protected string $title = 'Success Stories';

    protected string $singular = 'Story';

    protected string $icon = 'stars';

    protected string $uploadDir = 'stories';

    protected array $searchable = ['title', 'person_name'];

    protected function fields(?Model $item): array
    {
        return [
            ['name' => 'title', 'label' => 'Story title', 'rules' => 'required|string|max:200'],
            $this->slugField($item, 'success_stories'),
            ['name' => 'person_name', 'label' => 'Person / group', 'col' => 6, 'rules' => 'nullable|string|max:120'],
            ['name' => 'location', 'label' => 'Location', 'col' => 6, 'rules' => 'nullable|string|max:120'],
            ['name' => 'excerpt', 'label' => 'Summary', 'type' => 'textarea', 'rows' => 3, 'rules' => 'required|string|max:500', 'counter' => 200],
            ['name' => 'body', 'label' => 'Full story', 'type' => 'editor'],
            $this->imageField(),
            $this->featuredField(),
            $this->activeField(),
        ];
    }

    protected function columns(): array
    {
        return [
            ['', fn ($s) => static::thumb($s->image, $s->title)],
            ['Story', fn ($s) => static::titleCell($s->title, trim($s->person_name.' · '.$s->location, ' ·')), 'title'],
        ];
    }

    protected function rowActions(Model $item): array
    {
        return [['View on site', route('stories.show', $item), 'box-arrow-up-right']];
    }
}
