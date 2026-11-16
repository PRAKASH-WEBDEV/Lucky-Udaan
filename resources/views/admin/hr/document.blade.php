{{-- Printable A4 document on the organisation letterhead (Settings → Letterhead). --}}
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $docTitle ?? 'Document' }} · {{ setting('site_name') }}</title>
    <style>
        :root { --brand: {{ setting('letterhead_color', setting('color_primary', '#1f7a4d')) }}; }
        * { box-sizing: border-box; }
        body { margin: 0; background: #eef1f4; font: 14px/1.55 "Segoe UI", Arial, sans-serif; color: #1d2329; }
        .toolbar { max-width: 210mm; margin: 16px auto; display: flex; gap: 8px; justify-content: flex-end; }
        .toolbar a, .toolbar button { font: inherit; padding: 8px 14px; border-radius: 6px; border: 1px solid #c9d0d6; background: #fff; cursor: pointer; text-decoration: none; color: inherit; }
        .toolbar button { background: var(--brand); color: #fff; border-color: var(--brand); }
        .page { width: 210mm; min-height: 297mm; margin: 0 auto 24px; background: #fff; padding: 14mm 16mm; display: flex; flex-direction: column; box-shadow: 0 2px 12px rgba(0,0,0,.08); }
        .lh-head { display: flex; align-items: center; gap: 16px; border-bottom: 3px solid var(--brand); padding-bottom: 10px; }
        .lh-head img { height: 64px; width: auto; }
        .lh-head h1 { margin: 0; font-size: 22px; color: var(--brand); }
        .lh-head .sub { font-size: 12px; color: #55606a; }
        .lh-body { flex: 1; padding: 18px 0; }
        .lh-foot { border-top: 1px solid #d5dbe0; padding-top: 8px; font-size: 11px; color: #55606a; text-align: center; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #d5dbe0; padding: 6px 8px; text-align: left; vertical-align: top; }
        th { background: #f4f6f8; font-weight: 600; }
        td.num, th.num { text-align: right; }
        .sign { margin-top: 48px; display: flex; justify-content: space-between; align-items: flex-end; }
        .sign img { max-height: 56px; display: block; }
        @media print { body { background: #fff; } .toolbar { display: none; } .page { margin: 0; box-shadow: none; width: auto; min-height: 0; padding: 0; } @page { size: A4; margin: 14mm 16mm; } }
    </style>
</head>
<body>
<div class="toolbar">
    <a href="{{ url()->previous() }}">← Back</a>
    <button type="button" onclick="window.print()">Print / Save as PDF</button>
</div>
<div class="page">
    <header class="lh-head">
        @if ($logo = setting('letterhead_logo') ?: setting('logo'))<img src="{{ upload_url($logo) }}" alt="">@endif
        <div>
            <h1>{{ setting('site_name') }}</h1>
            <div class="sub">{{ setting('letterhead_tagline', setting('legal_name')) }}</div>
            <div class="sub">{{ setting('address') }}, {{ setting('city') }}, {{ setting('state') }} {{ setting('pincode') }} · {{ setting('phone') }} · {{ setting('email') }}</div>
        </div>
    </header>
    <main class="lh-body">@yield('body')</main>
    <footer class="lh-foot">
        {{ setting('letterhead_footer') ?: collect([setting('cin') ? 'CIN: '.setting('cin') : null, setting('pan') ? 'PAN: '.setting('pan') : null, setting('reg_12a') ? '12A: '.setting('reg_12a') : null, setting('reg_80g') ? '80G: '.setting('reg_80g') : null])->filter()->implode(' · ') }}
    </footer>
</div>
</body>
</html>
