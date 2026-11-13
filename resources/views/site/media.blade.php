@extends('layouts.site')

@section('content')
<x-page-hero :title="$page->title" :subtitle="$page->subtitle" :image="$page->image" />

<section class="section">
    <div class="container">
        @if ($page->body)<div class="prose mb-5">{!! $page->body !!}</div>@endif

        <x-section-head eyebrow="In the news" title="Media coverage" />
        @if ($coverage->isEmpty())
            <x-empty icon="newspaper" title="Coverage will appear here" text="Journalists can reach us through the contact page." />
        @else
            <div class="grid grid-3">@foreach ($coverage as $post)<x-post-card :post="$post" />@endforeach</div>
            <div class="mt-5">{{ $coverage->links() }}</div>
        @endif
    </div>
</section>

@if ($videos->isNotEmpty())
<section class="section bg-surface">
    <div class="container">
        <x-section-head eyebrow="Watch" title="Videos" />
        <div class="grid grid-3">
            @foreach ($videos as $v)
                <button type="button" class="card-x text-start p-0 border reveal" data-lightbox data-video="{{ $v->embedUrl() }}" data-caption="{{ $v->title }}">
                    <span class="card-media d-block position-relative w-100">
                        <img src="{{ $v->image ? upload_url($v->image) : 'https://i.ytimg.com/vi/'.$v->youtubeId().'/hqdefault.jpg' }}" alt="{{ $v->title }}" loading="lazy">
                        <span class="position-absolute top-50 start-50 translate-middle text-white" style="font-size:3.5rem;text-shadow:0 4px 20px rgba(0,0,0,.5)"><i class="bi bi-play-circle-fill"></i></span>
                    </span>
                    <span class="card-body-x"><span class="card-title-x h5">{{ $v->title }}</span></span>
                </button>
            @endforeach
        </div>
    </div>
</section>
@endif

<section class="section-sm">
    <div class="container">
        <div class="form-card d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div>
                <h2 class="h4 mb-1">Media enquiries</h2>
                <p class="text-muted mb-0">For interviews, photographs or information, contact {{ setting('contact_person') ?: 'our team' }}.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="mailto:{{ setting('email') }}" class="btn btn-primary"><i class="bi bi-envelope"></i> Email us</a>
                <a href="{{ route('press.index') }}" class="btn btn-outline-primary">Press releases</a>
            </div>
        </div>
    </div>
</section>
@include('site.partials.lightbox')
@endsection
