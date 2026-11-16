<?php

namespace App\Http\Controllers\Admin;

use App\Models\SiteBlock;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * Small repeatable website sections stored in one table (site_blocks), split by "type".
 * Subclasses pick the type and which fields make sense for it.
 */
abstract class SiteBlockController extends ResourceController
{
    protected string $model = SiteBlock::class;

    protected string $uploadDir = 'blocks';

    protected array $searchable = ['title', 'subtitle', 'description'];

    protected string $sort = 'sort_order';

    protected string $direction = 'asc';

    /** Value stored in site_blocks.type. */
    protected string $blockType;

    /** Labels for the optional fields this block uses: field => label (null = hidden). */
    protected array $labels = [];

    protected function newQuery(): Builder
    {
        return parent::newQuery()->where('type', $this->blockType);
    }

    protected function label(string $field): ?string
    {
        return $this->labels[$field] ?? null;
    }

    protected function fields(?Model $item): array
    {
        $f = [['name' => 'title', 'label' => $this->label('title') ?? 'Title', 'rules' => 'required|string|max:200']];
        if ($l = $this->label('value')) {
            $f[] = ['name' => 'value', 'label' => $l, 'col' => 4, 'rules' => 'nullable|string|max:60'];
        }
        if ($l = $this->label('subtitle')) {
            $f[] = ['name' => 'subtitle', 'label' => $l, 'col' => $this->label('value') ? 8 : 12, 'rules' => 'nullable|string|max:255'];
        }
        if ($l = $this->label('description')) {
            $f[] = ['name' => 'description', 'label' => $l, 'type' => 'textarea', 'rows' => 4, 'rules' => 'nullable|string|max:3000'];
        }
        if ($l = $this->label('url')) {
            $f[] = ['name' => 'url', 'label' => $l, 'type' => 'url', 'rules' => 'nullable|url|max:255'];
        }
        if ($l = $this->label('embed_code')) {
            $f[] = ['name' => 'embed_code', 'label' => $l, 'type' => 'textarea', 'rows' => 6, 'rules' => 'nullable|string|max:10000',
                'help' => 'Paste the embed code from Facebook, Instagram, YouTube or X. Only iframe/blockquote embeds from those sites are shown.'];
        }
        if ($l = $this->label('icon')) {
            $f[] = ['name' => 'icon', 'label' => $l, 'type' => 'icon', 'options' => static::iconOptions(), 'rules' => 'nullable|string|max:60'];
        }
        if ($l = $this->label('image')) {
            $f[] = $this->imageField('image', $l);
        }
        $f[] = $this->sortField();
        $f[] = $this->activeField();

        return $f;
    }

    protected function prepare(array $data, ?Model $item, Request $request): array
    {
        $data['type'] = $this->blockType;

        return $data;
    }

    protected function columns(): array
    {
        $cols = [];
        if ($this->label('image')) {
            $cols[] = ['', fn ($b) => static::thumb($b->image, $b->title)];
        }
        if ($this->label('value')) {
            $cols[] = [$this->label('value'), fn ($b) => $b->value ?: '—', 'value'];
        }
        $cols[] = [$this->singular, fn ($b) => static::titleCell($b->title, $b->subtitle ?: $b->description), 'title'];
        $cols[] = ['Order', fn ($b) => $b->sort_order, 'sort_order'];

        return $cols;
    }
}
