<?php

namespace App\Http\Controllers\Admin;

use App\Models\Campaign;
use App\Models\Expense;
use App\Models\Program;
use Illuminate\Database\Eloquent\Model;

/** Organisation expenses (Finance). */
class ExpenseController extends ResourceController
{
    protected string $model = Expense::class;

    protected string $route = 'admin.expenses';

    protected string $title = 'Expenses';

    protected string $singular = 'Expense';

    protected string $icon = 'cash-stack';

    protected string $uploadDir = 'bills';

    protected array $searchable = ['title', 'category', 'paid_to', 'reference'];

    protected array $with = ['program', 'campaign'];

    protected string $sort = 'date';

    protected ?string $dateColumn = 'date';

    public const CATEGORIES = ['Programme', 'Salaries', 'Rent', 'Utilities', 'Travel', 'Printing & stationery', 'Medical camp', 'Food & relief', 'Events', 'Professional fees', 'Bank charges', 'Other'];

    protected function fields(?Model $item): array
    {
        return [
            ['name' => 'title', 'label' => 'Description', 'col' => 8, 'rules' => 'required|string|max:200'],
            ['name' => 'date', 'label' => 'Date', 'type' => 'date', 'col' => 4, 'rules' => 'required|date', 'default' => today()->format('Y-m-d')],
            ['name' => 'amount', 'label' => 'Amount (₹)', 'type' => 'number', 'col' => 4, 'rules' => 'required|numeric|min:0.01|max:99999999'],
            ['name' => 'category', 'label' => 'Category', 'col' => 4, 'rules' => 'nullable|string|max:60', 'datalist' => self::CATEGORIES],
            ['name' => 'paid_to', 'label' => 'Paid to', 'col' => 4, 'rules' => 'nullable|string|max:150'],
            ['name' => 'payment_method', 'label' => 'Payment method', 'type' => 'select', 'col' => 4, 'empty' => '—', 'options' => ['cash' => 'Cash', 'upi' => 'UPI', 'bank_transfer' => 'Bank transfer', 'cheque' => 'Cheque', 'card' => 'Card'], 'rules' => 'nullable|in:cash,upi,bank_transfer,cheque,card'],
            ['name' => 'reference', 'label' => 'Voucher / reference no.', 'col' => 8, 'rules' => 'nullable|string|max:120'],
            ['name' => 'program_id', 'label' => 'Program', 'type' => 'select', 'col' => 6, 'empty' => '— General —', 'options' => Program::orderBy('title')->pluck('title', 'id')->all(), 'rules' => 'nullable|exists:programs,id'],
            ['name' => 'campaign_id', 'label' => 'Campaign', 'type' => 'select', 'col' => 6, 'empty' => '— None —', 'options' => Campaign::orderByDesc('id')->pluck('title', 'id')->all(), 'rules' => 'nullable|exists:campaigns,id'],
            ['name' => 'notes', 'label' => 'Notes', 'type' => 'textarea', 'rows' => 2, 'rules' => 'nullable|string|max:1000'],
            ['name' => 'bill', 'label' => 'Bill / voucher', 'type' => 'file', 'mimes' => 'pdf,jpg,jpeg,png,webp', 'rules' => 'nullable', 'max' => 5120],
        ];
    }

    protected function filters(): array
    {
        return [
            'category' => ['Category', array_combine(self::CATEGORIES, self::CATEGORIES)],
            'program_id' => ['Program', Program::orderBy('title')->pluck('title', 'id')->all()],
        ];
    }

    protected function columns(): array
    {
        return [
            ['Date', fn ($x) => $x->date->format('d M Y'), 'date'],
            ['Expense', fn ($x) => static::titleCell($x->title, $x->paid_to), 'title'],
            ['Category', fn ($x) => static::badge($x->category, 'secondary'), 'category'],
            ['Program', fn ($x) => $x->program?->title ?? $x->campaign?->title ?? '—'],
            ['Amount', fn ($x) => inr($x->amount, true), 'amount'],
        ];
    }

    protected function exportColumns(): array
    {
        return [
            'Date' => fn ($x) => $x->date->format('Y-m-d'), 'Description' => 'title', 'Category' => 'category', 'Amount' => 'amount',
            'Paid to' => 'paid_to', 'Method' => 'payment_method', 'Reference' => 'reference',
            'Program' => fn ($x) => $x->program?->title, 'Campaign' => fn ($x) => $x->campaign?->title, 'Notes' => 'notes',
        ];
    }
}
