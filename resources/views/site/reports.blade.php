@extends('layouts.site')

@section('content')
<x-page-hero :title="$page->title" :subtitle="$page->subtitle" :image="$page->image" />

<section class="section">
    <div class="container">
        @if ($page->body)<div class="prose mb-5">{!! $page->body !!}</div>@endif
        @forelse ($items as $type => $reports)
            <h2 class="h3 mb-4 reveal">{{ \App\Models\Report::TYPES[$type] ?? ucfirst($type) }}s</h2>
            <div class="grid grid-3 mb-5" style="--min: 18rem">
                @foreach ($reports as $r)
                    @include('site.partials.doc-card', ['title' => $r->title, 'meta' => $r->year ? 'Financial year '.$r->year : null, 'text' => $r->summary, 'file' => $r->file, 'image' => $r->cover, 'pending' => true])
                @endforeach
            </div>
        @empty
            <x-empty icon="file-earmark-bar-graph" title="Our first annual report is being prepared" text="It will be published here once the accounts are audited." />
        @endforelse
    </div>
</section>
@endsection
