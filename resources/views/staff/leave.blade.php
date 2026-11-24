@extends('staff.layout')
@section('title', 'Leave')

@php
    $num = fn ($n) => rtrim(rtrim(number_format((float) $n, 1), '0'), '.');
    $tone = fn ($s) => match ($s) { 'approved' => 'success', 'rejected' => 'danger', default => 'warning' };
@endphp

@section('content')
<div class="row g-3">
    <div class="col-lg-5">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h1 class="h5 mb-3">Apply for leave</h1>
                @if ($types->isEmpty())
                    <div class="alert alert-info mb-0">Leave types have not been set up yet. Please contact the office.</div>
                @else
                <form method="post" action="{{ route('staff.leave.apply') }}" novalidate>
                    @csrf
                    <div class="mb-3">
                        <label class="form-label" for="leave_type_id">Leave type</label>
                        <select name="leave_type_id" id="leave_type_id" class="form-select @error('leave_type_id') is-invalid @enderror" required>
                            <option value="">Select type</option>
                            @foreach ($types as $t)
                                <option value="{{ $t->id }}" @selected(old('leave_type_id') == $t->id)>{{ $t->name }}{{ $t->annual_quota ? ' ('.$num($t->annual_quota - ($used[$t->id] ?? 0)).' left)' : '' }}</option>
                            @endforeach
                        </select>
                        @error('leave_type_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="row g-2">
                        <div class="col-6 mb-3">
                            <label class="form-label" for="from_date">From</label>
                            <input type="date" name="from_date" id="from_date" value="{{ old('from_date', today()->toDateString()) }}" class="form-control @error('from_date') is-invalid @enderror" required>
                            @error('from_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label" for="to_date">To</label>
                            <input type="date" name="to_date" id="to_date" value="{{ old('to_date', today()->toDateString()) }}" class="form-control @error('to_date') is-invalid @enderror" required>
                            @error('to_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="half_day" value="1" id="half_day" @checked(old('half_day'))>
                        <label class="form-check-label" for="half_day">Half day (only when From and To are the same date)</label>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="reason">Reason</label>
                        <textarea name="reason" id="reason" rows="3" maxlength="1000" class="form-control @error('reason') is-invalid @enderror" required>{{ old('reason') }}</textarea>
                        @error('reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <button class="btn btn-primary w-100"><i class="bi bi-send"></i> Send leave request</button>
                </form>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        @if ($types->isNotEmpty())
            <div class="card shadow-sm border-0 mb-3"><div class="card-body p-3">
                <div class="small text-muted mb-2">Leave balance {{ now()->year }}</div>
                <div class="d-flex flex-wrap gap-2">
                    @foreach ($types as $t)
                        <span class="badge rounded-pill text-bg-light border fw-normal py-2 px-3">{{ $t->name }}: <strong>{{ $num($used[$t->id] ?? 0) }}</strong>{{ $t->annual_quota ? ' / '.$t->annual_quota : '' }} used</span>
                    @endforeach
                </div>
            </div></div>
        @endif

        <h2 class="h5">My leave requests</h2>
        <div class="card shadow-sm border-0"><div class="list-group list-group-flush">
            @forelse ($requests as $r)
                <div class="list-group-item py-3">
                    <div class="d-flex justify-content-between gap-2">
                        <div>
                            <div class="fw-semibold">{{ $r->from_date->format('d M Y') }}@if ($r->to_date->ne($r->from_date)) – {{ $r->to_date->format('d M Y') }}@endif
                                <span class="text-muted fw-normal">· {{ $num($r->days) }} day{{ $r->days == 1 ? '' : 's' }} · {{ $r->leaveType?->name }}</span></div>
                            @if ($r->reason)<div class="small text-muted">{{ $r->reason }}</div>@endif
                            @if ($r->admin_note)<div class="small mt-1"><i class="bi bi-chat-left-text"></i> Office: {{ $r->admin_note }}</div>@endif
                        </div>
                        <div class="text-end">
                            <span class="badge text-bg-{{ $tone($r->status) }}">{{ \App\Models\LeaveRequest::STATUSES[$r->status] ?? $r->status }}</span>
                            @if ($r->status === 'pending')
                                <form method="post" action="{{ route('staff.leave.cancel', $r->id) }}" class="mt-2" onsubmit="return confirm('Cancel this leave request?')">@csrf @method('DELETE')
                                    <button class="btn btn-link btn-sm text-danger p-0">Cancel</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="list-group-item text-center text-muted py-4">No leave requests yet.</div>
            @endforelse
        </div></div>
    </div>
</div>
@endsection
