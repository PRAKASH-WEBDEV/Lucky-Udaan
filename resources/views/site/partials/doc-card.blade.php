{{-- Certificate / report card. Expects $title, $meta, $text, $file, $image --}}
<article class="doc-card reveal">
    @if (! empty($image))
        <div class="media-frame ratio-3x4 flex-shrink-0" style="width: 4.5rem; border-radius: .6rem"><x-img :path="$image" alt="" sizes="80px" /></div>
    @else
        <span class="doc-ic"><i class="bi bi-file-earmark-{{ ! empty($file) ? 'pdf' : 'text' }}"></i></span>
    @endif
    <div class="min-w-0 flex-grow-1">
        <h3>{{ $title }}</h3>
        @if (! empty($meta))<div class="small text-muted mb-1">{{ $meta }}</div>@endif
        @if (! empty($text))<p class="small text-muted mb-2">{{ \Illuminate\Support\Str::limit($text, 160) }}</p>@endif
        @if (! empty($file))
            <a href="{{ upload_url($file) }}" target="_blank" rel="noopener" class="link-arrow small">View / download PDF <i class="bi bi-download"></i></a>
        @elseif (! empty($pending))
            <span class="small text-muted"><i class="bi bi-hourglass-split"></i> Document will be published soon</span>
        @endif
    </div>
</article>
