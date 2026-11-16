<?php

namespace App\Http\Controllers\Admin;

class JourneyController extends SiteBlockController
{
    protected string $route = 'admin.journey';

    protected string $title = 'Our Journey';

    protected string $singular = 'Milestone';

    protected string $icon = 'signpost-split';

    protected string $blockType = 'journey';

    protected array $labels = [
        'title' => 'Milestone',
        'value' => 'Year',
        'description' => 'What happened',
        'image' => 'Photo (optional)',
    ];
}
