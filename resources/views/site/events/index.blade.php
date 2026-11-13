@extends('layouts.site')

@section('content')
<x-page-hero :title="$page->title" :subtitle="$page->subtitle" :image="$page->image" />

<section class="section">
    <div class="container">
        <div class="filter-chips mb-4" role="tablist" aria-label="Event filter">
            <a href="{{ route('events.index') }}" @class(['active' => ! $past]) role="tab" aria-selected="{{ $past ? 'false' : 'true' }}">Upcoming</a>
            <a href="{{ route('events.index', ['show' => 'past']) }}" @class(['active' => $past]) role="tab" aria-selected="{{ $past ? 'true' : 'false' }}">Past events</a>
        </div>
        @if ($page->body)<div class="prose mb-4">{!! $page->body !!}</div>@endif
        @if ($items->isEmpty())
            <x-empty icon="calendar-event" :title="$past ? 'No past events yet' : 'No upcoming events scheduled'" text="Follow our announcements for new dates." />
        @else
            <div class="grid grid-3">
                @foreach ($items as $event)
                    <article class="card-x reveal">
                        <div class="card-media">
                            <x-img :path="$event->image" :alt="$event->title" />
                            <span class="pill pill-light card-tag"><i class="bi bi-calendar3"></i> {{ $event->starts_at->format('d M Y') }}</span>
                        </div>
                        <div class="card-body-x">
                            <div class="card-meta">
                                <span><i class="bi bi-clock"></i>{{ $event->starts_at->format('h:i A') }}</span>
                                @if ($event->venue)<span><i class="bi bi-geo-alt"></i>{{ $event->venue }}</span>@endif
                            </div>
                            <h2 class="card-title-x"><a href="{{ route('events.show', $event->slug) }}">{{ $event->title }}</a></h2>
                            <p class="card-text-x">{{ $event->excerpt }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
            <div class="mt-5">{{ $items->links() }}</div>
        @endif
    </div>
</section>
@endsection
