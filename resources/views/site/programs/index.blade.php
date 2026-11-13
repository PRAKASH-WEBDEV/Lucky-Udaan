@extends('layouts.site')

@section('content')
<x-page-hero :title="$page->title" :subtitle="$page->subtitle" :image="$page->image" />

<section class="section">
    <div class="container">
        @if ($page->body)
            <div class="prose mx-auto text-center mb-5 reveal">{!! $page->body !!}</div>
        @endif
        <div class="grid">
            @forelse ($items as $program)
                <x-program-card :program="$program" level="h2" />
            @empty
                <x-empty icon="diagram-3" title="Programs will be listed soon" />
            @endforelse
        </div>
    </div>
</section>

<x-donate-cta />
@endsection
