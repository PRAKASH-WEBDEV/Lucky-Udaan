@extends('admin.hr.document')

@section('body')
<p style="display:flex;justify-content:space-between"><span>Ref: HR/{{ $o->issued_on?->format('Y') }}/{{ str_pad($o->id, 3, '0', STR_PAD_LEFT) }}</span><span>Date: {{ ($o->issued_on ?? today())->format('d F Y') }}</span></p>
<p>To,<br><strong>{{ $o->candidate_name }}</strong>@if ($o->email)<br>{{ $o->email }}@endif @if ($o->phone)<br>{{ $o->phone }}@endif</p>
<p><strong>Subject: Offer of appointment — {{ $o->designation }}</strong></p>
@if (filled(strip_tags((string) $o->body)))
    {!! $o->body !!}
@else
    <p>Dear {{ \Illuminate\Support\Str::before($o->candidate_name, ' ') }},</p>
    <p>We are pleased to offer you the position of <strong>{{ $o->designation }}</strong>@if ($o->department) in the {{ $o->department }} team @endif at {{ setting('site_name') }}@if ($o->location), based at {{ $o->location }}@endif.</p>
    @if ($o->joining_date)<p>Your expected date of joining is <strong>{{ $o->joining_date->format('d F Y') }}</strong>.</p>@endif
    @if ($o->ctc_annual)<p>Your total annual compensation (CTC) will be <strong>{{ inr($o->ctc_annual) }}</strong>, payable monthly as per the organisation’s salary structure and applicable statutory deductions.</p>@endif
    <p>This offer is subject to verification of your documents and the organisation’s service rules. Please confirm your acceptance by signing and returning a copy of this letter.</p>
    <p>We look forward to having you with us in our mission — together for a better tomorrow.</p>
@endif
<div class="sign">
    <span>Accepted by: ____________________<br><span style="font-size:12px">{{ $o->candidate_name }}</span></span>
    <span style="text-align:center">
        @if (setting('letterhead_signature'))<img src="{{ upload_url(setting('letterhead_signature')) }}" alt="">@endif
        <strong>{{ setting('signatory_name') }}</strong><br><span style="font-size:12px">{{ setting('signatory_title') }}, {{ setting('site_name') }}</span>
    </span>
</div>
@endsection
