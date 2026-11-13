@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
@php $admin = auth('admin')->user(); @endphp
<div class="page-head">
    <div>
        <h1>{{ $greeting }}, {{ \Illuminate\Support\Str::before($admin->name, ' ') }} 👋</h1>
        <p class="sub">Here’s what’s happening at {{ setting('site_name') }} today, {{ now('Asia/Kolkata')->format('l, d F Y') }}.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        @if ($admin->canAccess('donations'))<a href="{{ route('admin.donations.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Record donation</a>@endif
        @if ($admin->canAccess('posts'))<a href="{{ route('admin.posts.create') }}" class="btn btn-light border"><i class="bi bi-pencil-square"></i> Write post</a>@endif
    </div>
</div>

<div class="stat-grid mb-4">
    @foreach ($stats as [$label, $value, $icon, $tone, $hint, $url])
        <a href="{{ $url }}" class="stat-card">
            <span class="ic tone-{{ $tone }}"><i class="bi bi-{{ $icon }}"></i></span>
            <span class="min-w-0">
                <span class="label d-block">{{ $label }}</span>
                <span class="value d-block">{{ $value }}</span>
                <span class="hint d-block text-truncate">{{ $hint }}</span>
            </span>
        </a>
    @endforeach
</div>

@if ($chart)
<div class="row g-4 mb-4">
    <div class="col-xxl-8">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Donations — last 12 months</span>
                @if ($admin->canAccess('analytics'))<a href="{{ route('admin.analytics.index') }}" class="small fw-semibold text-decoration-none">Full report <i class="bi bi-arrow-right"></i></a>@endif
            </div>
            <div class="card-body"><div style="height: 300px"><canvas data-chart="{{ json_encode(['labels' => $chart['labels'], 'values' => $chart['values'], 'money' => true, 'label' => 'Donations']) }}" aria-label="Line chart of confirmed donations per month" role="img"></canvas></div></div>
        </div>
    </div>
    <div class="col-xxl-4">
        <div class="card h-100">
            <div class="card-header">By payment method</div>
            <div class="card-body">
                @if (count($chart['methods']['values']))
                    <div style="height: 300px"><canvas data-chart="{{ json_encode(['type' => 'doughnut', 'labels' => $chart['methods']['labels'], 'values' => $chart['methods']['values'], 'money' => true]) }}" aria-label="Donations by payment method" role="img"></canvas></div>
                @else
                    <p class="text-muted text-center py-5 mb-0">No confirmed donations yet.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

<div class="row g-4">
    @if ($admin->canAccess('donations'))
    <div class="col-xl-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between"><span>Recent donations</span><a href="{{ route('admin.donations.index') }}" class="small fw-semibold text-decoration-none">View all</a></div>
            <div class="table-responsive">
                <table class="table table-hover table-stack align-middle mb-0">
                    <thead><tr><th>Donor</th><th>Amount</th><th>Status</th><th>Date</th></tr></thead>
                    <tbody>
                        @forelse ($recentDonations as $d)
                            <tr>
                                <td data-label="Donor"><a href="{{ route('admin.donations.edit', $d->id) }}" class="fw-semibold text-dark text-decoration-none">{{ $d->donor?->name ?? 'Unknown' }}</a><div class="small text-muted">{{ $d->receipt_no }}</div></td>
                                <td data-label="Amount" class="fw-semibold">{{ inr($d->amount) }}</td>
                                <td data-label="Status"><span class="badge-soft badge-soft--{{ ['confirmed' => 'success', 'pending' => 'warning', 'failed' => 'danger'][$d->status] ?? 'secondary' }}">{{ ucfirst($d->status) }}</span></td>
                                <td data-label="Date" class="small text-muted">{{ $d->donated_at?->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">No donations yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    @if ($admin->canAccess('enquiries'))
    <div class="col-xl-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between"><span>Recent enquiries</span><a href="{{ route('admin.enquiries.index') }}" class="small fw-semibold text-decoration-none">View all</a></div>
            <div class="list-group list-group-flush">
                @forelse ($recentEnquiries as $e)
                    <a href="{{ route('admin.enquiries.edit', $e->id) }}" class="list-group-item list-group-item-action d-flex gap-3 py-3">
                        <span class="ic tone-{{ $e->status === 'new' ? 'danger' : 'secondary' }} rounded-3 d-grid flex-shrink-0" style="width:2.4rem;height:2.4rem;place-items:center"><i class="bi bi-{{ ['contact' => 'envelope', 'partnership' => 'building', 'career' => 'briefcase', 'internship' => 'mortarboard'][$e->type] ?? 'envelope' }}"></i></span>
                        <span class="min-w-0 flex-grow-1">
                            <span class="d-flex justify-content-between gap-2"><strong class="text-truncate">{{ $e->name }}</strong><small class="text-muted text-nowrap">{{ $e->created_at->diffForHumans(short: true) }}</small></span>
                            <span class="small text-muted d-block text-truncate">{{ \App\Models\Enquiry::TYPES[$e->type] ?? '' }} · {{ $e->subject ?: \Illuminate\Support\Str::limit($e->message, 60) }}</span>
                        </span>
                    </a>
                @empty
                    <div class="list-group-item text-center text-muted py-4">No enquiries yet.</div>
                @endforelse
            </div>
        </div>
    </div>
    @endif

    @if ($admin->canAccess('volunteers') && $recentVolunteers->isNotEmpty())
    <div class="col-xl-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between"><span>New volunteers</span><a href="{{ route('admin.volunteers.index') }}" class="small fw-semibold text-decoration-none">View all</a></div>
            <div class="list-group list-group-flush">
                @foreach ($recentVolunteers as $v)
                    <a href="{{ route('admin.volunteers.edit', $v->id) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center gap-2 py-3">
                        <span class="min-w-0"><strong class="d-block text-truncate">{{ $v->name }}</strong><small class="text-muted">{{ $v->city }} · {{ \Illuminate\Support\Str::limit(implode(', ', $v->interests ?? []), 50) }}</small></span>
                        <span class="badge-soft badge-soft--{{ $v->status === 'approved' ? 'success' : ($v->status === 'new' ? 'warning' : 'danger') }}">{{ ucfirst($v->status) }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    @if ($admin->canAccess('campaigns') && $campaigns->isNotEmpty())
    <div class="col-xl-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between"><span>Campaign progress</span><a href="{{ route('admin.campaigns.index') }}" class="small fw-semibold text-decoration-none">Manage</a></div>
            <div class="card-body d-grid gap-3">
                @foreach ($campaigns as $c)
                    <div>
                        <div class="d-flex justify-content-between small mb-1"><a href="{{ route('admin.campaigns.edit', $c->id) }}" class="fw-semibold text-dark text-decoration-none text-truncate me-2">{{ $c->title }}</a><span class="text-muted text-nowrap">{{ inr($c->raised_amount) }} / {{ inr($c->goal_amount) }}</span></div>
                        <div class="progress" style="height:8px" role="progressbar" aria-valuenow="{{ $c->progress() }}" aria-valuemin="0" aria-valuemax="100" aria-label="{{ $c->title }} progress"><div class="progress-bar" style="width: {{ $c->progress() }}%"></div></div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
