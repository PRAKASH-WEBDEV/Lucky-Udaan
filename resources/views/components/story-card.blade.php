@props(['story'])
<article class="card-x reveal">
    <div class="card-media"><x-img :path="$story->image" :alt="$story->title" /></div>
    <div class="card-body-x">
        @if ($story->person_name || $story->location)
            <div class="card-meta">
                @if ($story->person_name)<span><i class="bi bi-person"></i>{{ $story->person_name }}</span>@endif
                @if ($story->location)<span><i class="bi bi-geo-alt"></i>{{ $story->location }}</span>@endif
            </div>
        @endif
        <h3 class="card-title-x"><a href="{{ route('stories.show', $story->slug) }}">{{ $story->title }}</a></h3>
        <p class="card-text-x">{{ $story->excerpt }}</p>
        <span class="link-arrow card-foot">Read the story <i class="bi bi-arrow-right"></i></span>
    </div>
</article>
