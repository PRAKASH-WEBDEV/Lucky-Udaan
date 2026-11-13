@php
    $links = array_filter([
        'facebook' => setting('facebook'),
        'instagram' => setting('instagram'),
        'youtube' => setting('youtube'),
        'twitter-x' => setting('twitter'),
        'linkedin' => setting('linkedin'),
    ]);
@endphp
@if ($links)
    <div class="{{ $class ?? 'social' }}">
        @foreach ($links as $icon => $url)
            <a href="{{ $url }}" target="_blank" rel="noopener" aria-label="{{ ucfirst(str_replace('-x', '', $icon)) }}"><i class="bi bi-{{ $icon }}"></i></a>
        @endforeach
    </div>
@endif
