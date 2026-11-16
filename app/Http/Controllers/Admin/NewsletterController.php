<?php

namespace App\Http\Controllers\Admin;

use App\Models\NewsletterSubscriber;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class NewsletterController extends ResourceController
{
    protected string $model = NewsletterSubscriber::class;

    protected string $route = 'admin.newsletter';

    protected string $title = 'Newsletter Subscribers';

    protected string $singular = 'Subscriber';

    protected string $icon = 'envelope-at';

    protected array $searchable = ['email', 'name'];

    protected ?string $dateColumn = 'created_at';

    protected int $perPage = 30;

    public const STATUSES = ['subscribed' => 'Subscribed', 'unsubscribed' => 'Unsubscribed'];

    protected function fields(?Model $item): array
    {
        return [
            ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'col' => 6, 'rules' => ['required', 'email', 'max:150', Rule::unique('newsletter_subscribers', 'email')->ignore($item?->getKey())]],
            ['name' => 'name', 'label' => 'Name', 'col' => 6, 'rules' => 'nullable|string|max:120'],
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'col' => 6, 'options' => self::STATUSES, 'rules' => 'required|in:subscribed,unsubscribed', 'default' => 'subscribed'],
            ['name' => 'source', 'label' => 'Source', 'col' => 6, 'rules' => 'nullable|string|max:30', 'default' => 'manual'],
        ];
    }

    protected function filters(): array
    {
        return ['status' => ['Status', self::STATUSES]];
    }

    protected function columns(): array
    {
        return [
            ['Subscriber', fn ($s) => static::titleCell($s->email, $s->name), 'email'],
            ['Status', fn ($s) => static::badge(self::STATUSES[$s->status] ?? $s->status, $s->status === 'subscribed' ? 'success' : 'secondary'), 'status'],
            ['Source', fn ($s) => $s->source],
            ['Joined', fn ($s) => $s->created_at?->format('d M Y'), 'created_at'],
        ];
    }

    protected function exportColumns(): array
    {
        return ['Email' => 'email', 'Name' => 'name', 'Status' => 'status', 'Source' => 'source', 'Joined' => fn ($s) => $s->created_at?->format('Y-m-d')];
    }
}
