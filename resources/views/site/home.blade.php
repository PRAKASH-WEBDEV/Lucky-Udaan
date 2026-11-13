@extends('layouts.site')

@section('content')
{{-- ============ Hero slider ============ --}}
@php
    $slides = $banners->isNotEmpty() ? $banners : collect([new \App\Models\Banner([
        'eyebrow' => setting('city').', '.setting('state'), 'title' => setting('tagline', 'Together for a Better Tomorrow'),
        'subtitle' => setting('seo_description'), 'button_text' => 'Donate now', 'button_link' => route('donate'),
        'image' => setting('about_image'),
    ])]);
@endphp
<section class="hero" aria-label="Highlights">
    <div id="heroCarousel" class="carousel slide carousel-fade" @if ($slides->count() > 1) data-bs-ride="carousel" data-bs-interval="7000" data-bs-pause="hover" @endif>
        <div class="carousel-inner">
            @foreach ($slides as $i => $b)
                <div @class(['carousel-item', 'active' => $i === 0])>
                    <div class="container hero-inner">
                        <div class="hero-copy">
                            @if ($b->eyebrow)<span class="hero-eyebrow">{{ $b->eyebrow }}</span>@endif
                            @if ($i === 0)<h1>{{ $b->title }}</h1>@else<h2>{{ $b->title }}</h2>@endif
                            @if ($b->subtitle)<p class="hero-sub">{{ $b->subtitle }}</p>@endif
                            <div class="hero-actions">
                                @if ($b->button_text)<a href="{{ $b->button_link ?: route('donate') }}" class="btn btn-accent btn-lg">{{ $b->button_text }} <i class="bi bi-arrow-right"></i></a>@endif
                                @if ($b->button2_text)<a href="{{ $b->button2_link ?: route('about') }}" class="btn btn-light-ghost btn-lg">{{ $b->button2_text }}</a>@endif
                            </div>
                        </div>
                        @if ($b->image)
                            <div class="hero-visual">
                                <div class="media-frame"><x-img :path="$b->image" :alt="$b->title" :eager="$i === 0" sizes="(min-width: 992px) 45vw, 1px" /></div>
                                <div class="hero-chip">
                                    <span class="ic"><i class="bi bi-geo-alt-fill"></i></span>
                                    <span><strong>Serving {{ setting('city', 'Gonda') }}</strong><span>{{ setting('state') }} · {{ $programCount }} focus areas</span></span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        @if ($slides->count() > 1)
            <div class="hero-nav">
                <div class="container">
                    <button class="hero-arrow" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev" aria-label="Previous slide"><i class="bi bi-arrow-left"></i></button>
                    <div class="carousel-indicators">
                        @foreach ($slides as $i => $b)
                            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $i }}" @class(['active' => $i === 0]) @if ($i === 0) aria-current="true" @endif aria-label="Slide {{ $i + 1 }}: {{ $b->title }}"></button>
                        @endforeach
                    </div>
                    <button class="hero-arrow" type="button" data-bs-target="#heroCarousel" data-bs-slide="next" aria-label="Next slide"><i class="bi bi-arrow-right"></i></button>
                </div>
            </div>
        @endif
    </div>
</section>

{{-- ============ Impact counters ============ --}}
<section class="impact-bar" aria-label="Our impact">
    <div class="container">
        <div class="impact-card">
            @foreach ([1 => 'people', 2 => 'heart-pulse', 3 => 'hand-thumbs-up', 4 => 'diagram-3'] as $n => $icon)
                @if (setting("stat{$n}_label"))
                    <div class="stat">
                        <span class="stat-icon"><i class="bi bi-{{ $icon }}"></i></span>
                        <div>
                            <div class="stat-num"><span data-count="{{ (int) setting("stat{$n}_value") }}">{{ number_format((int) setting("stat{$n}_value")) }}</span>{{ setting("stat{$n}_suffix") }}</div>
                            <div class="stat-label">{{ setting("stat{$n}_label") }}</div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</section>

{{-- ============ About ============ --}}
<section class="section">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6 reveal">
                <div class="about-media">
                    <div class="media-frame ratio-16x10"><x-img :path="setting('about_image')" alt="Women at a Lucky Udaan Foundation skill training centre" sizes="(min-width: 992px) 50vw, 100vw" /></div>
                    <div class="about-badge"><strong>{{ $programCount }} focus areas</strong><span>one promise — a better tomorrow</span></div>
                </div>
            </div>
            <div class="col-lg-6">
                <x-section-head eyebrow="Who we are" :title="setting('about_heading', 'About us')" class="mb-4" />
                <p class="lead reveal">{{ setting('about_text') }}</p>
                <div class="row g-4 my-2">
                    <div class="col-sm-6 reveal"><div class="feature-tile"><span class="ic"><i class="bi bi-mortarboard"></i></span><div><h3>Education for all</h3><p>Classrooms, digital literacy and skills for every age.</p></div></div></div>
                    <div class="col-sm-6 reveal"><div class="feature-tile"><span class="ic"><i class="bi bi-heart-pulse"></i></span><div><h3>Health for all</h3><p>Free medical camps, hygiene and nutrition awareness.</p></div></div></div>
                    <div class="col-sm-6 reveal"><div class="feature-tile"><span class="ic"><i class="bi bi-gender-female"></i></span><div><h3>Women empowerment</h3><p>Self-help groups, tailoring and livelihood training.</p></div></div></div>
                    <div class="col-sm-6 reveal"><div class="feature-tile"><span class="ic"><i class="bi bi-tree"></i></span><div><h3>A greener Gonda</h3><p>Plantation drives, clean rivers and water conservation.</p></div></div></div>
                </div>
                <div class="d-flex flex-wrap gap-2 mt-3 reveal">
                    <a href="{{ route('about') }}" class="btn btn-primary">Our story <i class="bi bi-arrow-right"></i></a>
                    <a href="{{ route('transparency') }}" class="btn btn-outline-primary">Transparency</a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ============ Programs ============ --}}
