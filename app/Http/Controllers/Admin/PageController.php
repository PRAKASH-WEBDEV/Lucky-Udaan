<?php

namespace App\Http\Controllers\Admin;

use App\Models\Page;
use Illuminate\Database\Eloquent\Model;

/**
 * Website Content Management: hero text, body copy and SEO for every public page,
 * including the Privacy, Terms and Refund policies.
 */
class PageController extends ResourceController
{
    protected string $model = Page::class;

    protected string $route = 'admin.pages';

    protected string $title = 'Pages';

    protected string $singular = 'Page';

    protected string $icon = 'file-earmark-richtext';

    protected string $uploadDir = 'pages';

    protected array $searchable = ['title', 'slug'];

    protected string $sort = 'title';

    protected string $direction = 'asc';

    protected bool $canCreate = false; // pages are fixed by the site structure

    protected bool $canDelete = false;

    protected int $perPage = 40;

    protected function fields(?Model $item): array
    {
        return [
            ['type' => 'static', 'label' => 'Page address', 'value' => fn ($p) => new \Illuminate\Support\HtmlString('<code>/'.e($p->slug === 'home' ? '' : $p->slug).'</code>')],
            ['name' => 'title', 'label' => 'Page heading', 'rules' => 'required|string|max:150'],
            ['name' => 'subtitle', 'label' => 'Intro line (under the heading)', 'type' => 'textarea', 'rows' => 2, 'rules' => 'nullable|string|max:500'],
            ['name' => 'body', 'label' => 'Page content', 'type' => 'editor', 'help' => 'Main text for this page. On listing pages (e.g. Events) it appears above the list.'],
            ...$this->seoFields(),
            $this->imageField('image', 'Header / share image'),
            $this->activeField(),
        ];
    }

    protected function columns(): array
    {
        return [
            ['Page', fn ($p) => static::titleCell($p->title, $p->subtitle), 'title'],
            ['Address', fn ($p) => new \Illuminate\Support\HtmlString('<code>/'.e($p->slug === 'home' ? '' : $p->slug).'</code>'), 'slug'],
            ['Updated', fn ($p) => $p->updated_at?->diffForHumans(), 'updated_at'],
        ];
    }

    protected function rowActions(Model $item): array
    {
        return [['View on site', url($item->slug === 'home' ? '/' : $item->slug), 'box-arrow-up-right']];
    }
}
