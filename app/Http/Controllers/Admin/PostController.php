<?php

namespace App\Http\Controllers\Admin;

use App\Models\Post;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class PostController extends ResourceController
{
    protected string $model = Post::class;

    protected string $route = 'admin.posts';

    protected string $title = 'Blog & News';

    protected string $singular = 'Post';

    protected string $icon = 'newspaper';

    protected string $uploadDir = 'posts';

    protected array $searchable = ['title', 'excerpt', 'author'];

    protected string $sort = 'published_at';

    protected ?string $dateColumn = 'published_at';

    protected function fields(?Model $item): array
    {
        return [
            ['name' => 'type', 'label' => 'Type', 'type' => 'select', 'col' => 6, 'options' => Post::TYPES, 'rules' => 'required|in:'.implode(',', array_keys(Post::TYPES)),
                'default' => request('type', 'blog'), 'help' => 'Announcements, Press Releases and Media Coverage each have their own page on the website.'],
            ['name' => 'published_at', 'label' => 'Publish date', 'type' => 'datetime', 'col' => 6, 'rules' => 'nullable|date', 'help' => 'Future dates are scheduled.'],
            ['name' => 'title', 'label' => 'Title', 'rules' => 'required|string|max:200'],
            $this->slugField($item, 'posts'),
            ['name' => 'excerpt', 'label' => 'Summary', 'type' => 'textarea', 'rows' => 3, 'rules' => 'nullable|string|max:500', 'counter' => 200],
            ['name' => 'body', 'label' => 'Content', 'type' => 'editor'],
            ['name' => 'author', 'label' => 'Author', 'col' => 4, 'rules' => 'nullable|string|max:120'],
            ['name' => 'source', 'label' => 'Publication / source', 'col' => 4, 'rules' => 'nullable|string|max:120', 'help' => 'For media coverage, e.g. newspaper name.'],
            ['name' => 'external_url', 'label' => 'External link', 'type' => 'url', 'col' => 4, 'rules' => 'nullable|url|max:255'],
            ['name' => 'video_url', 'label' => 'YouTube video link', 'type' => 'url', 'col' => 12, 'rules' => 'nullable|url|max:255'],
            ...$this->seoFields(),
            $this->imageField('image', 'Cover image'),
            $this->featuredField('Featured'),
            $this->activeField(),
        ];
    }

    protected function filters(): array
    {
        return ['type' => ['Type', Post::TYPES]];
    }

    protected function prepare(array $data, ?Model $item, Request $request): array
    {
        $data['published_at'] ??= $item?->published_at ?? now();

        return $data;
    }

    protected function columns(): array
    {
        return [
            ['', fn ($p) => static::thumb($p->image, $p->title)],
            ['Title', fn ($p) => static::titleCell($p->title, $p->author), 'title'],
            ['Type', fn ($p) => static::badge($p->typeLabel(), 'info'), 'type'],
            ['Published', fn ($p) => $p->published_at?->format('d M Y') ?? '—', 'published_at'],
        ];
    }

    protected function rowActions(Model $item): array
    {
        return [['View on site', $item->url(), 'box-arrow-up-right']];
    }
}
