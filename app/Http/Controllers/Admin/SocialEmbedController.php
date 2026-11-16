<?php

namespace App\Http\Controllers\Admin;

class SocialEmbedController extends SiteBlockController
{
    protected string $route = 'admin.social-embeds';

    protected string $title = 'Social Embeds';

    protected string $singular = 'Embed';

    protected string $icon = 'share';

    protected string $blockType = 'embed';

    protected array $labels = [
        'title' => 'Label (for your reference)',
        'subtitle' => 'Platform',
        'embed_code' => 'Embed code',
        'url' => 'Post link',
    ];
}
