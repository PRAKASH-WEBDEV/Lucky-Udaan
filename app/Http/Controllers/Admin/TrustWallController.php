<?php

namespace App\Http\Controllers\Admin;

class TrustWallController extends SiteBlockController
{
    protected string $route = 'admin.trust-wall';

    protected string $title = 'Trust Wall';

    protected string $singular = 'Supporter';

    protected string $icon = 'patch-check';

    protected string $blockType = 'trust';

    protected array $labels = [
        'title' => 'Supporter / organisation name',
        'subtitle' => 'Role or relation (e.g. CSR partner, Monthly donor)',
        'description' => 'What they say about us',
        'url' => 'Website (optional)',
        'image' => 'Logo or photo',
    ];
}
