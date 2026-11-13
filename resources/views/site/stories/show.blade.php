@extends('layouts.site')

@section('content')
<x-page-hero :title="$story->title" :subtitle="trim($story->person_name.($story->location ? ' · '.$story->location : ''))" />

<article class="section">
    <div class="container">
        <div class="mx-auto" style="max-width: 50rem">
            @if ($story->image)
                <div class="media-frame ratio-16x10 mb-5"><x-img :path="$story->image" :alt="$story->title" eager sizes="(min-width: 992px) 800px, 100vw" /></div>
            @endif
            <p class="lead mb-4">{{ $story->excerpt }}</p>
            <div class="prose prose-wide">{!! $story->body !!}</div>
            <hr class="my-5">
            <a href="{{ route('stories.index') }}" class="link-arrow"><i class="bi bi-arrow-left"></i> All success stories</a>
        </div>
    </div>
</article>

@if ($others->isNotEmpty())
<section class="section bg-surface">
    <div class="container">
        <x-section-head eyebrow="More stories" title="Lives transformed" />
        <div class="grid grid-3">@foreach ($others as $s)<x-story-card :story="$s" />@endforeach</div>
    </div>
</section>
@endif
<x-donate-cta />
@endsection
