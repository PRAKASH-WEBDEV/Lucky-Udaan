@extends('layouts.admin')
@section('title', 'Media Library')

@section('content')
<div class="page-head">
    <div>
        <h1>Media Library</h1>
        <p class="sub">All images and documents uploaded to the website ({{ $files->count() }} files). Copy a link to use a file anywhere.</p>
    </div>
    <form class="d-flex gap-2" method="get">
        <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search file names…">
        <button class="btn btn-light border" type="submit"><i class="bi bi-search"></i></button>
    </form>
</div>

@if (! $writable)
    <div class="alert alert-warning"><i class="bi bi-info-circle"></i> This server’s disk is read-only (Vercel), so new uploads can’t be saved here. Existing files are listed below. Connect cloud storage (e.g. Vercel Blob or S3) to enable uploads.</div>
@endif

<form class="card mb-4" method="post" action="{{ route('admin.tools.media.upload') }}" enctype="multipart/form-data">
    @csrf
    <div class="card-body d-flex flex-wrap gap-2 align-items-center">
        <input type="file" name="files[]" class="form-control" style="max-width:420px" multiple accept="image/jpeg,image/png,image/webp,application/pdf" required>
        <button class="btn btn-primary" type="submit" @disabled(! $writable)><i class="bi bi-upload"></i> Upload</button>
        <span class="small text-muted">Images are resized and converted to WebP automatically. Max 10 MB each.</span>
    </div>
</form>

<div class="row g-3">
    @forelse ($files as $f)
        @php $url = upload_url($f['path']); @endphp
        <div class="col-6 col-md-4 col-xl-3 col-xxl-2">
            <div class="card h-100">
                <a href="{{ $url }}" target="_blank" rel="noopener" class="ratio ratio-4x3 bg-light rounded-top overflow-hidden d-block">
                    @if ($f['image'])<img src="{{ upload_small_url($f['path']) ?: $url }}" alt="" loading="lazy" style="object-fit:cover">@else<span class="d-flex align-items-center justify-content-center"><i class="bi bi-file-earmark-pdf fs-1 text-danger"></i></span>@endif
                </a>
                <div class="card-body p-2 small">
                    <div class="text-truncate" title="{{ $f['path'] }}">{{ basename($f['path']) }}</div>
                    <div class="text-muted">{{ number_format($f['size'] / 1024) }} KB · {{ date('d M Y', $f['time']) }}</div>
                    <button type="button" class="btn btn-sm btn-light border w-100 mt-1" onclick="navigator.clipboard.writeText('{{ $url }}').then(() => this.textContent = 'Copied!')"><i class="bi bi-clipboard"></i> Copy link</button>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-muted">No files found.</div>
    @endforelse
</div>
@endsection
