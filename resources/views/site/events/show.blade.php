@extends('layouts.site')

@section('content')
<x-page-hero :title="$event->title" :subtitle="$event->excerpt" />

<section class="section">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-8">
                @if ($event->image)
                    <div class="media-frame ratio-16x10 mb-5 reveal"><x-img :path="$event->image" :alt="$event->title" eager sizes="(min-width: 992px) 66vw, 100vw" /></div>
                @endif
                <div class="prose reveal">{!! $event->description !!}</div>
            </div>
            <aside class="col-lg-4">
                <div class="form-card position-sticky reveal" style="top: calc(var(--header-h) + 1.5rem)">
                    <h2 class="h5 mb-4">Event details</h2>
                    <ul class="info-list mb-4">
                        <li><span class="ic"><i class="bi bi-calendar3"></i></span><div><strong>Date</strong>{{ $event->starts_at->format('l, d F Y') }}@if ($event->ends_at && ! $event->ends_at->isSameDay($event->starts_at)) – {{ $event->ends_at->format('d F Y') }}@endif</div></li>
                        <li><span class="ic"><i class="bi bi-clock"></i></span><div><strong>Time</strong>{{ $event->starts_at->format('h:i A') }}@if ($event->ends_at) – {{ $event->ends_at->format('h:i A') }}@endif</div></li>
                        @if ($event->venue)<li><span class="ic"><i class="bi bi-geo-alt"></i></span><div><strong>Venue</strong>{{ $event->venue }}</div></li>@endif
                    </ul>
                    @if ($event->isPast())
                        <p class="notice mb-0">This event has ended. Thank you to everyone who took part!</p>
                    @elseif ($event->registration_link)
                        <a href="{{ $event->registration_link }}" target="_blank" rel="noopener" class="btn btn-primary w-100">Register now <i class="bi bi-box-arrow-up-right"></i></a>
                    @else
                        <a href="{{ route('volunteer') }}" class="btn btn-primary w-100">Volunteer at this event</a>
                        <a href="{{ route('contact') }}" class="btn btn-outline-primary w-100 mt-2">Ask a question</a>
                    @endif
                </div>
            </aside>
        </div>
    </div>
</section>

@if ($others->isNotEmpty())
<section class="section bg-surface">
    <div class="container">
        <x-section-head eyebrow="Coming up" title="More upcoming events" />
        <div class="grid grid-3" style="--min: 18rem">@foreach ($others as $e)<x-event-row :event="$e" />@endforeach</div>
    </div>
</section>
@endif
@endsection
