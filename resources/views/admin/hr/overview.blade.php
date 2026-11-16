@extends('layouts.admin')
@section('title', 'HR Overview')

@section('content')
<div class="page-head">
    <div>
        <h1>HR Overview</h1>
        <p class="sub">Staff, attendance, leave and payroll at a glance — {{ now('Asia/Kolkata')->format('l, d F Y') }}.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('admin.attendance.create') }}" class="btn btn-primary"><i class="bi bi-fingerprint"></i> Manual punch</a>
        <a href="{{ route('admin.employees.create') }}" class="btn btn-light border"><i class="bi bi-person-plus"></i> Add employee</a>
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

<div class="row g-4">
    <div class="col-xl-6">
        <div class="card h-100">
            <div class="card-header">Run payroll</div>
            <div class="card-body">
                <p class="text-muted small">Creates a draft salary entry for every active employee who doesn’t have one for the month. Paid days come from attendance (full month if none was recorded). Review the drafts, then mark them paid to publish salary slips.</p>
                <form method="post" action="{{ route('admin.hr.payroll.generate') }}" class="row g-2 align-items-end">
                    @csrf
                    <div class="col-sm-5"><label class="form-label" for="pm">Month</label><input type="month" class="form-control" id="pm" name="month" value="{{ $payrollMonth }}" required></div>
                    <div class="col-sm-3"><label class="form-label" for="wd">Working days</label><input type="number" class="form-control" id="wd" name="working_days" value="26" min="1" max="31" required></div>
                    <div class="col-sm-4"><button class="btn btn-primary w-100" type="submit"><i class="bi bi-lightning-charge"></i> Generate drafts</button></div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between"><span>Leave requests awaiting approval</span><a class="small text-decoration-none" href="{{ route('admin.leaves.index', ['status' => 'pending']) }}">All</a></div>
            <ul class="list-group list-group-flush">
                @forelse ($pendingLeaves as $l)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><strong>{{ $l->employee?->name }}</strong> <span class="text-muted small">· {{ $l->leaveType?->name }} · {{ $l->from_date->format('d M') }}@if ($l->to_date->ne($l->from_date)) – {{ $l->to_date->format('d M') }}@endif</span></span>
                        <a href="{{ route('admin.leaves.edit', $l) }}" class="btn btn-sm btn-light border">Review</a>
                    </li>
                @empty
                    <li class="list-group-item text-muted">No pending requests.</li>
                @endforelse
            </ul>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between"><span>Upcoming holidays</span><a class="small text-decoration-none" href="{{ route('admin.holidays.index') }}">Calendar</a></div>
            <ul class="list-group list-group-flush">
                @forelse ($holidays as $h)
                    <li class="list-group-item d-flex justify-content-between"><span>{{ $h->title }}</span><span class="text-muted">{{ $h->date->format('D, d M') }}</span></li>
                @empty
                    <li class="list-group-item text-muted">No upcoming holidays. <a href="{{ route('admin.holidays.create') }}">Add one</a>.</li>
                @endforelse
            </ul>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="card h-100">
            <div class="card-header">Birthdays in the next 30 days</div>
            <ul class="list-group list-group-flush">
                @forelse ($birthdays as $e)
                    <li class="list-group-item d-flex justify-content-between"><span>🎂 {{ $e->name }}</span><span class="text-muted">{{ $e->dob->format('d M') }}</span></li>
                @empty
                    <li class="list-group-item text-muted">None coming up.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection
