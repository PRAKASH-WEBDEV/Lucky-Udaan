@php
    $menus = [
        'About' => [
            ['About Us', 'about', 'info-circle'],
            ['Our Team', 'team', 'people'],
            ['Success Stories', 'stories.index', 'stars'],
            ['Testimonials', 'testimonials', 'chat-quote'],
            ['Transparency', 'transparency', 'shield-check'],
            ['Certifications', 'certifications', 'patch-check'],
            ['Annual Reports', 'reports', 'file-earmark-bar-graph'],
            ['FAQ', 'faq', 'question-circle'],
        ],
        'Get Involved' => [
            ['Donate Now', 'donate', 'heart'],
            ['Campaigns', 'campaigns.index', 'megaphone'],
            ['Volunteer', 'volunteer', 'hand-thumbs-up'],
            ['Membership', 'membership', 'person-badge'],
            ['Partnership', 'partnership', 'building'],
            ['Careers', 'careers', 'briefcase'],
            ['Internships', 'internships', 'mortarboard'],
        ],
        'Media' => [
            ['Blog & News', 'blog.index', 'newspaper'],
            ['Events', 'events.index', 'calendar-event'],
            ['Announcements', 'announcements.index', 'bell'],
            ['Press Releases', 'press.index', 'file-earmark-text'],
            ['Gallery', 'gallery', 'images'],
            ['Media Coverage', 'media', 'camera-video'],
        ],
    ];
    $isActive = fn (array $items) => collect($items)->contains(fn ($i) => request()->routeIs($i[1]) || request()->routeIs(str_replace('.index', '.*', $i[1])));
    $logo = upload_url(setting('logo'));
@endphp

<div class="topbar d-none d-md-block">
    <div class="container">
        <div class="topbar-links">
            <a href="tel:{{ phone_link(setting('phone')) }}"><i class="bi bi-telephone me-1"></i>+91 {{ setting('phone') }}</a>
            <a href="mailto:{{ setting('email') }}"><i class="bi bi-envelope me-1"></i>{{ setting('email') }}</a>
            <span class="d-none d-lg-inline"><i class="bi bi-geo-alt me-1"></i>{{ setting('city') }}, {{ setting('state') }}</span>
        </div>
        @include('site.partials.social', ['class' => 'social'])
    </div>
</div>

<header class="site-header">
    <div class="container">
        <a class="brand" href="{{ route('home') }}" aria-label="{{ setting('site_name') }} — home">
            @if ($logo)
                <img src="{{ $logo }}" alt="{{ setting('site_name') }} logo" width="120" height="96">
            @endif
            <span class="brand-text">
                <span class="brand-name">{{ setting('site_name') }}</span>
                <span class="brand-tag d-none d-sm-block">{{ setting('tagline') }}</span>
            </span>
        </a>

        <nav aria-label="Main">
            <ul class="main-nav">
                <li><a href="{{ route('home') }}" @class(['active' => request()->routeIs('home')])>Home</a></li>
                <li class="dropdown">
                    <button type="button" data-bs-toggle="dropdown" aria-expanded="false" @class(['active' => $isActive($menus['About'])])>About <i class="bi bi-chevron-down"></i></button>
                    <ul class="dropdown-menu">
                        @foreach ($menus['About'] as [$label, $route, $icon])
                            <li><a class="dropdown-item @if (request()->routeIs($route)) active @endif" href="{{ route($route) }}"><i class="bi bi-{{ $icon }}"></i>{{ $label }}</a></li>
                        @endforeach
                    </ul>
                </li>
                <li class="dropdown">
                    <button type="button" data-bs-toggle="dropdown" aria-expanded="false" @class(['active' => request()->routeIs('programs.*')])>Programs <i class="bi bi-chevron-down"></i></button>
                    <div class="dropdown-menu mega">
                        <div class="mega-grid">
                            @foreach ($navPrograms as $p)
                                <a class="dropdown-item" href="{{ route('programs.show', $p->slug) }}"><i class="bi bi-{{ $p->icon ?: 'heart' }}"></i>{{ $p->title }}</a>
                            @endforeach
                        </div>
                        <div class="mega-foot">
                            <span class="text-muted">{{ $navPrograms->count() }} focus areas serving communities in {{ setting('city') }}</span>
                            <a class="link-arrow" href="{{ route('programs.index') }}">View all programs <i class="bi bi-arrow-right"></i></a>
                        </div>
                    </div>
                </li>
                @foreach (['Get Involved', 'Media'] as $menu)
                    <li class="dropdown">
                        <button type="button" data-bs-toggle="dropdown" aria-expanded="false" @class(['active' => $isActive($menus[$menu])])>{{ $menu }} <i class="bi bi-chevron-down"></i></button>
                        <ul class="dropdown-menu">
                            @foreach ($menus[$menu] as [$label, $route, $icon])
                                <li><a class="dropdown-item @if (request()->routeIs($route)) active @endif" href="{{ route($route) }}"><i class="bi bi-{{ $icon }}"></i>{{ $label }}</a></li>
                            @endforeach
                        </ul>
                    </li>
                @endforeach
                <li><a href="{{ route('contact') }}" @class(['active' => request()->routeIs('contact')])>Contact</a></li>
            </ul>
        </nav>

        <a href="{{ route('donate') }}" class="btn btn-accent header-donate"><i class="bi bi-heart-fill"></i> Donate</a>
        <button class="nav-toggle" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileNav" aria-controls="mobileNav" aria-label="Open menu">
            <i class="bi bi-list"></i>
        </button>
    </div>
</header>

<div class="offcanvas offcanvas-end offcanvas-nav" tabindex="-1" id="mobileNav" aria-labelledby="mobileNavLabel">
    <div class="offcanvas-header">
        <a class="brand" href="{{ route('home') }}">
            @if ($logo)<img src="{{ $logo }}" alt="" width="60" height="48" style="height:2.5rem">@endif
            <span class="brand-name" id="mobileNavLabel">{{ setting('site_name') }}</span>
        </a>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close menu"></button>
    </div>
    <div class="offcanvas-body d-flex flex-column">
        <ul class="m-nav">
            <li><a href="{{ route('home') }}" @class(['active' => request()->routeIs('home')])>Home</a></li>
            @php $mobileMenus = ['About' => $menus['About'], 'Programs' => $navPrograms->map(fn ($p) => [$p->title, null, $p->icon, route('programs.show', $p->slug)])->prepend(['All programs', 'programs.index', 'grid'])->all()] + $menus; @endphp
            @foreach ($mobileMenus as $menu => $items)
                @php $id = 'm-'.\Illuminate\Support\Str::slug($menu); @endphp
                <li>
                    <button type="button" data-bs-toggle="collapse" data-bs-target="#{{ $id }}" aria-expanded="false" aria-controls="{{ $id }}">
                        {{ $menu }} <i class="bi bi-chevron-down"></i>
                    </button>
                    <ul class="m-sub collapse" id="{{ $id }}">
                        @foreach ($items as $item)
                            @php $href = $item[3] ?? route($item[1]); @endphp
                            <li><a href="{{ $href }}" @class(['active' => url()->current() === $href])>{{ $item[0] }}</a></li>
                        @endforeach
                    </ul>
                </li>
            @endforeach
            <li><a href="{{ route('contact') }}" @class(['active' => request()->routeIs('contact')])>Contact Us</a></li>
        </ul>
        <div class="mt-auto pt-4 d-grid gap-2">
            <a href="{{ route('donate') }}" class="btn btn-accent btn-lg"><i class="bi bi-heart-fill"></i> Donate Now</a>
            <a href="tel:{{ phone_link(setting('phone')) }}" class="btn btn-outline-primary"><i class="bi bi-telephone"></i> +91 {{ setting('phone') }}</a>
        </div>
    </div>
</div>
