@props(['item'])
<figure class="quote-card reveal m-0">
    <div class="stars" aria-label="{{ $item->rating }} out of 5 stars">{{ str_repeat('★', $item->rating) }}</div>
    <blockquote>{{ $item->message }}</blockquote>
    <figcaption class="who">
        @if ($item->photo)
            <x-img :path="$item->photo" alt="" class="avatar" width="48" height="48" />
        @else
            <span class="avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr($item->name, 0, 1)) }}</span>
        @endif
        <span><strong>{{ $item->name }}</strong>@if ($item->designation)<span>{{ $item->designation }}</span>@endif</span>
    </figcaption>
</figure>
