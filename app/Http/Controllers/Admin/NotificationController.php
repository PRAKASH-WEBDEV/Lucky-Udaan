<?php

namespace App\Http\Controllers\Admin;

use App\Models\AdminNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

/** Dashboard notifications created when visitors submit forms, donate, subscribe, etc. */
class NotificationController extends ResourceController
{
    protected string $model = AdminNotification::class;

    protected string $route = 'admin.notifications';

    protected string $title = 'Notifications';

    protected string $singular = 'Notification';

    protected string $icon = 'bell';

    protected array $searchable = ['title', 'message'];

    protected bool $canCreate = false;

    protected bool $canEdit = false;

    protected ?string $dateColumn = 'created_at';

    protected int $perPage = 30;

    public const TYPES = ['donation' => 'Donation', 'enquiry' => 'Enquiry', 'volunteer' => 'Volunteer', 'member' => 'Membership', 'newsletter' => 'Newsletter', 'survey' => 'Survey'];

    protected function fields(?Model $item): array
    {
        return [];
    }

    public function index(\Illuminate\Http\Request $request)
    {
        // Opening the list marks everything as read.
        $response = parent::index($request);
        AdminNotification::whereNull('read_at')->update(['read_at' => now()]);

        return $response;
    }

    public function markAllRead()
    {
        AdminNotification::whereNull('read_at')->update(['read_at' => now()]);

        return back()->with('success', 'All notifications marked as read.');
    }

    protected function filters(): array
    {
        return ['type' => ['Type', self::TYPES]];
    }

    protected function columns(): array
    {
        return [
            ['', fn ($n) => new HtmlString($n->read_at ? '' : '<span class="badge rounded-pill text-bg-warning">new</span>')],
            ['Notification', fn ($n) => static::titleCell($n->title, $n->message)],
            ['Type', fn ($n) => static::badge(self::TYPES[$n->type] ?? $n->type, 'info')],
            ['When', fn ($n) => $n->created_at?->diffForHumans(), 'created_at'],
        ];
    }

    protected function rowActions(Model $item): array
    {
        return $item->url ? [['Open', $item->url, 'box-arrow-in-right']] : [];
    }
}
