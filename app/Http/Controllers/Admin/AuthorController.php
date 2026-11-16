<?php

namespace App\Http\Controllers\Admin;

use App\Models\Author;
use App\Models\Post;
use Illuminate\Database\Eloquent\Model;

class AuthorController extends ResourceController
{
    protected string $model = Author::class;

    protected string $route = 'admin.authors';

    protected string $title = 'Authors';

    protected string $singular = 'Author';

    protected string $icon = 'pen';

    protected string $uploadDir = 'authors';

    protected array $searchable = ['name', 'designation', 'email'];

    protected string $sort = 'name';

    protected string $direction = 'asc';

    protected function fields(?Model $item): array
    {
        return [
            ['name' => 'name', 'label' => 'Name', 'col' => 6, 'rules' => 'required|string|max:120'],
            ['name' => 'designation', 'label' => 'Designation', 'col' => 6, 'rules' => 'nullable|string|max:120'],
            ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'col' => 6, 'rules' => 'nullable|email|max:150'],
            ['name' => 'bio', 'label' => 'Short bio', 'type' => 'textarea', 'rows' => 3, 'rules' => 'nullable|string|max:1000'],
            $this->imageField('photo', 'Photo'),
            $this->activeField(),
        ];
    }

    protected function columns(): array
    {
        return [
            ['', fn ($a) => static::thumb($a->photo, $a->name)],
            ['Author', fn ($a) => static::titleCell($a->name, $a->designation), 'name'],
            ['Posts', fn ($a) => Post::where('author', $a->name)->count()],
        ];
    }
}
