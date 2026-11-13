@props(['eyebrow' => null, 'title', 'text' => null, 'center' => false, 'level' => 'h2'])
<div {{ $attributes->class(['section-head reveal', 'text-center' => $center]) }}>
    @if ($eyebrow)<span class="eyebrow">{{ $eyebrow }}</span>@endif
    <{{ $level }} class="h2 mb-3">{{ $title }}</{{ $level }}>
    @if ($text)<p class="lead mb-0">{{ $text }}</p>@endif
    {{ $slot }}
</div>
