<?php

namespace App\Http\Controllers\Admin;

use App\Models\Subscription;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

/** Subscription & Billing: the organisation's recurring services (hosting, domain, software, etc.). */
class SubscriptionController extends ResourceController
{
    protected string $model = Subscription::class;

    protected string $route = 'admin.subscriptions';

    protected string $title = 'Subscription & Billing';

    protected string $singular = 'Subscription';

    protected string $icon = 'credit-card-2-front';

    protected string $uploadDir = 'invoices';

    protected array $searchable = ['service_name', 'vendor', 'plan'];

    protected string $sort = 'renewal_date';

    protected string $direction = 'asc';

    protected function fields(?Model $item): array
    {
        return [
            ['name' => 'service_name', 'label' => 'Service', 'col' => 6, 'rules' => 'required|string|max:150', 'datalist' => ['Website hosting', 'Domain name', 'Neon database', 'Email (Google Workspace)', 'Accounting software', 'SMS / WhatsApp API', 'Payment gateway']],
            ['name' => 'vendor', 'label' => 'Vendor', 'col' => 6, 'rules' => 'nullable|string|max:150'],
            ['name' => 'plan', 'label' => 'Plan', 'col' => 4, 'rules' => 'nullable|string|max:120'],
            ['name' => 'amount', 'label' => 'Amount (₹)', 'type' => 'number', 'col' => 4, 'rules' => 'required|numeric|min:0|max:9999999', 'default' => 0],
            ['name' => 'billing_cycle', 'label' => 'Billing cycle', 'type' => 'select', 'col' => 4, 'options' => Subscription::CYCLES, 'rules' => 'required|in:'.implode(',', array_keys(Subscription::CYCLES)), 'default' => 'yearly'],
            ['name' => 'start_date', 'label' => 'Started on', 'type' => 'date', 'col' => 4, 'rules' => 'nullable|date'],
            ['name' => 'renewal_date', 'label' => 'Next renewal', 'type' => 'date', 'col' => 4, 'rules' => 'nullable|date'],
            ['name' => 'payment_method', 'label' => 'Paid via', 'col' => 4, 'rules' => 'nullable|string|max:30', 'datalist' => ['UPI', 'Card', 'Bank transfer', 'Cheque', 'Free']],
            ['name' => 'notes', 'label' => 'Notes / login hint (no passwords)', 'type' => 'textarea', 'rows' => 2, 'rules' => 'nullable|string|max:1000'],
            ['name' => 'invoice', 'label' => 'Latest invoice', 'type' => 'file', 'mimes' => 'pdf,jpg,jpeg,png', 'rules' => 'nullable', 'max' => 5120],
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'side' => true, 'options' => Subscription::STATUSES, 'rules' => 'required|in:active,expired,cancelled', 'default' => 'active'],
        ];
    }

    protected function filters(): array
    {
        return ['status' => ['Status', Subscription::STATUSES], 'billing_cycle' => ['Cycle', Subscription::CYCLES]];
    }

    protected function columns(): array
    {
        return [
            ['Service', fn ($s) => static::titleCell($s->service_name, trim($s->vendor.' · '.$s->plan, ' ·')), 'service_name'],
            ['Cost', fn ($s) => inr($s->amount).' / '.strtolower(Subscription::CYCLES[$s->billing_cycle] ?? ''), 'amount'],
            ['Renews', function ($s) {
                if (! $s->renewal_date) {
                    return '—';
                }
                $days = (int) today()->diffInDays($s->renewal_date, false);
                $tone = $days < 0 ? 'danger' : ($days <= 30 ? 'warning' : 'secondary');

                return new HtmlString(e($s->renewal_date->format('d M Y')).' '.static::badge($days < 0 ? 'overdue' : "in {$days} days", $tone));
            }, 'renewal_date'],
            ['Status', fn ($s) => static::badge(Subscription::STATUSES[$s->status] ?? $s->status, static::statusTone($s->status === 'expired' ? 'failed' : $s->status)), 'status'],
        ];
    }
}
