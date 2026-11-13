@extends('layouts.site')

@section('content')
<x-page-hero :title="$campaign->title" :subtitle="$campaign->excerpt" />

<section class="section">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-8">
                <div class="media-frame ratio-16x10 mb-5 reveal"><x-img :path="$campaign->image" :alt="$campaign->title" eager sizes="(min-width: 992px) 66vw, 100vw" /></div>
                <div class="prose reveal">{!! $campaign->description !!}</div>
                <div class="d-flex flex-wrap gap-2 mt-4 no-print">
                    <a class="btn btn-sm btn-outline-primary" target="_blank" rel="noopener" href="{{ whatsapp_link('', $campaign->title.' — '.url()->current()) }}"><i class="bi bi-whatsapp"></i> Share on WhatsApp</a>
                    <a class="btn btn-sm btn-outline-primary" target="_blank" rel="noopener" href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"><i class="bi bi-facebook"></i> Share</a>
                    <a class="btn btn-sm btn-outline-primary" href="#" data-share><i class="bi bi-share"></i> More</a>
                </div>
            </div>
            <aside class="col-lg-4">
                <div class="form-card position-sticky reveal" style="top: calc(var(--header-h) + 1.5rem)">
                    @if ($campaign->goal_amount > 0)
                        <div class="d-flex justify-content-between align-items-end mb-2">
                            <div><div class="h3 mb-0">{{ inr($campaign->raised_amount) }}</div><div class="text-muted small">raised of {{ inr($campaign->goal_amount) }}</div></div>
                            <span class="pill">{{ $campaign->progress() }}%</span>
                        </div>
                        <div class="progress-x mb-4" role="progressbar" aria-label="Funds raised" aria-valuenow="{{ $campaign->progress() }}" aria-valuemin="0" aria-valuemax="100"><span style="width: {{ $campaign->progress() }}%"></span></div>
                    @endif
                    <ul class="info-list mb-4">
                        @if ($campaign->program)<li><span class="ic"><i class="bi bi-diagram-3"></i></span><div><strong>Program</strong><a href="{{ route('programs.show', $campaign->program->slug) }}">{{ $campaign->program->title }}</a></div></li>@endif
                        @if ($campaign->end_date)<li><span class="ic"><i class="bi bi-calendar-check"></i></span><div><strong>Runs till</strong>{{ $campaign->end_date->format('d F Y') }}</div></li>@endif
                    </ul>
                    <a href="{{ route('donate', ['campaign' => $campaign->slug]) }}" class="btn btn-accent btn-lg w-100"><i class="bi bi-heart-fill"></i> Donate to this campaign</a>
                    <p class="small text-muted mt-3 mb-0"><i class="bi bi-shield-check text-brand"></i> Every donation is acknowledged with a receipt.</p>
                </div>
            </aside>
        </div>
    </div>
</section>

@if ($others->isNotEmpty())
<section class="section bg-surface">
    <div class="container">
        <x-section-head eyebrow="More ways to help" title="Other campaigns" />
        <div class="grid grid-3">@foreach ($others as $c)<x-campaign-card :campaign="$c" />@endforeach</div>
    </div>
</section>
@endif
@endsection
