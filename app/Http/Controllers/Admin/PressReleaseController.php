<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/** Posts of a single type, with their own menu entry. */
class PressReleaseController extends PostController
{
    protected string $route = 'admin.press';

    protected string $title = 'Press Releases';

    protected string $singular = 'Press release';

    protected string $icon = 'broadcast';

    protected string $postType = 'press';

    protected function newQuery(): Builder
    {
        return parent::newQuery()->where('type', $this->postType);
    }

    protected function fields(?Model $item): array
    {
        return array_values(array_map(function ($f) {
            if (($f['name'] ?? null) === 'type') {
                return ['type' => 'static', 'label' => 'Type', 'value' => fn () => \App\Models\Post::TYPES[$this->postType]];
            }

            return $f;
        }, parent::fields($item)));
    }

    protected function prepare(array $data, ?Model $item, Request $request): array
    {
        $data['type'] = $this->postType;

        return parent::prepare($data, $item, $request);
    }

    protected function filters(): array
    {
        return [];
    }
}
