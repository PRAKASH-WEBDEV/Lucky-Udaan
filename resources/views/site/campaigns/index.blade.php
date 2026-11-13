@extends('layouts.site')

@section('content')
<x-page-hero :title="$page->title" :subtitle="$page->subtitle" :image="$page->image" />

<section class="section">
    <div class="container">
        @if ($page->body)<div class="prose mb-5 reveal">{!! $page->body !!}</div>@endif
        @if ($items->isEmpty())
            <x-empty icon="megaphone" title="No active campaigns right now" text="You can still support our ongoing programs.">
                <a href="{{ route('donate') }}" class="btn btn-primary mt-3">Donate to the general fund</a>
            </x-empty>
        @else
            <div class="grid grid-3">
                @foreach ($items as $campaign)<x-campaign-card :campaign="$campaign" />@endforeach
            </div>
            <div class="mt-5">{{ $items->links() }}</div>
        @endif
    </div>
</section>
@endsection
