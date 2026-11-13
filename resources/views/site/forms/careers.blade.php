@extends('layouts.site')
@section('handlesFlash', true)

@php $isIntern = $kind === 'internship'; @endphp

@section('content')
<x-page-hero :title="$page->title" :subtitle="$page->subtitle" :image="$page->image" />

<section class="section">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-7">
                <div class="prose mb-5 reveal">{!! $page->body !!}</div>

                <h2 class="h3 mb-4">{{ $isIntern ? 'Open internships' : 'Current openings' }}</h2>
                @forelse ($openings as $o)
                    <article class="form-card mb-3 reveal">
                        <div class="d-flex flex-wrap justify-content-between gap-2 mb-2">
                            <h3 class="h5 mb-0">{{ $o->title }}</h3>
                            @if ($o->deadline)<span class="pill pill-accent"><i class="bi bi-clock"></i> Apply by {{ $o->deadline->format('d M Y') }}</span>@endif
                        </div>
                        <div class="card-meta d-flex flex-wrap gap-3 small text-muted mb-3">
                            @if ($o->location)<span><i class="bi bi-geo-alt text-brand"></i> {{ $o->location }}</span>@endif
                            @if ($o->employment_type)<span><i class="bi bi-briefcase text-brand"></i> {{ $o->employment_type }}</span>@endif
                            @if ($o->duration)<span><i class="bi bi-hourglass text-brand"></i> {{ $o->duration }}</span>@endif
                        </div>
                        <p class="text-muted">{{ $o->summary }}</p>
                        @if ($o->description)
                            <details>
                                <summary class="fw-semibold text-brand" style="cursor:pointer">Responsibilities & requirements</summary>
                                <div class="prose prose-wide mt-3 small">{!! $o->description !!}</div>
                            </details>
                        @endif
                        <a href="#apply" class="btn btn-sm btn-outline-primary mt-3" onclick="document.getElementById('f-opening_id').value='{{ $o->id }}'">Apply for this role</a>
                    </article>
                @empty
                    <x-empty icon="briefcase" title="No open positions right now" text="You can still send us your profile — we’ll reach out when a suitable role opens." />
                @endforelse
            </div>
            <div class="col-lg-5">
                <div class="form-card position-sticky reveal" id="apply" style="top: calc(var(--header-h) + 1.5rem)">
                    <h2 class="h4 mb-1">{{ $isIntern ? 'Apply for an internship' : 'Send your application' }}</h2>
                    <p class="text-muted mb-4 small">CV in PDF or Word, up to 4 MB. We only use your details for recruitment.</p>
                    @include('site.partials.form-alerts')
                    <form method="post" action="{{ route($isIntern ? 'internships.submit' : 'careers.submit') }}" enctype="multipart/form-data" data-busy novalidate>
                        @csrf
                        <x-honeypot />
                        <x-field name="opening_id" type="select" label="Position" :options="$openings->pluck('title', 'id')->all()" :empty="$isIntern ? 'General internship' : 'General application'" />
                        <x-field name="name" label="Full name" required autocomplete="name" />
                        <x-field name="email" type="email" label="Email" required autocomplete="email" />
                        <x-field name="phone" type="tel" label="Mobile number" required autocomplete="tel" inputmode="tel" />
                        <x-field name="qualification" :label="$isIntern ? 'Course & year' : 'Highest qualification'" required />
                        <x-field name="institution" :label="$isIntern ? 'College / university' : 'Current / last organisation'" />
                        <x-field name="portfolio" type="url" label="LinkedIn or portfolio link" placeholder="https://" />
                        <x-field name="message" type="textarea" rows="4" label="Why do you want to work with us?" required />
                        <x-field name="cv" type="file" label="Upload CV" accept=".pdf,.doc,.docx" />
                        <div class="form-check mb-4">
                            <input class="form-check-input @error('consent') is-invalid @enderror" type="checkbox" name="consent" value="1" id="consent" required @checked(old('consent'))>
                            <label class="form-check-label small" for="consent">I confirm the information is correct and agree to the <a href="{{ route('privacy') }}">privacy policy</a>.</label>
                            @error('consent')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <button class="btn btn-primary btn-lg w-100" type="submit"><i class="bi bi-send"></i> Submit application</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
