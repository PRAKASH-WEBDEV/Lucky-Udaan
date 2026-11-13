@extends('layouts.site')

@section('content')
<x-page-hero :title="$page->title" :subtitle="$page->subtitle" :image="$page->image" />

<section class="section">
    <div class="container">
        @if (isset($tabs))
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-5">
                <div class="filter-chips" role="tablist" aria-label="Filter posts">
                    @foreach ($tabs as $key => $label)
                        <a href="{{ route($baseRoute, array_filter(['type' => $key, 'q' => $q])) }}" @class(['active' => (string) $type === (string) $key]) role="tab" aria-selected="{{ (string) $type === (string) $key ? 'true' : 'false' }}">{{ $label }}</a>
                    @endforeach
                </div>
                <form action="{{ route($baseRoute) }}" method="get" role="search" class="d-flex gap-2" style="min-width: min(100%, 20rem)">
                    @if ($type)<input type="hidden" name="type" value="{{ $type }}">@endif
                    <label for="post-search" class="visually-hidden">Search posts</label>
                    <input id="post-search" type="search" name="q" value="{{ $q }}" class="form-control" placeholder="Search articles…">
                    <button class="btn btn-primary" type="submit" aria-label="Search"><i class="bi bi-search"></i></button>
                </form>
            </div>
        @endif

        @if ($page->body)<div class="prose mb-5">{!! $page->body !!}</div>@endif

        @if ($items->isEmpty())
            <x-empty icon="newspaper" title="Nothing published yet" text="Please check back soon." />
        @elseif (! empty($compact))
            <div class="d-grid gap-3 mx-auto" style="max-width: 56rem">
                @foreach ($items as $post)
                    <article class="event-row reveal">
                        <div class="date-badge" aria-hidden="true">
                            <span class="m">{{ $post->published_at?->format('M') }}</span>
                            <span class="d">{{ $post->published_at?->format('d') }}</span>
                        </div>
                        <div class="min-w-0">
                            <h2 class="h5 mb-1"><a href="{{ $post->url() }}">{{ $post->title }}</a></h2>
                            <div class="small text-muted mb-2"><time datetime="{{ $post->published_at?->toDateString() }}">{{ $post->published_at?->format('d F Y') }}</time>@if ($post->source) · {{ $post->source }}@endif</div>
                            @if ($post->excerpt)<p class="text-muted mb-0">{{ $post->excerpt }}</p>@endif
                        </div>
                    </article>
                @endforeach
            </div>
            <div class="mt-5">{{ $items->links() }}</div>
        @else
            <div class="grid grid-3">
                @foreach ($items as $post)<x-post-card :post="$post" />@endforeach
            </div>
            <div class="mt-5">{{ $items->links() }}</div>
        @endif
    </div>
</section>
@endsection
