<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Database\Eloquent\Model;

class CategoryController extends ResourceController
{
    protected string $model = Category::class;

    protected string $route = 'admin.categories';

    protected string $title = 'Categories';

    protected string $singular = 'Category';

    protected string $icon = 'folder2';

    protected array $searchable = ['name', 'description'];

    protected string $sort = 'name';

    protected string $direction = 'asc';

    protected function fields(?Model $item): array
    {
        return [
            ['name' => 'name', 'label' => 'Name', 'rules' => 'required|string|max:120'],
            $this->slugField($item, 'categories'),
            ['name' => 'description', 'label' => 'Description', 'type' => 'textarea', 'rows' => 2, 'rules' => 'nullable|string|max:500'],
            $this->activeField(),
        ];
    }

    protected function columns(): array
    {
        return [
            ['Category', fn ($c) => static::titleCell($c->name, $c->description), 'name'],
            ['Slug', fn ($c) => $c->slug],
            ['Posts', fn ($c) => Post::where('category_id', $c->id)->count()],
        ];
    }
}
