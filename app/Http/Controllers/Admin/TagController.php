<?php

namespace App\Http\Controllers\Admin;

use App\Models\Post;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Model;

class TagController extends ResourceController
{
    protected string $model = Tag::class;

    protected string $route = 'admin.tags';

    protected string $title = 'Tags';

    protected string $singular = 'Tag';

    protected string $icon = 'tags';

    protected array $searchable = ['name', 'slug'];

    protected string $sort = 'name';

    protected string $direction = 'asc';

    protected int $perPage = 40;

    protected function fields(?Model $item): array
    {
        return [
            ['name' => 'name', 'label' => 'Tag', 'rules' => 'required|string|max:80'],
            $this->slugField($item, 'tags'),
        ];
    }

    protected function columns(): array
    {
        return [
            ['Tag', fn ($t) => static::badge($t->name, 'info'), 'name'],
            ['Slug', fn ($t) => $t->slug, 'slug'],
            ['Posts', fn ($t) => Post::whereJsonContains('tags', $t->slug)->count()],
        ];
    }
}
