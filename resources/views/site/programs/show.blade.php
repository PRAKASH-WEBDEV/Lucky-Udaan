@extends('layouts.site')

@section('content')
<x-page-hero :title="$program->title" :subtitle="$program->excerpt">
    <div class="d-flex flex-wrap gap-2 mt-4">
        <a href="{{ route('donate') }}" class="btn btn-accent"><i class="bi bi-heart-fill"></i> Support this program</a>
        <a href="{{ route('volunteer') }}" class="btn btn-light-ghost">Volunteer</a>
    </div>
</x-page-hero>

<section class="section">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-8">
                @if ($program->image)
                    <div class="media-frame ratio-16x10 mb-5 reveal"><x-img :path="$program->image" :alt="$program->title" eager sizes="(min-width: 992px) 66vw, 100vw" /></div>
                @endif
                <div class="prose reveal">{!! $program->description !!}</div>

                @if ($highlights = $program->highlightList())
                    <div class="form-card mt-5 reveal">
                        <h2 class="h4 mb-4">What we do under this program</h2>
                        <ul class="check-list" style="columns: 2 18rem; display: block">
                            @foreach ($highlights as $h)<li class="mb-3" style="break-inside: avoid">{{ $h }}</li>@endforeach
                        </ul>
                    </div>
                @endif
            </div>
            <aside class="col-lg-4">
                <div class="position-sticky" style="top: calc(var(--header-h) + 1.5rem)">
                    <div class="bank-card mb-4 reveal">
                        <span class="program-icon d-inline-grid mb-3" style="width:3.5rem;height:3.5rem;border-radius:1rem;place-items:center;background:rgba(255,255,255,.15);font-size:1.5rem"><i class="bi bi-{{ $program->icon ?: 'heart' }}"></i></span>
                        <h2 class="h4 text-white">Be part of this change</h2>
                        <p class="text-white-50 mb-4">Your support helps us reach more people through {{ \Illuminate\Support\Str::lower($program->title) }}.</p>
                        <div class="d-grid gap-2">
                            <a href="{{ route('donate') }}" class="btn btn-accent">Donate now</a>
                            <a href="{{ route('partnership') }}" class="btn btn-light-ghost">Partner with us</a>
                        </div>
                    </div>
                    @if ($others->isNotEmpty())
                        <div class="form-card reveal">
                            <h2 class="h5 mb-3">Other programs</h2>
                            <ul class="list-unstyled d-grid gap-2 mb-0">
                                @foreach ($others as $o)
                                    <li><a href="{{ route('programs.show', $o->slug) }}" class="d-flex align-items-center gap-2 text-decoration-none text-dark fw-semibold py-1"><i class="bi bi-{{ $o->icon ?: 'heart' }} text-brand"></i>{{ $o->title }}</a></li>
                                @endforeach
                            </ul>
                            <a class="link-arrow mt-3" href="{{ route('programs.index') }}">All programs <i class="bi bi-arrow-right"></i></a>
                        </div>
                    @endif
                </div>
            </aside>
        </div>
    </div>
</section>

@if ($campaigns->isNotEmpty())
<section class="section bg-surface">
    <div class="container">
        <x-section-head eyebrow="Related campaigns" title="Support {{ $program->title }} today" />
        <div class="grid grid-3">
            @foreach ($campaigns as $c)<x-campaign-card :campaign="$c" />@endforeach
        </div>
    </div>
</section>
@endif
@endsection
