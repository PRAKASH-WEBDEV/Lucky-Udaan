<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Dashboard') · {{ setting('site_name') }} Admin</title>
    <link rel="icon" href="{{ upload_url(setting('favicon')) ?? asset('favicon.png') }}">
    @vite(['resources/scss/admin.scss', 'resources/js/admin.js'])
    <style>:root { --brand: {{ setting('color_primary', '#1f7a4d') }}; --brand-rgb: {{ hex_rgb(setting('color_primary')) }}; }</style>
    @stack('head')
</head>
@php
    $admin = auth('admin')->user();
    $badges = [
        'admin.enquiries.index' => $admin->canAccess('enquiries') ? \App\Models\Enquiry::where('status', 'new')->count() : 0,
        'admin.donations.index' => $admin->canAccess('donations') ? \App\Models\Donation::where('status', 'pending')->count() : 0,
        'admin.volunteers.index' => $admin->canAccess('volunteers') ? \App\Models\Volunteer::where('status', 'new')->count() : 0,
        'admin.members.index' => $admin->canAccess('members') ? \App\Models\Member::where('status', 'pending')->count() : 0,
    ];
    $routeBase = fn ($r) => $r ? \Illuminate\Support\Str::beforeLast($r, '.').'.*' : null;
@endphp
<body>
<div class="admin-shell">
    <aside class="sidebar" aria-label="Dashboard navigation">
        <a href="{{ route('admin.dashboard') }}" class="sb-brand">
            <img src="{{ upload_url(setting('logo')) }}" alt="">
            <span>{{ setting('site_name') }}<small>Admin dashboard</small></span>
        </a>
        <nav class="sb-nav">
            @php $pendingHeading = null; @endphp
            @foreach (config('admin.menu') as $item)
                @if (is_string($item))
                    @php $pendingHeading = $item; @endphp
                    @continue
                @endif
                @php [$label, $icon, $route, $module] = $item; $children = $item[4] ?? null; @endphp
                @continue($module && ! $admin->canAccess($module))
                @if ($pendingHeading)<div class="sb-heading">{{ $pendingHeading }}</div>@php $pendingHeading = null; @endphp@endif
                @if ($children)
                    @php $open = collect($children)->contains(fn ($c) => request()->routeIs($routeBase($c[1]))); $cid = 'sb-'.\Illuminate\Support\Str::slug($label); @endphp
                    <button class="sb-link @if ($open) active @endif" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $cid }}" aria-expanded="{{ $open ? 'true' : 'false' }}" title="{{ $label }}">
                        <i class="bi bi-{{ $icon }}"></i><span class="label">{{ $label }}</span><i class="bi bi-chevron-down chev"></i>
                    </button>
                    <ul class="sb-sub collapse @if ($open) show @endif" id="{{ $cid }}">
                        @foreach ($children as [$cl, $cr])
                            <li><a href="{{ route($cr) }}" @class(['active' => request()->routeIs($routeBase($cr))])>{{ $cl }}</a></li>
                        @endforeach
                    </ul>
                @else
                    <a href="{{ route($route) }}" @class(['sb-link', 'active' => $route === 'admin.dashboard' ? request()->routeIs('admin.dashboard') : request()->routeIs($routeBase($route))]) title="{{ $label }}">
                        <i class="bi bi-{{ $icon }}"></i><span class="label">{{ $label }}</span>
                        @if (! empty($badges[$route]))<span class="badge rounded-pill text-bg-warning">{{ $badges[$route] }}</span>@endif
                    </a>
                @endif
            @endforeach
        </nav>
        <div class="sb-foot">Signed in as {{ $admin->role?->name }}</div>
    </aside>
    <div class="sb-backdrop"></div>

    <div class="main">
        <header class="topbar">
            <button class="btn btn-light btn-icon border" type="button" data-sb-toggle aria-label="Toggle navigation"><i class="bi bi-layout-sidebar"></i></button>
            <p class="page-title flex-grow-1">@yield('title', 'Dashboard')</p>
            <a href="{{ route('home') }}" target="_blank" rel="noopener" class="btn btn-light border btn-sm d-none d-sm-inline-flex"><i class="bi bi-box-arrow-up-right"></i> View website</a>
            <div class="dropdown">
                <button class="user-chip" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="avatar">@if ($admin->avatar)<img src="{{ upload_url($admin->avatar) }}" alt="">@else{{ $admin->initials() }}@endif</span>
                    <span class="meta d-none d-md-block"><span class="fw-semibold d-block">{{ $admin->name }}</span><small>{{ $admin->role?->name }}</small></span>
                    <i class="bi bi-chevron-down small text-muted"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                    <li><a class="dropdown-item" href="{{ route('admin.profile') }}"><i class="bi bi-person me-2"></i>My profile</a></li>
                    @if ($admin->canAccess('settings'))<li><a class="dropdown-item" href="{{ route('admin.settings.edit') }}"><i class="bi bi-gear me-2"></i>Website settings</a></li>@endif
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="post" action="{{ route('admin.logout') }}">@csrf
                            <button class="dropdown-item text-danger" type="submit"><i class="bi bi-box-arrow-right me-2"></i>Sign out</button>
                        </form>
                    </li>
                </ul>
            </div>
        </header>

        <main class="content" id="main">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show d-flex gap-2" role="status" data-autohide>
                    <i class="bi bi-check-circle-fill"></i><div>{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show d-flex gap-2" role="alert">
                    <i class="bi bi-exclamation-triangle-fill"></i><div>{{ session('error') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @yield('content')
        </main>
    </div>
</div>

<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-body text-center p-4">
                <div class="tone-danger rounded-circle d-inline-grid mb-3" style="width:3.5rem;height:3.5rem;place-items:center;font-size:1.5rem"><i class="bi bi-exclamation-triangle"></i></div>
                <h2 class="h5" id="confirmTitle">Are you sure?</h2>
                <p class="confirm-text text-muted mb-4"></p>
                <div class="d-flex gap-2 justify-content-center">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger confirm-yes">Yes, continue</button>
                </div>
            </div>
        </div>
    </div>
</div>
@stack('scripts')
</body>
</html>
