@props(['path' => null, 'alt' => '', 'eager' => false, 'sizes' => '(min-width: 1200px) 33vw, (min-width: 768px) 50vw, 100vw', 'fallback' => null])
@php
    $src = upload_url($path, $fallback);
    $small = upload_small_url($path);
@endphp
@if ($src)
    <img src="{{ $src }}"
         @if ($small) srcset="{{ $small }} 640w, {{ $src }} 1920w" sizes="{{ $sizes }}" @endif
         alt="{{ $alt }}"
         @if ($eager) fetchpriority="high" @else loading="lazy" @endif
         decoding="async"
         {{ $attributes }}>
@else
    <div {{ $attributes->merge(['class' => 'w-100 h-100 d-grid place-items-center']) }} style="display:grid;place-items:center;background:linear-gradient(135deg,rgba(var(--brand-rgb),.15),rgba(var(--accent-rgb),.2));color:var(--brand)" role="img" aria-label="{{ $alt }}">
        <i class="bi bi-image" style="font-size:2rem;opacity:.5" aria-hidden="true"></i>
    </div>
@endif
