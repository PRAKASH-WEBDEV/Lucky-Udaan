@extends('layouts.site')

@section('content')
<x-page-hero :title="$page->title" :subtitle="$page->subtitle" :image="$page->image" />

<section class="section">
    <div class="container">
        <div class="row g-5 align-items-start">
            <div class="col-lg-7">
                <div class="prose reveal">{!! $page->body !!}</div>
            </div>
            <aside class="col-lg-5">
                <div class="about-media mb-5 reveal">
                    <div class="media-frame ratio-1x1"><x-img :path="setting('about_image')" alt="Community programme by {{ setting('site_name') }}" sizes="(min-width: 992px) 40vw, 100vw" /></div>
                    <div class="about-badge"><strong>{{ setting('tagline') }}</strong><span>{{ setting('city') }}, {{ setting('state') }}</span></div>
                </div>
                <div class="form-card reveal">
                    <h2 class="h4 mb-3">At a glance</h2>
                    <ul class="info-list">
                        <li><span class="ic"><i class="bi bi-building-check"></i></span><div><strong>Registered as</strong>{{ setting('legal_name') }}</div></li>
                        @if (setting('cin'))<li><span class="ic"><i class="bi bi-hash"></i></span><div><strong>CIN</strong>{{ setting('cin') }}</div></li>@endif
                        <li><span class="ic"><i class="bi bi-geo-alt"></i></span><div><strong>Head office</strong>{{ full_address() }}</div></li>
                        @if (setting('contact_person'))<li><span class="ic"><i class="bi bi-person"></i></span><div><strong>Contact person</strong>{{ setting('contact_person') }}</div></li>@endif
                    </ul>
                </div>
            </aside>
        </div>
    </div>
</section>

<section class="section bg-surface">
    <div class="container">
        <div class="grid grid-3" style="--min: 16rem">
            @foreach ([
                ['Our vision', 'eye', 'A Gonda — and an India — where every child learns, every family is healthy, every woman is self-reliant and every living being is treated with compassion.'],
                ['Our mission', 'bullseye', 'To deliver education, healthcare, livelihood, relief and environmental programmes with honesty and dignity, working hand in hand with communities, government and partners.'],
                ['Our values', 'gem', 'Compassion, transparency, inclusion and accountability. We operate without any profit motive — every resource serves the cause.'],
            ] as [$t, $i, $d])
                <article class="program-card reveal">
                    <span class="program-icon"><i class="bi bi-{{ $i }}"></i></span>
                    <h2 class="h4 mb-0">{{ $t }}</h2>
                    <p style="-webkit-line-clamp: unset">{{ $d }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <x-section-head eyebrow="Our focus areas" title="{{ $programs->count() }} programs, one purpose" text="Each objective in our charter is a living program with its own team, volunteers and partners." center />
        <div class="grid">
            @foreach ($programs as $program)
                <x-program-card :program="$program" />
            @endforeach
        </div>
    </div>
</section>

<section class="section bg-warm">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-4">
                <x-section-head eyebrow="How we work" title="What we do beyond programs" text="The everyday work that keeps our programs effective, accountable and rooted in the community." />
            </div>
            <div class="col-lg-8">
                <div class="grid grid-2" style="--min: 15rem">
                    @foreach ([
                        ['easel', 'Seminars, workshops & training', 'Capacity-building for youth, women, teachers and community leaders.'],
                        ['megaphone', 'Awareness campaigns', 'Street plays, art, music and documentaries that spark change.'],
                        ['clipboard-data', 'Surveys & impact assessment', 'We measure outcomes and document best practices and impact stories.'],
                        ['people', 'Volunteers, interns & professionals', 'Engaging skilled people who want to give back.'],
                        ['book', 'Learning & wellness centres', 'Educational material, libraries and wellness spaces.'],
                        ['building', 'Partnerships & CSR', 'Working with government bodies, institutions, trusts and companies.'],
                        ['person-up', 'Leadership programmes', 'Building confident young and women leaders.'],
                        ['newspaper', 'Publications & events', 'Newsletters, exhibitions and public events that bring people together.'],
                    ] as [$i, $t, $d])
                        <div class="feature-tile reveal"><span class="ic"><i class="bi bi-{{ $i }}"></i></span><div><h3>{{ $t }}</h3><p>{{ $d }}</p></div></div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

@if ($team->isNotEmpty())
<section class="section">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-end gap-3">
            <x-section-head eyebrow="Leadership" title="The people behind the mission" />
            <a href="{{ route('team') }}" class="btn btn-outline-primary mb-5">Meet the team <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="grid">
            @foreach ($team as $member)
                @include('site.partials.team-card', ['member' => $member])
            @endforeach
        </div>
    </div>
</section>
@endif

<x-donate-cta />
@endsection
