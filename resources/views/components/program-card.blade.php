@props(['program', 'level' => 'h3'])
<article class="program-card reveal">
    <span class="program-icon" aria-hidden="true"><i class="bi bi-{{ $program->icon ?: 'heart' }}"></i></span>
    <{{ $level }}><a href="{{ route('programs.show', $program->slug) }}">{{ $program->title }}</a></{{ $level }}>
    <p>{{ $program->excerpt }}</p>
    <span class="link-arrow">Learn more <i class="bi bi-arrow-right"></i></span>
</article>
