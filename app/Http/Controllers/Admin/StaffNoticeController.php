<?php

namespace App\Http\Controllers\Admin;

use App\Models\StaffNotice;
use Illuminate\Database\Eloquent\Model;

/** Notices for employees, shown in the staff portal (/staff). */
class StaffNoticeController extends ResourceController
{
    protected string $model = StaffNotice::class;

    protected string $route = 'admin.staff-notices';

    protected string $title = 'Staff Notices';

    protected string $singular = 'Staff notice';

    protected string $icon = 'bell';

    protected array $searchable = ['title', 'message'];

    protected function fields(?Model $item): array
    {
        return [
            ['name' => 'title', 'label' => 'Title', 'col' => 8, 'rules' => 'required|string|max:150', 'placeholder' => 'e.g. Office closed on Saturday'],
            ['name' => 'priority', 'label' => 'Priority', 'type' => 'select', 'col' => 4, 'options' => StaffNotice::PRIORITIES, 'rules' => 'required|in:normal,important', 'default' => 'normal'],
            ['name' => 'message', 'label' => 'Message', 'type' => 'textarea', 'rows' => 4, 'rules' => 'nullable|string|max:3000'],
            ['name' => 'is_active', 'label' => 'Show to staff', 'type' => 'switch', 'side' => true, 'default' => true],
        ];
    }

    protected function filters(): array
    {
        return ['priority' => ['Priority', StaffNotice::PRIORITIES]];
    }

    protected function columns(): array
    {
        return [
            ['Notice', fn ($n) => static::titleCell($n->title, \Illuminate\Support\Str::limit((string) $n->message, 80)), 'title'],
            ['Priority', fn ($n) => static::badge(StaffNotice::PRIORITIES[$n->priority] ?? $n->priority, $n->priority === 'important' ? 'danger' : 'info')],
            ['Posted', fn ($n) => $n->created_at?->format('d M Y'), 'created_at'],
        ];
    }

    protected function exportColumns(): array
    {
        return ['Title' => 'title', 'Message' => 'message', 'Priority' => 'priority', 'Posted' => fn ($n) => $n->created_at?->format('Y-m-d')];
    }
}
