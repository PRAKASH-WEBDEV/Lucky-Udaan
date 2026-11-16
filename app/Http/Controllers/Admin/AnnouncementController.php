<?php

namespace App\Http\Controllers\Admin;

class AnnouncementController extends PressReleaseController
{
    protected string $route = 'admin.announcements';

    protected string $title = 'Announcements';

    protected string $singular = 'Announcement';

    protected string $icon = 'megaphone';

    protected string $postType = 'announcement';
}
