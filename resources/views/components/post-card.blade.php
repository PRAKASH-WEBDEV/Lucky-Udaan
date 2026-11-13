@props(['post'])
@php $external = $post->type === 'media' && $post->external_url; @endphp
<article class="card-x reveal">
    <div class="card-media">
        <x-img :path="$post->image" :alt="$post->title" />
        <span class="pill pill-light card-tag">{{ $post->typeLabel() }}</span>
    </div>
    <div class="card-body-x">
        <div class="card-meta">
            @if ($post->published_at)<span><i class="bi bi-calendar3"></i><time datetime="{{ $post->published_at->toDateString() }}">{{ $post->published_at->format('d M Y') }}</time></span>@endif
            @if ($post->source)<span><i class="bi bi-newspaper"></i>{{ $post->source }}</span>@elseif ($post->body)<span><i class="bi bi-clock"></i>{{ $post->readingTime() }} min read</span>@endif
        </div>
        <h3 class="card-title-x"><a href="{{ $post->url() }}" @if ($external) target="_blank" rel="noopener" @endif>{{ $post->title }}</a></h3>
        @if ($post->excerpt)<p class="card-text-x">{{ $post->excerpt }}</p>@endif
        <span class="link-arrow card-foot">{{ $external ? 'Read coverage' : 'Read more' }} <i class="bi bi-{{ $external ? 'box-arrow-up-right' : 'arrow-right' }}"></i></span>
    </div>
</article>
