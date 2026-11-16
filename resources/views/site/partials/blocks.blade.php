{{--
    Website sections managed in the dashboard (Website → Our Values / Our Journey / Awards / Support Leaders,
    Content → Trust Wall, Website → Social Embeds). Each section only renders when it has entries.
    Usage: @include('site.partials.blocks', ['show' => ['value', 'journey', ...]])
--}}
@php
    $blocks = rescue(fn () => \App\Models\SiteBlock::active()->ordered()->whereIn('type', $show)->get()->groupBy('type'), collect(), false);
    $embedSrc = function ($b) {
        // Only iframes from known platforms are rendered; anything else in the embed code is ignored.
        $src = null;
        if ($b->embed_code && preg_match('/<iframe[^>]+src=["\']([^"\']+)["\']/i', $b->embed_code, $m)) {
            $src = html_entity_decode($m[1]);
        } elseif ($b->url && preg_match('~(?:youtube\.com/(?:watch\?v=|shorts/|embed/)|youtu\.be/)([\w-]{11})~', $b->url, $m)) {
            $src = 'https://www.youtube-nocookie.com/embed/'.$m[1];
        } elseif ($b->url && preg_match('~instagram\.com/(p|reel)/([\w-]+)~', $b->url, $m)) {
            $src = 'https://www.instagram.com/'.$m[1].'/'.$m[2].'/embed';
        }
        $host = $src ? strtolower((string) parse_url($src, PHP_URL_HOST)) : '';
        $allowed = ['www.youtube.com', 'youtube.com', 'www.youtube-nocookie.com', 'www.facebook.com', 'facebook.com', 'www.instagram.com', 'instagram.com', 'platform.twitter.com', 'twitframe.com', 'www.google.com', 'maps.google.com', 'player.vimeo.com', 'www.linkedin.com'];

        return $src && str_starts_with($src, 'https://') && in_array($host, $allowed, true) ? $src : null;
    };
@endphp

@if (in_array('value', $show) && ($items = $blocks->get('value'))?->isNotEmpty())
<section class="section">
    <div class="container">
        <x-section-head eyebrow="What guides us" title="Our values" center />
        <div class="grid grid-3" style="--min: 15rem">
            @foreach ($items as $b)
                <article class="program-card reveal">
                    <span class="program-icon"><i class="bi bi-{{ $b->icon ?: 'gem' }}"></i></span>
                    <h3 class="h5 mb-0">{{ $b->title }}</h3>
                    @if ($b->description)<p style="-webkit-line-clamp: unset">{{ $b->description }}</p>@endif
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif

@if (in_array('journey', $show) && ($items = $blocks->get('journey'))?->isNotEmpty())
<section class="section bg-surface">
    <div class="container" style="max-width: 860px">
        <x-section-head eyebrow="Since the beginning" title="Our journey" center />
        <ol class="list-unstyled m-0" style="border-left: 3px solid var(--bs-primary, #1f7a4d); padding-left: 1.5rem">
            @foreach ($items as $b)
                <li class="reveal mb-4 position-relative">
                    <span style="position:absolute;left:-2.05rem;top:.35rem;width:1rem;height:1rem;border-radius:50%;background:var(--bs-primary,#1f7a4d);box-shadow:0 0 0 4px #fff"></span>
                    @if ($b->value)<div class="fw-bold text-primary">{{ $b->value }}</div>@endif
                    <h3 class="h5 mb-1">{{ $b->title }}</h3>
                    @if ($b->description)<p class="mb-0 text-muted">{{ $b->description }}</p>@endif
                </li>
            @endforeach
        </ol>
    </div>
</section>
@endif

