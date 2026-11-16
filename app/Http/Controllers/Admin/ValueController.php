<?php

namespace App\Http\Controllers\Admin;

class ValueController extends SiteBlockController
{
    protected string $route = 'admin.values';

    protected string $title = 'Our Values';

    protected string $singular = 'Value';

    protected string $icon = 'gem';

    protected string $blockType = 'value';

    protected array $labels = [
        'title' => 'Value',
        'description' => 'What it means for us',
        'icon' => 'Icon',
    ];
}
