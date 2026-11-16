@extends('layouts.admin')
@section('title', 'All Website Links')

@section('content')
<div class="page-head">
    <div>
        <h1>All Website Links</h1>
        <p class="sub">Every public page of the website. Click <i class="bi bi-clipboard"></i> to copy a link for WhatsApp, social media or print.</p>
    </div>
    <a href="{{ url('/sitemap.xml') }}" target="_blank" rel="noopener" class="btn btn-light border"><i class="bi bi-diagram-3"></i> sitemap.xml</a>
</div>

<div class="row g-4">
@foreach ($groups as $group => $links)
    <div class="col-xl-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between"><span>{{ $group }}</span><span class="text-muted small">{{ count($links) }}</span></div>
            <ul class="list-group list-group-flush">
                @foreach ($links as $link)
                    @php [$label, $href] = $link; $edit = $link[2] ?? null; @endphp
                    <li class="list-group-item d-flex align-items-center gap-2">
                        <span class="flex-grow-1 min-w-0">
                            <span class="d-block text-truncate">{{ $label }}</span>
                            @if ($href)<a href="{{ $href }}" target="_blank" rel="noopener" class="small text-muted text-decoration-none d-block text-truncate">{{ $href }}</a>@endif
                        </span>
                        @if ($href)
                            <button type="button" class="btn btn-sm btn-light border btn-icon" title="Copy link" aria-label="Copy link"
                                    onclick="navigator.clipboard.writeText('{{ $href }}').then(() => { this.innerHTML = '<i class=\'bi bi-check2\'></i>'; setTimeout(() => this.innerHTML = '<i class=\'bi bi-clipboard\'></i>', 1500) })"><i class="bi bi-clipboard"></i></button>
                        @endif
                        @if ($edit)<a href="{{ $edit }}" class="btn btn-sm btn-light border btn-icon" title="Edit" aria-label="Edit"><i class="bi bi-pencil"></i></a>@endif
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
@endforeach
</div>
@endsection
