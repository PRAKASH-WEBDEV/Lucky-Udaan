@extends('layouts.site')

@section('content')
<x-page-hero :title="$page->title" :subtitle="$page->subtitle" :image="$page->image" />

<section class="section bg-warm">
    <div class="container">
        @if ($page->body)<div class="prose mx-auto text-center mb-5">{!! $page->body !!}</div>@endif
        <div class="grid grid-3">
            @forelse ($items as $t)
                <x-quote-card :item="$t" />
            @empty
                <x-empty icon="chat-quote" title="Testimonials coming soon" />
            @endforelse
        </div>
    </div>
</section>
<x-donate-cta />
@endsection
