@extends('staff.layout')
@section('title', 'Notices')

@section('content')
<div class="row g-3">
    <div class="col-lg-8">
        <h1 class="h5">Notices</h1>
        <div class="card shadow-sm border-0"><div class="list-group list-group-flush">
            @forelse ($notices as $n)
                <div class="list-group-item d-flex gap-3 py-3">
                    <i class="bi bi-{{ $n->icon }} text-{{ $n->tone }} fs-5"></i>
                    <div>
                        <div class="fw-semibold">{{ $n->title }}</div>
                        @if ($n->message)<div class="small" style="white-space:pre-line">{{ $n->message }}</div>@endif
                        <div class="small text-muted mt-1">{{ $n->at?->format('d M Y, h:i A') }}</div>
                    </div>
                </div>
            @empty
                <div class="list-group-item text-center text-muted py-4">No notices right now.</div>
            @endforelse
        </div></div>
    </div>
    <div class="col-lg-4">
        <h2 class="h5">Upcoming holidays</h2>
        <div class="card shadow-sm border-0"><div class="list-group list-group-flush">
            @forelse ($holidays as $h)
                <div class="list-group-item d-flex justify-content-between"><span>{{ $h->title }}</span><span class="text-muted small">{{ $h->date->format('D, d M') }}</span></div>
            @empty
                <div class="list-group-item text-center text-muted py-3">None scheduled.</div>
            @endforelse
        </div></div>
    </div>
</div>
@endsection
