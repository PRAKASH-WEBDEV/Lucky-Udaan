<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\Enquiry;
use App\Models\Member;
use App\Models\Volunteer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/** Reports & Analytics: date-filtered summaries with CSV and PDF export. */
class AnalyticsController extends Controller
{
    public const REPORTS = [
        'donations' => 'Donations',
        'volunteers' => 'Volunteers',
        'members' => 'Members',
        'enquiries' => 'Enquiries & applications',
    ];

    public function index(Request $request)
    {
        return view('admin.analytics.index', $this->build($request));
    }

    public function export(Request $request, string $format)
    {
        abort_unless(in_array($format, ['csv', 'pdf'], true), 404);
        $data = $this->build($request);
        $name = $data['report'].'-report-'.$data['from']->format('Ymd').'-'.$data['to']->format('Ymd');

        if ($format === 'pdf') {
            return Pdf::loadView('admin.analytics.pdf', $data)->setPaper('a4', 'landscape')->download($name.'.pdf');
        }

        return response()->streamDownload(function () use ($data) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, $data['headings']);
            foreach ($data['rows'] as $row) {
                fputcsv($out, $row);
            }
            fclose($out);
        }, $name.'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function build(Request $request): array
    {
        $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
            'report' => 'nullable|in:'.implode(',', array_keys(self::REPORTS)),
            'status' => 'nullable|string|max:20',
        ]);

        // Default range: current Indian financial year to date.
        $fyStart = now()->month >= 4 ? now()->startOfYear()->addMonths(3) : now()->subYear()->startOfYear()->addMonths(3);
        $from = $request->date('from')?->startOfDay() ?? $fyStart->startOfDay();
        $to = $request->date('to')?->endOfDay() ?? now()->endOfDay();
        $report = $request->get('report', 'donations');
        $status = $request->get('status');

        [$headings, $rows, $summary, $series, $statuses] = match ($report) {
            'volunteers' => $this->volunteers($from, $to, $status),
            'members' => $this->members($from, $to, $status),
            'enquiries' => $this->enquiries($from, $to, $status),
            default => $this->donations($from, $to, $status),
        };

        return [
            'reports' => self::REPORTS,
            'report' => $report,
            'reportTitle' => self::REPORTS[$report] ?? 'Donations',
            'from' => $from,
            'to' => $to,
            'status' => $status,
            'statuses' => $statuses,
            'headings' => $headings,
            'rows' => $rows,
            'summary' => $summary,
            'series' => $series,
        ];
    }

    /** Group dated records into daily (≤ 62 days) or monthly buckets for the chart. */
    private function series(Collection $items, Carbon $from, Carbon $to, string $dateField, ?callable $value = null): array
    {
        $monthly = $from->diffInDays($to) > 62;
        $fmt = $monthly ? 'Y-m' : 'Y-m-d';
        $grouped = $items->groupBy(fn ($i) => $i->{$dateField}->format($fmt))
            ->map(fn ($g) => $value ? $g->sum($value) : $g->count());

        $labels = [];
        $values = [];
        $cursor = $from->copy()->startOf($monthly ? 'month' : 'day');
        while ($cursor->lte($to)) {
            $labels[] = $cursor->format($monthly ? 'M Y' : 'd M');
            $values[] = round((float) ($grouped[$cursor->format($fmt)] ?? 0), 2);
            $monthly ? $cursor->addMonthNoOverflow() : $cursor->addDay();
        }

        return ['labels' => $labels, 'values' => $values];
    }

    private function donations(Carbon $from, Carbon $to, ?string $status): array
    {
        $items = Donation::with('donor', 'campaign')->whereBetween('donated_at', [$from, $to])
            ->when($status, fn ($q) => $q->where('status', $status))->orderBy('donated_at')->get();
        $confirmed = $items->where('status', 'confirmed');

        return [
            ['Receipt', 'Date', 'Donor', 'Phone', 'Amount (INR)', 'Method', 'Campaign', 'Status'],
            $items->map(fn ($d) => [$d->receipt_no, $d->donated_at->format('d-m-Y'), $d->donor?->name, $d->donor?->phone,
                number_format((float) $d->amount, 2, '.', ''), $d->methodLabel(), $d->campaign?->title ?? 'General fund', ucfirst($d->status)])->all(),
            [
                ['Confirmed amount', inr($confirmed->sum('amount'))],
                ['Confirmed donations', $confirmed->count()],
                ['Average gift', inr($confirmed->count() ? $confirmed->avg('amount') : 0)],
                ['Pending pledges', $items->where('status', 'pending')->count().' · '.inr($items->where('status', 'pending')->sum('amount'))],
                ['Unique donors', $confirmed->pluck('donor_id')->unique()->count()],
            ],
            $this->series($confirmed, $from, $to, 'donated_at', fn ($d) => (float) $d->amount) + ['label' => 'Confirmed donations (₹)'],
            Donation::STATUSES,
        ];
    }

    private function volunteers(Carbon $from, Carbon $to, ?string $status): array
    {
        $items = Volunteer::whereBetween('created_at', [$from, $to])->when($status, fn ($q) => $q->where('status', $status))->orderBy('created_at')->get();

        return [
            ['Name', 'Email', 'Phone', 'City', 'Interests', 'Availability', 'Status', 'Applied on'],
            $items->map(fn ($v) => [$v->name, $v->email, $v->phone, $v->city, implode(', ', $v->interests ?? []), $v->availability, ucfirst($v->status), $v->created_at->format('d-m-Y')])->all(),
            [
                ['Applications', $items->count()],
                ['Approved', $items->where('status', 'approved')->count()],
                ['Awaiting review', $items->where('status', 'new')->count()],
                ['Top interest', collect($items->pluck('interests')->flatten())->countBy()->sortDesc()->keys()->first() ?? '—'],
            ],
            $this->series($items, $from, $to, 'created_at') + ['label' => 'Volunteer applications'],
            ['new' => 'New', 'approved' => 'Approved', 'rejected' => 'Rejected'],
        ];
    }

    private function members(Carbon $from, Carbon $to, ?string $status): array
    {
        $items = Member::whereBetween('created_at', [$from, $to])->when($status, fn ($q) => $q->where('status', $status))->orderBy('created_at')->get();

        return [
            ['Membership No', 'Name', 'Email', 'Phone', 'City', 'Type', 'Status', 'Applied on'],
            $items->map(fn ($m) => [$m->membership_no, $m->name, $m->email, $m->phone, $m->city, Member::TYPES[$m->membership_type] ?? $m->membership_type, ucfirst($m->status), $m->created_at->format('d-m-Y')])->all(),
            [
                ['Applications', $items->count()],
                ['Approved', $items->where('status', 'approved')->count()],
                ['Pending', $items->where('status', 'pending')->count()],
                ['Life members', $items->where('membership_type', 'life')->count()],
            ],
            $this->series($items, $from, $to, 'created_at') + ['label' => 'Membership applications'],
            ['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'],
        ];
    }

    private function enquiries(Carbon $from, Carbon $to, ?string $status): array
    {
        $items = Enquiry::with('opening')->whereBetween('created_at', [$from, $to])->when($status, fn ($q) => $q->where('status', $status))->orderBy('created_at')->get();
        $byType = $items->countBy('type');

        return [
            ['Type', 'Name', 'Email', 'Phone', 'Subject / position', 'Status', 'Received'],
            $items->map(fn ($e) => [Enquiry::TYPES[$e->type] ?? $e->type, $e->name, $e->email, $e->phone, $e->subject ?: $e->opening?->title, ucfirst($e->status), $e->created_at->format('d-m-Y')])->all(),
            array_merge([['Total received', $items->count()]], collect(Enquiry::TYPES)->map(fn ($label, $key) => [$label, $byType[$key] ?? 0])->values()->all()),
            $this->series($items, $from, $to, 'created_at') + ['label' => 'Enquiries received'],
            ['new' => 'New', 'read' => 'Read', 'replied' => 'Replied', 'closed' => 'Closed'],
        ];
    }
}
