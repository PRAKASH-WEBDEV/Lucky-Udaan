<?php

namespace App\Http\Controllers\Admin;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

/** Read-only history of what each admin created, changed or deleted. */
class AuditLogController extends ResourceController
{
    protected string $model = AuditLog::class;

    protected string $route = 'admin.audit-logs';

    protected string $title = 'Audit Logs';

    protected string $singular = 'Log entry';

    protected string $icon = 'clock-history';

    protected array $searchable = ['description', 'subject_type'];

    protected array $with = ['admin'];

    protected bool $canCreate = false;

    protected bool $canEdit = false;

    protected bool $canDelete = false;

    protected ?string $dateColumn = 'created_at';

    protected int $perPage = 40;

    public const ACTIONS = ['created' => 'Created', 'updated' => 'Updated', 'deleted' => 'Deleted', 'login' => 'Signed in', 'logout' => 'Signed out'];

    protected function fields(?Model $item): array
    {
        return [];
    }

    protected function filters(): array
    {
        return [
            'action' => ['Action', self::ACTIONS],
            'admin_id' => ['Admin', \App\Models\Admin::orderBy('name')->pluck('name', 'id')->all()],
        ];
    }

    protected function columns(): array
    {
        return [
            ['When', fn ($l) => new HtmlString(e($l->created_at?->format('d M Y, h:i A')).'<div class="small text-muted">'.e($l->created_at?->diffForHumans()).'</div>'), 'created_at'],
            ['Admin', fn ($l) => static::titleCell($l->admin?->name ?? 'System', $l->ip)],
            ['Action', fn ($l) => static::badge(self::ACTIONS[$l->action] ?? $l->action, match ($l->action) { 'created' => 'success', 'deleted' => 'danger', 'updated' => 'info', default => 'secondary' })],
            ['Record', fn ($l) => static::titleCell($l->description ?? '—', $l->subject_type ? Str::headline(class_basename($l->subject_type)).' #'.$l->subject_id : null)],
            ['Changed fields', fn ($l) => $l->changes ? Str::limit(implode(', ', array_keys($l->changes)), 80) : '—'],
        ];
    }

    protected function exportColumns(): array
    {
        return [
            'When' => fn ($l) => $l->created_at?->format('Y-m-d H:i:s'), 'Admin' => fn ($l) => $l->admin?->name, 'Action' => 'action',
            'Record type' => fn ($l) => $l->subject_type ? class_basename($l->subject_type) : '', 'Record id' => 'subject_id',
            'Description' => 'description', 'Changes' => fn ($l) => $l->changes ? json_encode($l->changes, JSON_UNESCAPED_UNICODE) : '', 'IP' => 'ip',
        ];
    }
}
