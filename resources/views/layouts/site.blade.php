<!DOCTYPE html>
<html lang="en-IN" class="no-js">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    @include('site.partials.seo')
    <meta name="theme-color" content="{{ setting('color_primary_dark', '#0f3d2e') }}">
    <link rel="icon" href="{{ upload_url(setting('favicon')) ?? asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ upload_url(setting('favicon')) ?? asset('favicon.png') }}">
    @vite(['resources/scss/site.scss', 'resources/js/site.js'])
    <style>
        :root {
            --brand: {{ setting('color_primary', '#1f7a4d') }}; --brand-rgb: {{ hex_rgb(setting('color_primary')) }};
            --brand-dark: {{ setting('color_primary_dark', '#0f3d2e') }}; --brand-dark-rgb: {{ hex_rgb(setting('color_primary_dark'), '15, 61, 46') }};
            --accent: {{ setting('color_accent', '#e2a93b') }}; --accent-rgb: {{ hex_rgb(setting('color_accent'), '226, 169, 59') }};
        }
    </style>
    @if ($ga = setting('ga_id'))
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $ga }}"></script>
        <script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments)}gtag('js',new Date());gtag('config','{{ $ga }}');</script>
    @endif
    @stack('head')
</head>
<body>
<a class="skip-link" href="#main">Skip to main content</a>

@include('site.partials.header')

<main id="main" tabindex="-1">
    @if (session('success') && ! View::hasSection('handlesFlash'))
        <div class="container pt-4">
            <div class="alert alert-success alert-dismissible fade show d-flex gap-2 align-items-start" role="status">
                <i class="bi bi-check-circle-fill fs-5"></i><div>{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif
    @yield('content')
</main>

@include('site.partials.footer')

<div class="fab-stack no-print">
    <button type="button" class="fab fab-top" aria-label="Back to top"><i class="bi bi-arrow-up"></i></button>
    @if (setting('whatsapp'))
        <a class="fab fab-wa" href="{{ whatsapp_link(setting('whatsapp'), 'Hello '.setting('site_name').', I would like to know more.') }}" target="_blank" rel="noopener" aria-label="Chat with us on WhatsApp"><i class="bi bi-whatsapp"></i></a>
    @endif
</div>

@unless (request()->routeIs('donate*'))
    <div class="mobile-donate no-print">
        <a href="tel:{{ phone_link(setting('phone')) }}" class="btn btn-outline-primary"><i class="bi bi-telephone"></i> Call</a>
        <a href="{{ route('donate') }}" class="btn btn-accent"><i class="bi bi-heart-fill"></i> Donate</a>
    </div>
@endunless

@stack('scripts')
</body>
</html>
