<?php

namespace App\Http\Controllers\Admin;

class AwardController extends SiteBlockController
{
    protected string $route = 'admin.awards';

    protected string $title = 'Awards';

    protected string $singular = 'Award';

    protected string $icon = 'trophy';

    protected string $blockType = 'award';

    protected array $labels = [
        'title' => 'Award / recognition',
        'value' => 'Year',
        'subtitle' => 'Given by',
        'description' => 'Details',
        'url' => 'Link (news, certificate)',
        'image' => 'Photo or certificate',
    ];
}
