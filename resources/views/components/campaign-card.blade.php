@props(['campaign'])
<article class="card-x reveal">
    <div class="card-media">
        <x-img :path="$campaign->image" :alt="$campaign->title" />
        @if ($campaign->end_date && $campaign->end_date->isFuture())
            <span class="pill pill-light card-tag"><i class="bi bi-clock"></i> {{ max(1, (int) now()->diffInDays($campaign->end_date)) }} days left</span>
        @endif
    </div>
    <div class="card-body-x">
        @if ($campaign->program)<span class="pill align-self-start">{{ $campaign->program->title }}</span>@endif
        <h3 class="card-title-x"><a href="{{ route('campaigns.show', $campaign->slug) }}">{{ $campaign->title }}</a></h3>
        <p class="card-text-x">{{ $campaign->excerpt }}</p>
        @if ($campaign->goal_amount > 0)
            <div class="card-foot">
                <div class="progress-x mb-2" role="progressbar" aria-label="Funds raised" aria-valuenow="{{ $campaign->progress() }}" aria-valuemin="0" aria-valuemax="100"><span style="width: {{ $campaign->progress() }}%"></span></div>
                <div class="raise-row"><span><strong>{{ inr($campaign->raised_amount) }}</strong> raised</span><span>Goal {{ inr($campaign->goal_amount) }}</span></div>
            </div>
        @endif
    </div>
</article>
