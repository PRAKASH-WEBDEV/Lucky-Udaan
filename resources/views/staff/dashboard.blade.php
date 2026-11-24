@extends('staff.layout')
@section('title', 'My attendance')

@php
    $tone = fn ($s) => match ($s) { 'present' => 'success', 'late', 'half_day' => 'warning', 'absent' => 'danger', default => 'info' };
    $time = fn ($t) => $t ? \Illuminate\Support\Carbon::createFromTimeString($t)->format('h:i A') : '—';
    $fmt = fn ($t) => \Illuminate\Support\Carbon::createFromTimeString($t)->format('h:i A');
@endphp

@section('content')
<h1 class="h4 mb-1">Hello, {{ \Illuminate\Support\Str::of($employee->name)->before(' ') }}</h1>
<p class="text-muted">{{ trim($employee->designation.' · '.$employee->employee_code, ' ·') }}</p>

<div class="row g-3 mb-4">
    <div class="col-lg-7">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body p-4">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                    <div>
                        <div class="text-muted small">Today</div>
                        <div class="fs-5 fw-semibold">{{ today()->format('l, d F Y') }}</div>
                        <div class="display-6 fw-bold mt-1" id="clock">{{ now()->format('h:i A') }}</div>
                    </div>
                    @if ($today)
                        <span class="badge text-bg-{{ $tone($today->status) }} fs-6">{{ \App\Models\Attendance::STATUSES[$today->status] ?? $today->status }}</span>
                    @endif
                </div>

                <div class="row g-3 mt-2">
                    <div class="col-6"><div class="border rounded p-3"><div class="small text-muted">Check-in</div><div class="fs-5 fw-semibold">{{ $time($today?->check_in) }}</div></div></div>
                    <div class="col-6"><div class="border rounded p-3"><div class="small text-muted">Check-out</div><div class="fs-5 fw-semibold">{{ $time($today?->check_out) }}</div></div></div>
                </div>

                <div class="mt-4">
                    @if (! $today?->check_in && ! in_array($today?->status, ['leave', 'holiday'], true))
                        <form method="post" action="{{ route('staff.check-in') }}">@csrf
                            <button class="btn btn-success btn-lg w-100"><i class="bi bi-box-arrow-in-right"></i> Check in now</button>
                        </form>
                    @elseif ($today?->check_in && ! $today->check_out)
                        <form method="post" action="{{ route('staff.check-out') }}" onsubmit="return confirm('Check out for today?')">@csrf
                            <button class="btn btn-danger btn-lg w-100"><i class="bi bi-box-arrow-right"></i> Check out now</button>
                        </form>
                    @elseif ($today?->check_out)
                        <div class="alert alert-success mb-0"><i class="bi bi-check2-all"></i> Your attendance for today is complete.</div>
                    @else
                        <div class="alert alert-info mb-0">Today is marked as {{ \App\Models\Attendance::STATUSES[$today->status] }}.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body p-4">
                <h2 class="h6 text-uppercase text-muted mb-3"><i class="bi bi-clock"></i> Office timings</h2>
                <dl class="row mb-0 small">
                    <dt class="col-6 fw-normal text-muted">Office hours</dt>
                    <dd class="col-6 fw-semibold">{{ $fmt(setting('hr_office_start', '10:00')) }} – {{ $fmt(setting('hr_office_end', '18:00')) }}</dd>
                    <dt class="col-6 fw-normal text-muted">Late after</dt>
                    <dd class="col-6 fw-semibold">{{ \Illuminate\Support\Carbon::createFromTimeString(setting('hr_office_start', '10:00'))->addMinutes((int) setting('hr_grace_minutes', 15))->format('h:i A') }}</dd>
                    <dt class="col-6 fw-normal text-muted">Full day</dt>
                    <dd class="col-6 fw-semibold">{{ setting('hr_half_day_hours', 6) }} hours or more</dd>
                    <dt class="col-6 fw-normal text-muted">Weekly off</dt>
                    <dd class="col-6 fw-semibold">{{ setting('hr_weekly_off', 'Sunday') }}</dd>
                </dl>
                @if ($holidays->isNotEmpty())
                    <h3 class="h6 text-uppercase text-muted mt-4 mb-2"><i class="bi bi-calendar-heart"></i> Upcoming holidays</h3>
                    <ul class="list-unstyled small mb-0">
                        @foreach ($holidays as $h)<li class="d-flex justify-content-between py-1 border-bottom"><span>{{ $h->title }}</span><span class="text-muted">{{ $h->date->format('D, d M') }}</span></li>@endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</div>

@if ($notices->isNotEmpty())
    <div class="d-flex justify-content-between align-items-end mb-2">
        <h2 class="h5 mb-0">Latest notices</h2>
        <a href="{{ route('staff.notices') }}" class="small">See all →</a>
    </div>
    <div class="card shadow-sm border-0 mb-4"><div class="list-group list-group-flush">
        @foreach ($notices as $n)
            <div class="list-group-item d-flex gap-3 py-3">
                <i class="bi bi-{{ $n->icon }} text-{{ $n->tone }} fs-5"></i>
                <div><div class="fw-semibold">{{ $n->title }}</div><div class="small text-muted">{{ $n->at?->diffForHumans() }}</div></div>
            </div>
        @endforeach
    </div></div>
@endif

<div class="d-flex justify-content-between align-items-end mb-2">
    <h2 class="h5 mb-0">{{ today()->format('F Y') }}</h2>
    <div class="small text-muted">
        @foreach (\App\Models\Attendance::STATUSES as $k => $label)
            @if ($counts[$k] ?? 0)<span class="me-2">{{ $label }}: <strong>{{ $counts[$k] }}</strong></span>@endif
        @endforeach
    </div>
</div>
<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table mb-0 align-middle">
            <thead><tr><th>Date</th><th>In</th><th>Out</th><th>Status</th></tr></thead>
            <tbody>
            @forelse ($month as $a)
                <tr>
                    <td>{{ $a->date->format('D, d M') }}</td>
                    <td>{{ $time($a->check_in) }}</td>
                    <td>{{ $time($a->check_out) }}</td>
                    <td><span class="badge text-bg-{{ $tone($a->status) }}">{{ \App\Models\Attendance::STATUSES[$a->status] ?? $a->status }}</span></td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center text-muted py-4">No attendance recorded this month yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    setInterval(() => { document.getElementById('clock').textContent = new Date().toLocaleTimeString('en-IN', { hour: '2-digit', minute: '2-digit', hour12: true, timeZone: @json(config('app.timezone')) }).toUpperCase(); }, 15000);
</script>
@endpush
