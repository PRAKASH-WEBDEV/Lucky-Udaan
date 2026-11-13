<?php

namespace App\Http\Controllers\Admin;

use App\Models\Campaign;
use App\Models\Donation;
use App\Models\Donor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\ValidationException;

class DonationController extends ResourceController
{
    protected string $model = Donation::class;

    protected string $route = 'admin.donations';

    protected string $title = 'Donations';

    protected string $singular = 'Donation';

    protected string $icon = 'cash-coin';

    protected array $searchable = ['receipt_no', 'transaction_ref'];

    protected array $with = ['donor', 'campaign'];

    protected string $sort = 'donated_at';

    protected ?string $dateColumn = 'donated_at';

    protected function filteredQuery(Request $request): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::filteredQuery($request->duplicate($request->except('q')));
        if ($q = trim((string) $request->get('q'))) {
            $query->where(fn ($w) => $w->whereLike('receipt_no', "%$q%")->orWhereLike('transaction_ref', "%$q%")
                ->orWhereHas('donor', fn ($d) => $d->whereLike('name', "%$q%")->orWhereLike('phone', "%$q%")->orWhereLike('email', "%$q%")));
        }

        return $query;
    }

    protected function fields(?Model $item): array
    {
        return [
            ['type' => 'section', 'label' => 'Donor'],
            ['name' => 'donor_id', 'label' => 'Existing donor', 'type' => 'select', 'empty' => '— New donor (fill below) —',
                'options' => Donor::orderBy('name')->get()->mapWithKeys(fn ($d) => [$d->id => $d->name.($d->phone ? ' · '.$d->phone : '')])->all(),
                'rules' => 'nullable|exists:donors,id'],
            ['name' => 'donor_name', 'label' => 'New donor name', 'col' => 4, 'rules' => 'nullable|required_without:donor_id|string|max:120', 'hide_on_edit' => true],
            ['name' => 'donor_phone', 'label' => 'Phone', 'col' => 4, 'rules' => 'nullable|string|max:20', 'hide_on_edit' => true],
            ['name' => 'donor_email', 'label' => 'Email', 'type' => 'email', 'col' => 4, 'rules' => 'nullable|email|max:150', 'hide_on_edit' => true],
            ['type' => 'section', 'label' => 'Donation'],
            ['name' => 'amount', 'label' => 'Amount (₹)', 'type' => 'number', 'col' => 4, 'rules' => 'required|numeric|min:1|max:99999999', 'step' => '0.01'],
            ['name' => 'method', 'label' => 'Payment method', 'type' => 'select', 'col' => 4, 'options' => Donation::METHODS, 'rules' => 'required|in:'.implode(',', array_keys(Donation::METHODS))],
            ['name' => 'donated_at', 'label' => 'Date', 'type' => 'datetime', 'col' => 4, 'rules' => 'nullable|date'],
            ['name' => 'transaction_ref', 'label' => 'Transaction / UTR / cheque no.', 'col' => 6, 'rules' => 'nullable|string|max:120'],
            ['name' => 'campaign_id', 'label' => 'Campaign', 'type' => 'select', 'col' => 6, 'empty' => 'General fund',
                'options' => Campaign::orderBy('title')->pluck('title', 'id')->all(), 'rules' => 'nullable|exists:campaigns,id'],
            ['name' => 'message', 'label' => 'Donor message', 'type' => 'textarea', 'rows' => 2, 'rules' => 'nullable|string|max:1000'],
            ['name' => 'admin_note', 'label' => 'Internal note', 'type' => 'textarea', 'rows' => 2, 'rules' => 'nullable|string|max:1000'],
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'side' => true, 'options' => Donation::STATUSES, 'rules' => 'required|in:pending,confirmed,failed',
                'default' => 'confirmed', 'help' => 'Confirm once the money is received in the bank account. Only confirmed donations count towards totals and receipts.'],
            ['name' => 'wants_80g', 'label' => 'Donor wants 80G receipt', 'type' => 'switch', 'side' => true],
            ...($item?->exists ? [['type' => 'static', 'side' => true, 'label' => 'Receipt', 'value' => fn ($d) => new HtmlString(
                '<div class="fw-semibold">'.e($d->receipt_no).'</div><div class="small text-muted">Source: '.e(ucfirst($d->source)).'</div>')]] : []),
        ];
    }

    protected function prepare(array $data, ?Model $item, Request $request): array
    {
        $newDonor = ['name' => $data['donor_name'] ?? null, 'phone' => $data['donor_phone'] ?? null, 'email' => $data['donor_email'] ?? null];
        unset($data['donor_name'], $data['donor_phone'], $data['donor_email']);

        if (empty($data['donor_id'])) {
            if (! $newDonor['name']) {
                if ($item?->donor_id) {
                    $data['donor_id'] = $item->donor_id;

                    return $data;
                }
                throw ValidationException::withMessages(['donor_name' => 'Choose an existing donor or enter the new donor\'s name.']);
            }
            $data['donor_id'] = Donor::create($newDonor)->id;
        }
        if (! $item) {
            $data['source'] = 'offline';
        }
        $data['donated_at'] ??= $item?->donated_at ?? now();

        return $data;
    }

    protected function filters(): array
    {
        return [
            'status' => ['Status', Donation::STATUSES],
            'method' => ['Method', Donation::METHODS],
            'campaign_id' => ['Campaign', Campaign::orderBy('title')->pluck('title', 'id')->all()],
        ];
    }

    protected function columns(): array
    {
        return [
            ['Receipt', fn ($d) => static::titleCell($d->receipt_no, $d->donated_at?->format('d M Y, h:i A')), 'receipt_no'],
            ['Donor', fn ($d) => static::titleCell($d->donor?->name ?? 'Unknown', $d->donor?->phone)],
            ['Amount', fn ($d) => new HtmlString('<span class="fw-semibold">'.e(inr($d->amount)).'</span>'), 'amount'],
            ['Method', fn ($d) => $d->methodLabel(), 'method'],
            ['Campaign', fn ($d) => $d->campaign?->title ?? 'General fund'],
            ['Status', fn ($d) => static::badge(ucfirst($d->status), static::statusTone($d->status)), 'status'],
        ];
    }

    protected function rowActions(Model $item): array
    {
        return $item->status === 'confirmed' ? [['Receipt', route('admin.donations.receipt', $item->id), 'receipt']] : [];
    }

    protected function exportColumns(): array
    {
        return [
            'Receipt No' => 'receipt_no',
            'Date' => fn ($d) => $d->donated_at?->format('Y-m-d H:i'),
            'Donor' => 'donor.name',
            'Phone' => 'donor.phone',
            'Email' => 'donor.email',
            'PAN' => 'donor.pan',
            'Amount' => 'amount',
            'Method' => fn ($d) => $d->methodLabel(),
            'Transaction Ref' => 'transaction_ref',
            'Campaign' => fn ($d) => $d->campaign?->title ?? 'General fund',
            'Status' => 'status',
            '80G' => fn ($d) => $d->wants_80g ? 'Yes' : 'No',
            'Source' => 'source',
        ];
    }

    public function receipt(int $id)
    {
        $donation = Donation::with('donor', 'campaign')->findOrFail($id);
        abort_unless($donation->status === 'confirmed', 404);

        return view('admin.donations.receipt', compact('donation'));
    }
}
