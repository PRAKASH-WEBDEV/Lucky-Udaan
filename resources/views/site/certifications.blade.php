@extends('layouts.site')

@section('content')
<x-page-hero :title="$page->title" :subtitle="$page->subtitle" :image="$page->image" />

<section class="section">
    <div class="container">
        @if ($page->body)<div class="prose mb-5">{!! $page->body !!}</div>@endif
        <div class="grid grid-2">
            @forelse ($items as $c)
                <article class="form-card d-flex flex-column flex-sm-row gap-4 reveal">
                    <div class="media-frame ratio-3x4 flex-shrink-0" style="width: min(100%, 9rem)">
                        @if ($c->image)
                            <x-img :path="$c->image" :alt="$c->title" sizes="160px" />
                        @else
                            <div class="w-100 h-100 d-grid" style="place-items:center;color:var(--brand);font-size:2.5rem"><i class="bi bi-patch-check"></i></div>
                        @endif
                    </div>
                    <div>
                        <h2 class="h5">{{ $c->title }}</h2>
                        <dl class="small mb-2">
                            <dt class="d-inline text-muted">Number:</dt> <dd class="d-inline">{{ $c->number ?: 'To be updated' }}</dd><br>
                            @if ($c->issued_by)<dt class="d-inline text-muted">Issued by:</dt> <dd class="d-inline">{{ $c->issued_by }}</dd><br>@endif
                            @if ($c->issued_on)<dt class="d-inline text-muted">Issued on:</dt> <dd class="d-inline">{{ $c->issued_on->format('d M Y') }}</dd><br>@endif
                            @if ($c->valid_till)<dt class="d-inline text-muted">Valid till:</dt> <dd class="d-inline">{{ $c->valid_till->format('d M Y') }}</dd>@endif
                        </dl>
                        @if ($c->description)<p class="text-muted small">{{ $c->description }}</p>@endif
                        @if ($c->file)<a class="btn btn-sm btn-outline-primary" href="{{ upload_url($c->file) }}" target="_blank" rel="noopener"><i class="bi bi-file-earmark-pdf"></i> View certificate</a>@endif
                    </div>
                </article>
            @empty
                <x-empty icon="patch-check" title="Certificates will be published soon" />
            @endforelse
        </div>
    </div>
</section>
@endsection