@if ($programs->isNotEmpty())
<section class="section bg-surface">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-end gap-3">
            <x-section-head eyebrow="What we do" title="Programs that change lives" text="From the classroom to the community kitchen, each program answers a real need we see on the ground in Gonda." />
            <a href="{{ route('programs.index') }}" class="btn btn-outline-primary mb-5 reveal">All {{ $programCount }} programs <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="grid">
            @foreach ($programs as $program)
                <x-program-card :program="$program" />
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ============ Campaigns ============ --}}
@if ($campaigns->isNotEmpty())
<section class="section">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-end gap-3">
            <x-section-head eyebrow="Active campaigns" title="Help us reach these goals" text="Choose a cause close to your heart. Every contribution is recorded and acknowledged with a receipt." />
            <a href="{{ route('campaigns.index') }}" class="btn btn-outline-primary mb-5 reveal">View campaigns <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="grid grid-3">
            @foreach ($campaigns as $campaign)
                <x-campaign-card :campaign="$campaign" />
            @endforeach
        </div>
    </div>
</section>
@endif

<x-donate-cta />

{{-- ============ Events + news ============ --}}
<section class="section">
    <div class="container">
        <div class="row g-5">
            <div class="col-xl-5">
                <x-section-head eyebrow="Upcoming events" title="Join us on the ground" class="mb-4" />
                <div class="d-grid gap-3">
                    @forelse ($events as $event)
                        <x-event-row :event="$event" />
                    @empty
                        <x-empty icon="calendar-event" title="New events coming soon" text="Follow us or check back shortly." />
                    @endforelse
                </div>
                <a href="{{ route('events.index') }}" class="link-arrow mt-4">See all events <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="col-xl-7">
                <x-section-head eyebrow="Latest news" title="Stories from the field" class="mb-4" />
                <div class="grid grid-2" style="--min: 15rem">
                    @forelse ($posts->take(2) as $post)
                        <x-post-card :post="$post" />
                    @empty
                        <x-empty icon="newspaper" title="News will appear here" />
                    @endforelse
                </div>
                <a href="{{ route('blog.index') }}" class="link-arrow mt-4">Read the blog <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>
</section>

{{-- ============ Testimonials ============ --}}
@if ($testimonials->isNotEmpty())
<section class="section bg-warm">
    <div class="container">
        <x-section-head eyebrow="Voices of change" title="What our community says" center />
        <div class="grid grid-3">
            @foreach ($testimonials->take(3) as $t)
                <x-quote-card :item="$t" />
            @endforeach
        </div>
        <div class="text-center mt-4"><a href="{{ route('testimonials') }}" class="link-arrow">More testimonials <i class="bi bi-arrow-right"></i></a></div>
    </div>
</section>
@endif

{{-- ============ Get involved ============ --}}
<section class="section">
    <div class="container">
        <x-section-head eyebrow="Get involved" title="There’s a place for everyone" text="Give money, time or skills — or bring your organisation on board." center />
        <div class="grid" style="--min: 15rem">
            @foreach ([
                ['Donate', 'Fund education, health camps and relief with a one-time gift.', 'heart', 'donate'],
                ['Volunteer', 'Teach, organise camps or lend your professional skills.', 'hand-thumbs-up', 'volunteer'],
                ['Become a member', 'Join our family and help shape the work we do.', 'person-badge', 'membership'],
                ['Partner with us', 'CSR, institutional and government collaborations.', 'building', 'partnership'],
            ] as [$t, $d, $i, $r])
                <article class="program-card reveal">
                    <span class="program-icon"><i class="bi bi-{{ $i }}"></i></span>
                    <h3><a href="{{ route($r) }}">{{ $t }}</a></h3>
                    <p>{{ $d }}</p>
                    <span class="link-arrow">Get started <i class="bi bi-arrow-right"></i></span>
                </article>
            @endforeach
        </div>
    </div>
</section>

{{-- ============ Partners ============ --}}
@if ($partners->isNotEmpty())
<section class="section-sm bg-surface">
    <div class="container">
        <p class="text-center text-muted fw-semibold mb-4 reveal">Working together with</p>
        <div class="logo-strip">
            @foreach ($partners as $p)
                <a class="logo-tile reveal" @if ($p->url) href="{{ $p->url }}" target="_blank" rel="noopener" @endif title="{{ $p->name }}">
                    @if ($p->logo)<x-img :path="$p->logo" :alt="$p->name" sizes="200px" />@else{{ $p->name }}@endif
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection
