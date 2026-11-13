<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Donation;
use App\Models\Donor;
use App\Models\Enquiry;
use App\Models\Event;
use App\Models\Member;
use App\Models\Volunteer;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $admin = auth('admin')->user();
        $can = fn (string $m) => $admin->canAccess($m);

        $stats = [];
        if ($can('donations')) {
            $thisMonth = Donation::where('status', 'confirmed')->where('donated_at', '>=', now()->startOfMonth())->sum('amount');
            $lastMonth = Donation::where('status', 'confirmed')->whereBetween('donated_at', [now()->subMonthNoOverflow()->startOfMonth(), now()->subMonthNoOverflow()->endOfMonth()])->sum('amount');
            $stats[] = ['Total raised', inr(Donation::where('status', 'confirmed')->sum('amount')), 'cash-stack', 'primary', 'All confirmed donations', route('admin.donations.index', ['status' => 'confirmed'])];
            $stats[] = ['This month', inr($thisMonth), 'graph-up-arrow', 'success',
                $lastMonth > 0 ? (($thisMonth >= $lastMonth ? '+' : '').round(($thisMonth - $lastMonth) / $lastMonth * 100).'% vs last month') : 'Confirmed this month', route('admin.donations.index')];
            $stats[] = ['Pending to verify', Donation::where('status', 'pending')->count(), 'hourglass-split', 'warning', 'Website pledges awaiting bank check', route('admin.donations.index', ['status' => 'pending'])];
        }
        if ($can('donors')) {
            $stats[] = ['Donors', Donor::count(), 'person-hearts', 'info', 'In the donor database', route('admin.donors.index')];
        }
        if ($can('volunteers')) {
            $stats[] = ['Volunteers', Volunteer::count(), 'hand-thumbs-up', 'success', Volunteer::where('status', 'new')->count().' new applications', route('admin.volunteers.index')];
        }
        if ($can('members')) {
            $stats[] = ['Members', Member::where('status', 'approved')->count(), 'person-badge', 'primary', Member::where('status', 'pending')->count().' awaiting approval', route('admin.members.index')];
        }
        if ($can('enquiries')) {
            $stats[] = ['New enquiries', Enquiry::where('status', 'new')->count(), 'envelope-paper', 'danger', 'Unread messages & applications', route('admin.enquiries.index', ['status' => 'new'])];
        }
        if ($can('events')) {
            $stats[] = ['Upcoming events', Event::upcoming()->count(), 'calendar-event', 'info', 'Scheduled on the website', route('admin.events.index')];
        }

        // Last 12 months of confirmed donations for the chart.
        $chart = null;
        if ($can('donations')) {
            $start = now()->startOfMonth()->subMonths(11);
            $rows = Donation::where('status', 'confirmed')->where('donated_at', '>=', $start)
                ->get(['amount', 'donated_at'])
                ->groupBy(fn ($d) => $d->donated_at->format('Y-m'))
                ->map->sum('amount');
            $labels = [];
            $values = [];
            for ($i = 0; $i < 12; $i++) {
                $m = $start->copy()->addMonths($i);
                $labels[] = $m->format('M y');
                $values[] = round((float) ($rows[$m->format('Y-m')] ?? 0));
            }
            $methods = Donation::where('status', 'confirmed')->selectRaw('method, SUM(amount) as total')->groupBy('method')->pluck('total', 'method');
            $chart = [
                'labels' => $labels,
                'values' => $values,
                'methods' => ['labels' => $methods->keys()->map(fn ($k) => Donation::METHODS[$k] ?? $k)->values(), 'values' => $methods->values()->map(fn ($v) => (float) $v)],
            ];
        }

        return view('admin.dashboard', [
            'stats' => $stats,
            'chart' => $chart,
            'recentDonations' => $can('donations') ? Donation::with('donor')->latest('donated_at')->limit(6)->get() : collect(),
            'recentEnquiries' => $can('enquiries') ? Enquiry::latest()->limit(6)->get() : collect(),
            'recentVolunteers' => $can('volunteers') ? Volunteer::latest()->limit(5)->get() : collect(),
            'campaigns' => $can('campaigns') ? Campaign::active()->latest()->limit(4)->get() : collect(),
            'greeting' => match (true) {
                Carbon::now('Asia/Kolkata')->hour < 12 => 'Good morning',
                Carbon::now('Asia/Kolkata')->hour < 17 => 'Good afternoon',
                default => 'Good evening',
            },
        ]);
    }
}
