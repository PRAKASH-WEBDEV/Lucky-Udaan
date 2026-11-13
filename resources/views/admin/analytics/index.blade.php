@extends('layouts.admin')
@section('title', 'Reports & analytics')

@section('content')
@php $query = ['report' => $report, 'from' => $from->toDateString(), 'to' => $to->toDateString(), 'status' => $status]; @endphp
<div class="page-head">
    <div>
        <h1><i class="bi bi-bar-chart-line text-primary"></i> Reports &amp; analytics</h1>
        <p class="sub">{{ $reportTitle }} from {{ $from->format('d M Y') }} to {{ $to->format('d M Y') }}</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('admin.analytics.export', ['format' => 'csv'] + $query) }}" class="btn btn-light border"><i class="bi bi-filetype-csv"></i> Export CSV</a>
        <a href="{{ route('admin.analytics.export', ['format' => 'pdf'] + $query) }}" class="btn btn-light border"><i class="bi bi-filetype-pdf"></i> Export PDF</a>
        <button type="button" class="btn btn-light border" onclick="window.print()"><i class="bi bi-printer"></i> Print</button>
    </div>
</div>

<div class="card mb-4 no-print">
    <form class="toolbar" method="get">
        <div>
            <label class="form-label small mb-1" for="report">Report</label>
            <select class="form-select" id="report" name="report" onchange="this.form.status.value='';this.form.submit()">
                @foreach ($reports as $k => $v)<option value="{{ $k }}" @selected($report === $k)>{{ $v }}</option>@endforeach
            </select>
        </div>
        <div>
            <label class="form-label small mb-1" for="from">From</label>
            <input type="date" class="form-control" id="from" name="from" value="{{ $from->toDateString() }}">
        </div>
        <div>
            <label class="form-label small mb-1" for="to">To</label>
            <input type="date" class="form-control" id="to" name="to" value="{{ $to->toDateString() }}">
        </div>
        <div>
            <label class="form-label small mb-1" for="status">Status</label>
            <select class="form-select" id="status" name="status">
                <option value="">All</option>
                @foreach ($statuses as $k => $v)<option value="{{ $k }}" @selected($status === $k)>{{ $v }}</option>@endforeach
            </select>
        </div>
        <div class="align-self-end d-flex flex-wrap gap-2">
            <button class="btn btn-primary" type="submit">Apply</button>
            @php $fy = now()->month >= 4 ? now()->year : now()->year - 1; @endphp
            <a class="btn btn-light border" href="{{ route('admin.analytics.index', ['report' => $report, 'from' => now()->subDays(29)->toDateString(), 'to' => now()->toDateString()]) }}">Last 30 days</a>
            <a class="btn btn-light border" href="{{ route('admin.analytics.index', ['report' => $report, 'from' => $fy.'-04-01', 'to' => now()->toDateString()]) }}">This FY</a>
            <a class="btn btn-light border" href="{{ route('admin.analytics.index', ['report' => $report, 'from' => ($fy - 1).'-04-01', 'to' => $fy.'-03-31']) }}">Last FY</a>
        </div>
    </form>
</div>

<div class="stat-grid mb-4">
    @foreach ($summary as [$label, $value])
        <div class="stat-card"><span class="min-w-0"><span class="label d-block">{{ $label }}</span><span class="value d-block text-truncate">{{ $value }}</span></span></div>
    @endforeach
</div>

<div class="card mb-4">
    <div class="card-header">{{ $series['label'] }}</div>
    <div class="card-body"><div style="height: 300px"><canvas data-chart="{{ json_encode(['type' => 'bar', 'labels' => $series['labels'], 'values' => $series['values'], 'money' => $report === 'donations']) }}" role="img" aria-label="{{ $series['label'] }} chart"></canvas></div></div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between"><span>Records</span><span class="text-muted small fw-normal">{{ count($rows) }} rows</span></div>
    @if (empty($rows))
        <p class="text-center text-muted py-5 mb-0">No records in this period.</p>
    @else
        <div class="table-responsive" style="max-height: 36rem">
            <table class="table table-sm table-hover table-stack align-middle">
                <thead class="sticky-top"><tr>@foreach ($headings as $h)<th>{{ $h }}</th>@endforeach</tr></thead>
                <tbody>
                    @foreach (array_slice($rows, 0, 500) as $row)
                        <tr>@foreach ($row as $i => $cell)<td data-label="{{ $headings[$i] }}">{{ $cell }}</td>@endforeach</tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if (count($rows) > 500)<p class="small text-muted p-3 mb-0">Showing the first 500 rows. Export CSV for the full list.</p>@endif
    @endif
</div>
@endsection
