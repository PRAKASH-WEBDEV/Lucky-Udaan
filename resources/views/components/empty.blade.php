@props(['icon' => 'inbox', 'title' => 'Nothing here yet', 'text' => null])
<div class="empty-state">
    <i class="bi bi-{{ $icon }}" aria-hidden="true"></i>
    <p class="h5 text-dark mb-1">{{ $title }}</p>
    @if ($text)<p class="mb-0">{{ $text }}</p>@endif
    {{ $slot }}
</div>
