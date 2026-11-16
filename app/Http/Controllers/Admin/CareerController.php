<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/** Job openings only ("Careers"). */
class CareerController extends OpeningController
{
    protected string $route = 'admin.careers';

    protected string $title = 'Careers';

    protected string $singular = 'Job opening';

    protected string $icon = 'briefcase';

    protected string $openingType = 'job';

    protected function newQuery(): Builder
    {
        return parent::newQuery()->where('type', $this->openingType);
    }

    protected function fields(?Model $item): array
    {
        return array_values(array_filter(parent::fields($item), fn ($f) => ($f['name'] ?? null) !== 'type'));
    }

    protected function prepare(array $data, ?Model $item, Request $request): array
    {
        $data['type'] = $this->openingType;

        return parent::prepare($data, $item, $request);
    }

    protected function filters(): array
    {
        return [];
    }
}
