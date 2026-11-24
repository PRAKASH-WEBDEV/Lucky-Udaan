<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title') · Staff · {{ setting('site_name') }}</title>
    <link rel="icon" href="{{ upload_url(setting('favicon')) ?? asset('favicon.png') }}">
    @vite(['resources/scss/admin.scss', 'resources/js/admin.js'])
    <style>
        .staff-wrap { max-width: 56rem; }
        .staff-tabs .nav-link { color: var(--bs-body-color); border-radius: .6rem; padding: .5rem .9rem; }
        .staff-tabs .nav-link.active { background: var(--bs-primary); color: #fff; }
        @media (max-width: 575.98px) {
            body { padding-bottom: 4.5rem; }
            .staff-tabs { position: fixed; bottom: 0; left: 0; right: 0; z-index: 1030; background: #fff; border-top: 1px solid var(--bs-border-color); padding: .35rem; justify-content: space-around; }
            .staff-tabs .nav-link { display: flex; flex-direction: column; align-items: center; font-size: .75rem; padding: .35rem .6rem; }
            .staff-tabs .nav-link i { font-size: 1.15rem; }
        }
    </style>
</head>
<body class="bg-light">
@php $staff = auth('employee')->user(); @endphp
<header class="bg-white border-bottom">
    <div class="container staff-wrap py-3 d-flex align-items-center justify-content-between gap-3">
        <a href="{{ route('staff.dashboard') }}" class="d-flex align-items-center gap-2 text-decoration-none text-body">
            @if (setting('logo'))<img src="{{ upload_url(setting('logo')) }}" alt="" style="height:2.5rem">@endif
            <div><div class="fw-bold">{{ setting('site_name') }}</div><div class="small text-muted">Staff portal · {{ $staff?->name }}</div></div>
        </a>
        <form method="post" action="{{ route('staff.logout') }}">@csrf
            <button class="btn btn-light border btn-sm"><i class="bi bi-box-arrow-right"></i> <span class="d-none d-sm-inline">Sign out</span></button>
        </form>
    </div>
</header>

<div class="container staff-wrap pt-3">
    <nav class="nav staff-tabs gap-1 mb-3">
        @foreach ([['staff.dashboard', 'calendar-check', 'Attendance'], ['staff.leave', 'calendar-x', 'Leave'], ['staff.notices', 'bell', 'Notices']] as [$r, $i, $l])
            <a class="nav-link @if (request()->routeIs($r)) active @endif" href="{{ route($r) }}"><i class="bi bi-{{ $i }}"></i> {{ $l }}</a>
        @endforeach
    </nav>
</div>

<main class="container staff-wrap pb-5">
    @foreach (['success' => 'check-circle-fill', 'error' => 'exclamation-circle-fill'] as $k => $icon)
        @if (session($k))
            <div class="alert alert-{{ $k === 'error' ? 'danger' : 'success' }} d-flex gap-2" role="status"><i class="bi bi-{{ $icon }}"></i><div>{{ session($k) }}</div></div>
        @endif
    @endforeach
    @yield('content')
</main>
@stack('scripts')
</body>
</html>
