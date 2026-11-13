<?php

namespace App\Http\Controllers\Admin;

use App\Models\Banner;
use Illuminate\Database\Eloquent\Model;

class BannerController extends ResourceController
{
    protected string $model = Banner::class;

    protected string $route = 'admin.banners';

    protected string $title = 'Homepage Banners';

    protected string $singular = 'Banner';

    protected string $icon = 'images';

    protected string $uploadDir = 'banners';

    protected array $searchable = ['title', 'subtitle'];

    protected string $sort = 'sort_order';

    protected string $direction = 'asc';

    protected function fields(?Model $item): array
    {
        return [
            ['name' => 'eyebrow', 'label' => 'Small label above title', 'rules' => 'nullable|string|max:80', 'placeholder' => 'e.g. Education for every child'],
            ['name' => 'title', 'label' => 'Headline', 'rules' => 'required|string|max:120', 'counter' => 70],
            ['name' => 'subtitle', 'label' => 'Supporting text', 'type' => 'textarea', 'rows' => 3, 'rules' => 'nullable|string|max:300', 'counter' => 180],
            ['type' => 'section', 'label' => 'Buttons'],
            ['name' => 'button_text', 'label' => 'Primary button text', 'col' => 6, 'rules' => 'nullable|string|max:40'],
            ['name' => 'button_link', 'label' => 'Primary button link', 'col' => 6, 'rules' => 'nullable|string|max:255', 'placeholder' => '/donate'],
            ['name' => 'button2_text', 'label' => 'Secondary button text', 'col' => 6, 'rules' => 'nullable|string|max:40'],
            ['name' => 'button2_link', 'label' => 'Secondary button link', 'col' => 6, 'rules' => 'nullable|string|max:255', 'placeholder' => '/volunteer'],
            $this->imageField('image', 'Banner image', 'Landscape photo, ideally 1920×1080 or larger. Faces work best in the centre-right.'),
            $this->sortField(),
            $this->activeField(),
        ];
    }

    protected function columns(): array
    {
        return [
            ['', fn ($b) => static::thumb($b->image, $b->title)],
            ['Headline', fn ($b) => static::titleCell($b->title, $b->subtitle), 'title'],
            ['Order', fn ($b) => $b->sort_order, 'sort_order'],
        ];
    }
}
