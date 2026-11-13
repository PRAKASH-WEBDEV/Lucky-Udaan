@props(['title', 'subtitle' => null, 'image' => null])
<section class="page-hero">
    @if ($image)
        <div class="page-hero-img"><x-img :path="$image" alt="" eager sizes="100vw" /></div>
    @endif
    <div class="container">
        <nav aria-label="Breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="bi bi-house-door"></i> Home</a></li>
                @foreach (seo()->breadcrumbs as [$name, $link])
                    @if ($link)
                        <li class="breadcrumb-item"><a href="{{ $link }}">{{ $name }}</a></li>
                    @else
                        <li class="breadcrumb-item active" aria-current="page">{{ \Illuminate\Support\Str::limit($name, 60) }}</li>
                    @endif
                @endforeach
            </ol>
        </nav>
        <h1>{{ $title }}</h1>
        @if ($subtitle)
            <p class="page-hero-sub">{{ $subtitle }}</p>
        @endif
        {{ $slot }}
    </div>
</section>