@if (in_array('award', $show) && ($items = $blocks->get('award'))?->isNotEmpty())
<section class="section">
    <div class="container">
        <x-section-head eyebrow="Recognition" title="Awards & honours" center />
        <div class="grid grid-3" style="--min: 15rem">
            @foreach ($items as $b)
                <article class="program-card reveal">
                    @if ($b->image)<div class="media-frame ratio-16x10 mb-3"><x-img :path="$b->image" :alt="$b->title" sizes="(min-width: 992px) 33vw, 100vw" /></div>@else<span class="program-icon"><i class="bi bi-trophy"></i></span>@endif
                    <h3 class="h5 mb-1">{{ $b->title }}</h3>
                    <div class="small text-muted mb-2">{{ collect([$b->subtitle, $b->value])->filter()->implode(' · ') }}</div>
                    @if ($b->description)<p style="-webkit-line-clamp: unset">{{ $b->description }}</p>@endif
                    @if ($b->url)<a href="{{ $b->url }}" target="_blank" rel="noopener" class="small fw-semibold">Read more <i class="bi bi-arrow-up-right"></i></a>@endif
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif

@if (in_array('leader', $show) && ($items = $blocks->get('leader'))?->isNotEmpty())
<section class="section bg-warm">
    <div class="container">
        <x-section-head eyebrow="With the support of" title="Leaders who stand with us" center />
        <div class="grid grid-3" style="--min: 15rem">
            @foreach ($items as $b)
                <article class="program-card reveal text-center">
                    @if ($b->image)<img src="{{ upload_small_url($b->image) ?: upload_url($b->image) }}" alt="{{ $b->title }}" loading="lazy" class="rounded-circle mx-auto mb-3" style="width:96px;height:96px;object-fit:cover">@endif
                    <h3 class="h5 mb-0">@if ($b->url)<a href="{{ $b->url }}" target="_blank" rel="noopener" class="text-reset">{{ $b->title }}</a>@else{{ $b->title }}@endif</h3>
                    @if ($b->subtitle)<div class="small text-muted mb-2">{{ $b->subtitle }}</div>@endif
                    @if ($b->description)<p style="-webkit-line-clamp: unset">“{{ $b->description }}”</p>@endif
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif

@if (in_array('trust', $show) && ($items = $blocks->get('trust'))?->isNotEmpty())
<section class="section">
    <div class="container">
        <x-section-head eyebrow="Trust wall" title="Trusted by people and partners" center />
        <div class="grid grid-3" style="--min: 16rem">
            @foreach ($items as $b)
                <figure class="program-card reveal m-0">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        @if ($b->image)<img src="{{ upload_small_url($b->image) ?: upload_url($b->image) }}" alt="" loading="lazy" style="width:52px;height:52px;object-fit:contain;border-radius:10px;background:#fff">@else<span class="program-icon m-0"><i class="bi bi-patch-check"></i></span>@endif
                        <figcaption><strong class="d-block">@if ($b->url)<a href="{{ $b->url }}" target="_blank" rel="noopener" class="text-reset">{{ $b->title }}</a>@else{{ $b->title }}@endif</strong>@if ($b->subtitle)<span class="small text-muted">{{ $b->subtitle }}</span>@endif</figcaption>
                    </div>
                    @if ($b->description)<blockquote class="m-0" style="-webkit-line-clamp: unset">“{{ $b->description }}”</blockquote>@endif
                </figure>
            @endforeach
        </div>
    </div>
</section>
@endif

@if (in_array('embed', $show) && ($items = $blocks->get('embed')?->filter(fn ($b) => $embedSrc($b))))?->isNotEmpty())
<section class="section bg-surface">
    <div class="container">
        <x-section-head eyebrow="Follow us" title="From our social media" center />
        <div class="grid grid-3" style="--min: 18rem">
            @foreach ($items as $b)
                <div class="reveal">
                    <div class="ratio ratio-4x3 rounded-4 overflow-hidden bg-white"><iframe src="{{ $embedSrc($b) }}" title="{{ $b->title }}" loading="lazy" allow="encrypted-media; picture-in-picture" allowfullscreen style="border:0"></iframe></div>
                    @if ($b->subtitle)<div class="small text-muted mt-2">{{ $b->subtitle }}</div>@endif
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
