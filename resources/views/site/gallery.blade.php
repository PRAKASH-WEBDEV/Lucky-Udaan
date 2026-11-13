@extends('layouts.site')

@section('content')
<x-page-hero :title="$page->title" :subtitle="$page->subtitle" :image="$page->image" />

<section class="section">
    <div class="container">
        @if ($categories->isNotEmpty())
            <nav class="filter-chips mb-5" aria-label="Albums">
                <a href="{{ route('gallery') }}" @class(['active' => ! $active]) @if (! $active) aria-current="page" @endif>All</a>
                @foreach ($categories as $cat)
                    <a href="{{ route('gallery', ['album' => $cat]) }}" @class(['active' => $active === $cat]) @if ($active === $cat) aria-current="page" @endif>{{ $cat }}</a>
                @endforeach
            </nav>
        @endif

        @if ($items->isEmpty())
            <x-empty icon="images" title="Photos coming soon" />
        @else
            <div class="gallery-grid">
                @foreach ($items as $item)
                    @php
                        $thumb = $item->image ? (upload_small_url($item->image) ?: upload_url($item->image)) : ($item->youtubeId() ? 'https://i.ytimg.com/vi/'.$item->youtubeId().'/hqdefault.jpg' : null);
                    @endphp
                    @continue(! $thumb)
                    <button type="button" class="gallery-item reveal" data-lightbox
                            @if ($item->type === 'video') data-video="{{ $item->embedUrl() }}" @else data-src="{{ upload_url($item->image) }}" @endif
                            data-caption="{{ $item->title }}" aria-label="Open {{ $item->type === 'video' ? 'video' : 'photo' }}: {{ $item->title }}">
                        <img src="{{ $thumb }}" alt="{{ $item->title }}" loading="lazy" decoding="async">
                        @if ($item->type === 'video')<span class="play" aria-hidden="true"><i class="bi bi-play-circle-fill"></i></span>@endif
                        <span class="gallery-cap">{{ $item->title }}</span>
                    </button>
                @endforeach
            </div>
            <div class="mt-4">{{ $items->links() }}</div>
        @endif
    </div>
</section>

@include('site.partials.lightbox')
@endsection
