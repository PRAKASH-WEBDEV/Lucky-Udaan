<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>{{ $reportTitle }} report</title>
<style>
    @page { margin: 28px 32px; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #222; }
    .head { border-bottom: 2px solid #1f7a4d; padding-bottom: 8px; margin-bottom: 14px; }
    .head h1 { font-size: 16px; margin: 0 0 2px; color: #0f3d2e; }
    .muted { color: #666; }
    .summary { width: 100%; margin-bottom: 14px; border-collapse: separate; border-spacing: 6px 0; }
    .summary td { background: #f2f6f3; padding: 8px 10px; border-radius: 4px; }
    .summary b { display: block; font-size: 13px; color: #0f3d2e; }
    table.data { width: 100%; border-collapse: collapse; }
    table.data th { background: #0f3d2e; color: #fff; text-align: left; padding: 5px 6px; font-size: 9px; }
    table.data td { padding: 4px 6px; border-bottom: 1px solid #e3e8e4; }
    table.data tr:nth-child(even) td { background: #fafbf9; }
    .foot { position: fixed; bottom: -12px; left: 0; right: 0; font-size: 8px; color: #888; text-align: center; }
</style>
</head>
<body>
<div class="head">
    <h1>{{ setting('site_name') }} — {{ $reportTitle }} report</h1>
    <div class="muted">{{ $from->format('d M Y') }} to {{ $to->format('d M Y') }}@if ($status) · Status: {{ ucfirst($status) }}@endif · Generated {{ now()->format('d M Y, h:i A') }}</div>
</div>
<table class="summary"><tr>
    @foreach ($summary as [$label, $value])<td><span class="muted">{{ $label }}</span><b>{{ str_replace('₹', 'Rs. ', $value) }}</b></td>@endforeach
</tr></table>
<table class="data">
    <thead><tr>@foreach ($headings as $h)<th>{{ $h }}</th>@endforeach</tr></thead>
    <tbody>
        @forelse ($rows as $row)
            <tr>@foreach ($row as $cell)<td>{{ $cell }}</td>@endforeach</tr>
        @empty
            <tr><td colspan="{{ count($headings) }}" style="text-align:center;padding:20px">No records in this period.</td></tr>
        @endforelse
    </tbody>
</table>
<div class="foot">{{ setting('site_name') }} · {{ full_address() }} · {{ setting('email') }}</div>
</body>
</html>
