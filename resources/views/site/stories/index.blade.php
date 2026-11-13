@extends('layouts.site')

@section('content')
<x-page-hero :title="$page->title" :subtitle="$page->subtitle" :image="$page->image" />

<section class="section">
    <div class="container">
        @if ($page->body)<div class="prose mb-5">{!! $page->body !!}</div>@endif
        @if ($items->isEmpty())
            <x-empty icon="stars" title="Stories coming soon" />
        @else
            <div class="grid grid-3">@foreach ($items as $story)<x-story-card :story="$story" />@endforeach</div>
            <div class="mt-5">{{ $items->links() }}</div>
        @endif
    </div>
</section>
<x-donate-cta title="Help us write the next success story" />
@endsection
