<?php

namespace App\Http\Controllers\Admin;

class InternshipController extends CareerController
{
    protected string $route = 'admin.internships';

    protected string $title = 'Internships';

    protected string $singular = 'Internship';

    protected string $icon = 'mortarboard';

    protected string $openingType = 'internship';
}
