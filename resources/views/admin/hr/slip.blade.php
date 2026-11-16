@extends('admin.hr.document')

@section('body')
@php $e = $p->employee; $m = fn ($v) => number_format((float) $v, 2); @endphp
<h2 style="text-align:center;margin:0 0 14px;font-size:17px;letter-spacing:.04em">SALARY SLIP — {{ strtoupper($p->monthLabel()) }}</h2>
<table style="margin-bottom:14px">
    <tr><th style="width:22%">Employee</th><td>{{ $e?->name }}</td><th style="width:22%">Employee code</th><td>{{ $e?->employee_code ?: '—' }}</td></tr>
    <tr><th>Designation</th><td>{{ $e?->designation ?: '—' }}</td><th>Department</th><td>{{ $e?->department ?: '—' }}</td></tr>
    <tr><th>Bank account</th><td>{{ $e?->bank_account ? $e->bank_name.' · '.$e->bank_account : '—' }}</td><th>PAN</th><td>{{ $e?->pan ?: '—' }}</td></tr>
    <tr><th>Working days</th><td>{{ $p->working_days }}</td><th>Paid days</th><td>{{ rtrim(rtrim(number_format($p->present_days, 1), '0'), '.') }}</td></tr>
</table>
<table>
    <tr><th>Earnings</th><th class="num">Amount (₹)</th><th>Deductions</th><th class="num">Amount (₹)</th></tr>
    <tr><td>Basic</td><td class="num">{{ $m($p->basic) }}</td><td>Provident fund</td><td class="num">{{ $m($p->pf) }}</td></tr>
    <tr><td>HRA</td><td class="num">{{ $m($p->hra) }}</td><td>ESI</td><td class="num">{{ $m($p->esi) }}</td></tr>
    <tr><td>Allowances</td><td class="num">{{ $m($p->allowances) }}</td><td>Professional tax / TDS</td><td class="num">{{ $m($p->tax) }}</td></tr>
    <tr><td></td><td></td><td>Other deductions</td><td class="num">{{ $m($p->other_deductions) }}</td></tr>
    <tr><th>Gross earnings</th><th class="num">{{ $m($p->gross) }}</th><th>Total deductions</th><th class="num">{{ $m($p->total_deductions) }}</th></tr>
</table>
<p style="font-size:16px;margin:16px 0 4px"><strong>Net pay: ₹ {{ $m($p->net) }}</strong></p>
@if ($p->status === 'paid')<p style="margin:0;color:#55606a">Paid on {{ $p->paid_on?->format('d M Y') }}@if ($p->payment_ref) · Ref {{ $p->payment_ref }}@endif</p>@endif
<div class="sign">
    <span style="color:#55606a;font-size:12px">This is a computer-generated salary slip.</span>
    <span style="text-align:center">
        @if (setting('letterhead_signature'))<img src="{{ upload_url(setting('letterhead_signature')) }}" alt="">@endif
        <strong>{{ setting('signatory_name') }}</strong><br><span style="font-size:12px">{{ setting('signatory_title') }}</span>
    </span>
</div>
@endsection
