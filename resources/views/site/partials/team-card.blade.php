<article class="team-card reveal">
    <div class="team-photo">
        @if ($member->photo)
            <x-img :path="$member->photo" :alt="$member->name" sizes="(min-width: 1200px) 25vw, (min-width: 576px) 50vw, 100vw" />
        @else
            <span class="initials" aria-hidden="true">{{ $member->initials() }}</span>
        @endif
    </div>
    <div class="team-body">
        <h3>{{ $member->name }}</h3>
        @if ($member->designation)<div class="role">{{ $member->designation }}</div>@endif
        @if ($member->bio)<p class="bio">{{ $member->bio }}</p>@endif
        @if ($member->linkedin || $member->facebook || $member->email)
            <div class="socials">
                @if ($member->linkedin)<a href="{{ $member->linkedin }}" target="_blank" rel="noopener" aria-label="{{ $member->name }} on LinkedIn"><i class="bi bi-linkedin"></i></a>@endif
                @if ($member->facebook)<a href="{{ $member->facebook }}" target="_blank" rel="noopener" aria-label="{{ $member->name }} on Facebook"><i class="bi bi-facebook"></i></a>@endif
                @if ($member->email)<a href="mailto:{{ $member->email }}" aria-label="Email {{ $member->name }}"><i class="bi bi-envelope"></i></a>@endif
            </div>
        @endif
    </div>
</article>
