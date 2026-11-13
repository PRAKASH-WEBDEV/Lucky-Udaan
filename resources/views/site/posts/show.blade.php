@extends('layouts.site')

@section('content')
<x-page-hero :title="$post->title">
    <div class="d-flex flex-wrap gap-3 text-white-50 small mt-2">
        <span class="pill pill-light">{{ $post->typeLabel() }}</span>
        @if ($post->published_at)<span><i class="bi bi-calendar3 me-1"></i><time datetime="{{ $post->published_at->toDateString() }}">{{ $post->published_at->format('d F Y') }}</time></span>@endif
        @if ($post->author)<span><i class="bi bi-person me-1"></i>{{ $post->author }}</span>@endif
        @if ($post->body)<span><i class="bi bi-clock me-1"></i>{{ $post->readingTime() }} min read</span>@endif
    </div>
</x-page-hero>

<article class="section">
    <div class="container">
        <div class="mx-auto" style="max-width: 50rem">
            @if ($post->image)
                <div class="media-frame ratio-16x10 mb-5"><x-img :path="$post->image" :alt="$post->title" eager sizes="(min-width: 992px) 800px, 100vw" /></div>
            @endif
            @if ($post->excerpt)<p class="lead mb-4">{{ $post->excerpt }}</p>@endif
            @if ($post->video_url && preg_match('~(?:youtu\.be/|v=|embed/|shorts/)([\w-]{11})~', $post->video_url, $m))
                <div class="ratio ratio-16x9 mb-4 rounded-4 overflow-hidden"><iframe src="https://www.youtube-nocookie.com/embed/{{ $m[1] }}" title="{{ $post->title }}" loading="lazy" allowfullscreen></iframe></div>
            @endif
            <div class="prose prose-wide">{!! $post->body !!}</div>
            @if ($post->external_url)
                <p class="mt-4"><a href="{{ $post->external_url }}" target="_blank" rel="noopener" class="btn btn-outline-primary">Read the original{{ $post->source ? ' on '.$post->source : '' }} <i class="bi bi-box-arrow-up-right"></i></a></p>
            @endif
            <hr class="my-5">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 no-print">
                <a href="{{ route($listRoute) }}" class="link-arrow"><i class="bi bi-arrow-left"></i> Back to {{ $listLabel }}</a>
                <div class="d-flex gap-2">
                    <a class="btn btn-sm btn-outline-primary" target="_blank" rel="noopener" href="{{ whatsapp_link('', $post->title.' — '.url()->current()) }}" aria-label="Share on WhatsApp"><i class="bi bi-whatsapp"></i></a>
                    <a class="btn btn-sm btn-outline-primary" target="_blank" rel="noopener" href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" aria-label="Share on Facebook"><i class="bi bi-facebook"></i></a>
                    <a class="btn btn-sm btn-outline-primary" target="_blank" rel="noopener" href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($post->title) }}" aria-label="Share on X"><i class="bi bi-twitter-x"></i></a>
                </div>
            </div>
        </div>
    </div>
</article>

@if ($related->isNotEmpty())
<section class="section bg-surface">
    <div class="container">
        <x-section-head eyebrow="Keep reading" title="More from {{ $listLabel }}" />
        <div class="grid grid-3">@foreach ($related as $r)<x-post-card :post="$r" />@endforeach</div>
    </div>
</section>
@endif
@endsection
