<?php

namespace App\Http\Controllers\Admin;

use App\Models\Volunteer;
use App\Support\Options;
use Illuminate\Database\Eloquent\Model;

class VolunteerController extends ResourceController
{
    protected string $model = Volunteer::class;

    protected string $route = 'admin.volunteers';

    protected string $title = 'Volunteers';

    protected string $singular = 'Volunteer';

    protected string $icon = 'hand-thumbs-up';

    protected string $uploadDir = 'volunteers';

    protected array $searchable = ['name', 'email', 'phone', 'city', 'skills'];

    protected ?string $dateColumn = 'created_at';

    protected function fields(?Model $item): array
    {
        return [
            ['name' => 'name', 'label' => 'Full name', 'col' => 6, 'rules' => 'required|string|max:120'],
            ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'col' => 6, 'rules' => 'required|email|max:150'],
            ['name' => 'phone', 'label' => 'Phone', 'col' => 6, 'rules' => 'required|string|max:20'],
            ['name' => 'city', 'label' => 'City', 'col' => 6, 'rules' => 'nullable|string|max:80'],
            ['name' => 'occupation', 'label' => 'Occupation', 'col' => 6, 'rules' => 'nullable|string|max:120'],
            ['name' => 'availability', 'label' => 'Availability', 'type' => 'select', 'col' => 6, 'options' => Options::availability(), 'empty' => '—', 'rules' => 'nullable|string|max:60'],
            ['name' => 'interests', 'label' => 'Areas of interest', 'type' => 'checkboxes', 'options' => Options::interests()],
            ['name' => 'skills', 'label' => 'Skills', 'type' => 'textarea', 'rows' => 2, 'rules' => 'nullable|string|max:1000'],
            ['name' => 'message', 'label' => 'Message', 'type' => 'textarea', 'rows' => 3, 'rules' => 'nullable|string|max:2000'],
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'side' => true, 'options' => ['new' => 'New', 'approved' => 'Approved', 'rejected' => 'Rejected'], 'rules' => 'required|in:new,approved,rejected'],
            $this->imageField('photo', 'Photo'),
            $this->activeField(),
        ];
    }

    protected function filters(): array
    {
        return ['status' => ['Status', ['new' => 'New', 'approved' => 'Approved', 'rejected' => 'Rejected']]];
    }

    protected function columns(): array
    {
        return [
            ['Volunteer', fn ($v) => static::titleCell($v->name, $v->email), 'name'],
            ['Phone', fn ($v) => $v->phone],
            ['City', fn ($v) => $v->city ?: '—', 'city'],
            ['Interests', fn ($v) => \Illuminate\Support\Str::limit(implode(', ', $v->interests ?? []), 40) ?: '—'],
            ['Status', fn ($v) => static::badge(ucfirst($v->status), static::statusTone($v->status)), 'status'],
            ['Applied', fn ($v) => $v->created_at->format('d M Y'), 'created_at'],
        ];
    }

    protected function exportColumns(): array
    {
        return [
            'Name' => 'name', 'Email' => 'email', 'Phone' => 'phone', 'City' => 'city', 'Occupation' => 'occupation',
            'Availability' => 'availability', 'Interests' => 'interests', 'Skills' => 'skills', 'Status' => 'status',
            'Applied on' => fn ($v) => $v->created_at->format('Y-m-d'),
        ];
    }
}
