@extends('layouts.admin')
@section('title', 'Receipt '.$donation->receipt_no)

@section('content')
<div class="page-head no-print">
    <div>
        <a href="{{ route('admin.donations.edit', $donation->id) }}" class="small text-decoration-none"><i class="bi bi-arrow-left"></i> Back to donation</a>
        <h1 class="mt-1">Donation receipt</h1>
    </div>
    <button class="btn btn-primary" onclick="window.print()"><i class="bi bi-printer"></i> Print / save as PDF</button>
</div>

<div class="receipt">
    <div class="d-flex flex-wrap justify-content-between gap-3 pb-4 mb-4 border-bottom">
        <div class="d-flex gap-3 align-items-center">
            @if (setting('logo'))<img src="{{ upload_url(setting('logo')) }}" alt="" style="height:4.5rem">@endif
            <div>
                <div class="h4 mb-0">{{ setting('site_name') }}</div>
                <div class="small text-muted">{{ setting('legal_name') }}</div>
                <div class="small text-muted">{{ full_address() }}</div>
                <div class="small text-muted">{{ setting('email') }} · +91 {{ setting('phone') }}</div>
            </div>
        </div>
        <div class="text-md-end">
            <div class="text-uppercase small fw-bold text-muted">Receipt</div>
            <div class="h5 mb-0">{{ $donation->receipt_no }}</div>
            <div class="small">Date: {{ $donation->donated_at?->format('d M Y') }}</div>
        </div>
    </div>

    <p>Received with thanks from <strong>{{ $donation->donor?->name }}</strong>@if ($donation->donor?->pan) (PAN: {{ $donation->donor->pan }})@endif
        @if ($donation->donor?->address), {{ $donation->donor->address }}@endif
        the sum of <strong>{{ inr($donation->amount, true) }}</strong>
        by {{ $donation->methodLabel() }}@if ($donation->transaction_ref) (Ref: {{ $donation->transaction_ref }})@endif
        as a voluntary donation{{ $donation->campaign ? ' towards “'.$donation->campaign->title.'”' : '' }}.</p>

    <table class="table table-bordered my-4">
        <tbody>
            <tr><th class="w-50">Amount</th><td class="fw-bold">{{ inr($donation->amount, true) }}</td></tr>
            <tr><th>Payment mode</th><td>{{ $donation->methodLabel() }}</td></tr>
            <tr><th>Purpose</th><td>{{ $donation->campaign?->title ?? 'General fund — charitable activities' }}</td></tr>
            @if (setting('pan'))<tr><th>Organisation PAN</th><td>{{ setting('pan') }}</td></tr>@endif
            @if (setting('reg_80g'))<tr><th>80G registration no.</th><td>{{ setting('reg_80g') }}</td></tr>@endif
            @if (setting('reg_12a'))<tr><th>12A registration no.</th><td>{{ setting('reg_12a') }}</td></tr>@endif
        </tbody>
    </table>

    @if (setting('reg_80g') && $donation->wants_80g)
        <p class="small">Donations to {{ setting('site_name') }} are eligible for deduction under Section 80G of the Income Tax Act, 1961, subject to the conditions specified therein. Cash donations above ₹2,000 are not eligible.</p>
    @elseif (! setting('reg_80g'))
        <p class="small text-muted">This receipt acknowledges your donation. (80G details will appear here once added in Website Settings.)</p>
    @endif

    <div class="d-flex justify-content-between align-items-end mt-5 pt-4">
        <p class="small text-muted mb-0">Thank you for supporting our work. This is a computer-generated receipt.</p>
        <div class="text-center">
            <div style="height:3rem"></div>
            <div class="border-top pt-1 fw-semibold">{{ setting('signatory_name') }}</div>
            <div class="small text-muted">{{ setting('signatory_title', 'Authorised Signatory') }}</div>
        </div>
    </div>
</div>
@endsection
