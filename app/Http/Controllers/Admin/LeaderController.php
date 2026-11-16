<?php

namespace App\Http\Controllers\Admin;

class LeaderController extends SiteBlockController
{
    protected string $route = 'admin.leaders';

    protected string $title = 'Support Leaders';

    protected string $singular = 'Leader';

    protected string $icon = 'person-raised-hand';

    protected string $blockType = 'leader';

    protected array $labels = [
        'title' => 'Name',
        'subtitle' => 'Designation / organisation',
        'description' => 'Message or how they support us',
        'url' => 'Profile link (optional)',
        'image' => 'Photo',
    ];
}
