@props(['event'])
<article class="event-row reveal">
    <div class="date-badge" aria-hidden="true">
        <span class="m">{{ $event->starts_at->format('M') }}</span>
        <span class="d">{{ $event->starts_at->format('d') }}</span>
    </div>
    <div class="min-w-0">
        <h3><a href="{{ route('events.show', $event->slug) }}">{{ $event->title }}</a></h3>
        <div class="card-meta d-flex flex-wrap gap-3 small text-muted">
            <span><i class="bi bi-clock text-brand me-1"></i><time datetime="{{ $event->starts_at->toIso8601String() }}">{{ $event->starts_at->format('D, d M Y · h:i A') }}</time></span>
            @if ($event->venue)<span><i class="bi bi-geo-alt text-brand me-1"></i>{{ $event->venue }}</span>@endif
        </div>
        @if ($event->excerpt)<p class="text-muted small mb-0 mt-2">{{ \Illuminate\Support\Str::limit($event->excerpt, 140) }}</p>@endif
    </div>
</article>
