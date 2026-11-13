<?php

namespace App\Http\Controllers\Admin;

use App\Models\Donor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

class DonorController extends ResourceController
{
    protected string $model = Donor::class;

    protected string $route = 'admin.donors';

    protected string $title = 'Donors';

    protected string $singular = 'Donor';

    protected string $icon = 'person-hearts';

    protected array $searchable = ['name', 'email', 'phone', 'city', 'pan'];

    protected function newQuery(): Builder
    {
        return Donor::query()
            ->withCount(['donations as confirmed_count' => fn ($q) => $q->where('status', 'confirmed')])
            ->withSum(['donations as confirmed_total' => fn ($q) => $q->where('status', 'confirmed')], 'amount');
    }

    protected function fields(?Model $item): array
    {
        return [
            ['name' => 'name', 'label' => 'Full name', 'rules' => 'required|string|max:120'],
            ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'col' => 6, 'rules' => 'nullable|email|max:150'],
            ['name' => 'phone', 'label' => 'Phone', 'col' => 6, 'rules' => 'nullable|string|max:20'],
            ['name' => 'pan', 'label' => 'PAN (for 80G receipts)', 'col' => 6, 'rules' => ['nullable', 'regex:/^[A-Za-z]{5}[0-9]{4}[A-Za-z]$/']],
            ['name' => 'city', 'label' => 'City', 'col' => 6, 'rules' => 'nullable|string|max:80'],
            ['name' => 'state', 'label' => 'State', 'col' => 6, 'rules' => 'nullable|string|max:80'],
            ['name' => 'address', 'label' => 'Address', 'type' => 'textarea', 'rows' => 2, 'rules' => 'nullable|string|max:500'],
            ['name' => 'notes', 'label' => 'Internal notes', 'type' => 'textarea', 'rows' => 3, 'rules' => 'nullable|string|max:2000'],
            ['name' => 'is_active', 'label' => 'Active', 'type' => 'switch', 'side' => true, 'default' => true],
            ...($item?->exists ? [['type' => 'static', 'label' => 'Donation history', 'value' => fn ($d) => $this->history($d)]] : []),
        ];
    }

    protected function prepare(array $data, ?Model $item, \Illuminate\Http\Request $request): array
    {
        if (! empty($data['pan'])) {
            $data['pan'] = strtoupper($data['pan']);
        }

        return $data;
    }

    private function history(Donor $donor): HtmlString
    {
        $rows = $donor->donations()->latest('donated_at')->limit(20)->get();
        if ($rows->isEmpty()) {
            return new HtmlString('<p class="text-muted mb-0">No donations yet.</p>');
        }
        $html = '<div class="table-responsive"><table class="table table-sm align-middle mb-0"><thead><tr><th>Receipt</th><th>Date</th><th>Amount</th><th>Status</th></tr></thead><tbody>';
        foreach ($rows as $d) {
            $html .= '<tr><td><a href="'.e(route('admin.donations.edit', $d->id)).'">'.e($d->receipt_no).'</a></td><td>'.e($d->donated_at?->format('d M Y'))
                .'</td><td>'.e(inr($d->amount)).'</td><td>'.static::badge(ucfirst($d->status), static::statusTone($d->status)).'</td></tr>';
        }

        return new HtmlString($html.'</tbody></table></div>');
    }

    protected function columns(): array
    {
        return [
            ['Donor', fn ($d) => static::titleCell($d->name, $d->email), 'name'],
            ['Phone', fn ($d) => $d->phone ?: '—'],
            ['City', fn ($d) => $d->city ?: '—', 'city'],
            ['Donations', fn ($d) => $d->confirmed_count, 'confirmed_count'],
            ['Total given', fn ($d) => new HtmlString('<span class="fw-semibold">'.e(inr($d->confirmed_total ?? 0)).'</span>'), 'confirmed_total'],
        ];
    }

    protected function exportColumns(): array
    {
        return [
            'Name' => 'name', 'Email' => 'email', 'Phone' => 'phone', 'PAN' => 'pan', 'City' => 'city', 'State' => 'state',
            'Address' => 'address', 'Confirmed donations' => 'confirmed_count', 'Total (INR)' => fn ($d) => (float) $d->confirmed_total,
            'Added on' => fn ($d) => $d->created_at?->format('Y-m-d'),
        ];
    }

    protected function beforeDelete(Model $item): void
    {
        if ($item->donations()->exists()) {
            abort(back()->with('error', 'This donor has donations on record. Mark the donor inactive instead of deleting.'));
        }
    }
}
