<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title') · {{ setting('site_name') }}</title>
    <link rel="icon" href="{{ upload_url(setting('favicon')) ?? asset('favicon.png') }}">
    @vite(['resources/scss/admin.scss', 'resources/js/admin.js'])
</head>
<body>
<div class="auth-wrap">
    <section class="auth-art" aria-hidden="true">
        <div class="d-flex align-items-center gap-3">
            @if (setting('logo'))<img class="logo" src="{{ upload_url(setting('logo')) }}" alt="">@endif
            <div><div class="fw-bold fs-5">{{ setting('site_name') }}</div><div class="text-white-50 small">{{ setting('tagline') }}</div></div>
        </div>
        <div>
            <h1>Every update here reaches a life out there.</h1>
            <p>Manage programs, donations, volunteers and stories for {{ setting('site_name') }} from one place.</p>
        </div>
        <p class="small mb-0">&copy; {{ date('Y') }} {{ setting('site_name') }} · {{ setting('city') }}, {{ setting('state') }}</p>
    </section>
    <main class="auth-panel">
        <div class="auth-card">
            <div class="d-lg-none text-center mb-4">
                @if (setting('logo'))<img src="{{ upload_url(setting('logo')) }}" alt="{{ setting('site_name') }}" style="height:4.5rem">@endif
            </div>
            @if (session('success'))
                <div class="alert alert-success d-flex gap-2" role="status"><i class="bi bi-check-circle-fill"></i><div>{{ session('success') }}</div></div>
            @endif
            @yield('content')
            <p class="text-center small text-muted mt-5 mb-0"><a href="{{ route('home') }}" class="text-decoration-none"><i class="bi bi-arrow-left"></i> Back to website</a></p>
        </div>
    </main>
</div>
</body>
</html>
