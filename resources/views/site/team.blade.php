@extends('layouts.site')

@section('content')
<x-page-hero :title="$page->title" :subtitle="$page->subtitle" :image="$page->image" />

<section class="section">
    <div class="container">
        @if ($page->body)<div class="prose mx-auto text-center mb-5">{!! $page->body !!}</div>@endif
        @forelse ($groups as $group => $members)
            <div @class(['mb-5' => ! $loop->last])>
                <h2 class="h3 mb-4 reveal">{{ \App\Models\TeamMember::GROUPS[$group] ?? ucfirst($group) }}</h2>
                <div class="grid">
                    @foreach ($members as $member)
                        @include('site.partials.team-card', ['member' => $member])
                    @endforeach
                </div>
            </div>
        @empty
            <x-empty icon="people" title="Team profiles coming soon" />
        @endforelse
    </div>
</section>

<section class="section-sm bg-warm">
    <div class="container text-center">
        <h2 class="h3">Want to work with us?</h2>
        <p class="lead mx-auto" style="max-width: 40rem">We are always looking for passionate volunteers, interns and professionals.</p>
        <div class="d-flex flex-wrap gap-2 justify-content-center">
            <a href="{{ route('volunteer') }}" class="btn btn-primary">Volunteer</a>
            <a href="{{ route('careers') }}" class="btn btn-outline-primary">Careers</a>
            <a href="{{ route('internships') }}" class="btn btn-outline-primary">Internships</a>
        </div>
    </div>
</section>
@endsection
