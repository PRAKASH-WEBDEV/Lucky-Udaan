<?php

namespace App\Http\Controllers\Admin;

use App\Models\OfferLetter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class OfferLetterController extends ResourceController
{
    protected string $model = OfferLetter::class;

    protected string $route = 'admin.offer-letters';

    protected string $title = 'Offer Letters';

    protected string $singular = 'Offer letter';

    protected string $icon = 'envelope-paper-heart';

    protected array $searchable = ['candidate_name', 'designation', 'email'];

    protected string $sort = 'id';

    protected function fields(?Model $item): array
    {
        return [
            ['name' => 'candidate_name', 'label' => 'Candidate name', 'col' => 6, 'rules' => 'required|string|max:150'],
            ['name' => 'designation', 'label' => 'Position offered', 'col' => 6, 'rules' => 'required|string|max:120'],
            ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'col' => 4, 'rules' => 'nullable|email|max:150'],
            ['name' => 'phone', 'label' => 'Phone', 'col' => 4, 'rules' => 'nullable|string|max:30'],
            ['name' => 'department', 'label' => 'Department', 'col' => 4, 'rules' => 'nullable|string|max:120'],
            ['name' => 'location', 'label' => 'Work location', 'col' => 4, 'rules' => 'nullable|string|max:150', 'default' => setting('city')],
            ['name' => 'joining_date', 'label' => 'Joining date', 'type' => 'date', 'col' => 4, 'rules' => 'nullable|date'],
            ['name' => 'ctc_annual', 'label' => 'Annual CTC (₹)', 'type' => 'number', 'col' => 4, 'rules' => 'nullable|numeric|min:0|max:99999999'],
            ['name' => 'body', 'label' => 'Letter text', 'type' => 'editor', 'help' => 'Leave blank to use the standard offer letter text. It is printed on the organisation letterhead.'],
            ['name' => 'issued_on', 'label' => 'Issued on', 'type' => 'date', 'side' => true, 'rules' => 'nullable|date', 'default' => today()->format('Y-m-d')],
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'side' => true, 'options' => OfferLetter::STATUSES, 'rules' => 'required|in:'.implode(',', array_keys(OfferLetter::STATUSES)), 'default' => 'draft'],
        ];
    }

    protected function prepare(array $data, ?Model $item, Request $request): array
    {
        $data['issued_on'] ??= today();

        return $data;
    }

    protected function filters(): array
    {
        return ['status' => ['Status', OfferLetter::STATUSES]];
    }

    protected function columns(): array
    {
        return [
            ['Candidate', fn ($o) => static::titleCell($o->candidate_name, $o->designation), 'candidate_name'],
            ['Joining', fn ($o) => $o->joining_date?->format('d M Y') ?? '—', 'joining_date'],
            ['CTC', fn ($o) => $o->ctc_annual ? inr($o->ctc_annual).' / yr' : '—'],
            ['Issued', fn ($o) => $o->issued_on?->format('d M Y') ?? '—', 'issued_on'],
            ['Status', fn ($o) => static::badge(OfferLetter::STATUSES[$o->status] ?? $o->status, match ($o->status) { 'accepted' => 'success', 'declined' => 'danger', 'sent' => 'info', default => 'warning' })],
        ];
    }

    protected function rowActions(Model $item): array
    {
        return [['Print letter', route('admin.offer-letters.print', $item->getKey()), 'printer']];
    }
}
