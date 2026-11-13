@extends('layouts.site')
@section('handlesFlash', true)

@section('content')
<x-page-hero :title="$page->title" :subtitle="$page->subtitle" :image="$page->image" />

<section class="section">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-5">
                <div class="prose mb-4 reveal">{!! $page->body !!}</div>
                <div class="d-grid gap-4">
                    @foreach ([
                        ['calendar-heart', 'Flexible commitment', 'Weekends, evenings, events only or remote — you choose.'],
                        ['award', 'Certificate of volunteering', 'Recognition for your time and contribution.'],
                        ['people', 'A community that cares', 'Meet people who want to make Gonda a better place.'],
                    ] as [$i, $t, $d])
                        <div class="feature-tile reveal"><span class="ic"><i class="bi bi-{{ $i }}"></i></span><div><h3>{{ $t }}</h3><p>{{ $d }}</p></div></div>
                    @endforeach
                </div>
            </div>
            <div class="col-lg-7">
                <div class="form-card reveal">
                    <h2 class="h4 mb-1">Volunteer registration</h2>
                    <p class="text-muted mb-4">Takes about two minutes. Fields marked <span class="req">*</span> are required.</p>
                    @include('site.partials.form-alerts')
                    <form method="post" action="{{ route('volunteer.submit') }}" data-busy novalidate>
                        @csrf
                        <x-honeypot />
                        <div class="row">
                            <x-field class="col-md-6" name="name" label="Full name" required autocomplete="name" />
                            <x-field class="col-md-6" name="email" type="email" label="Email" required autocomplete="email" />
                            <x-field class="col-md-6" name="phone" type="tel" label="Mobile number" required autocomplete="tel" inputmode="tel" />
                            <x-field class="col-md-6" name="city" label="City / village" required autocomplete="address-level2" />
                            <x-field class="col-md-6" name="occupation" label="Occupation / studying" />
                            <x-field class="col-md-6" name="availability" type="select" label="Availability" required :options="\App\Support\Options::availability()" />
                        </div>
                        <fieldset class="mb-3">
                            <legend class="form-label fs-6">Areas you’d like to help with <span class="req">*</span></legend>
                            <div class="choice-grid">
                                @foreach (\App\Support\Options::interests() as $k => $v)
                                    <label class="choice"><input type="checkbox" name="interests[]" value="{{ $k }}" @checked(in_array($k, old('interests', [])))><span>{{ $v }}</span></label>
                                @endforeach
                            </div>
                            @error('interests')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </fieldset>
                        <x-field name="skills" type="textarea" rows="2" label="Skills you can offer" placeholder="e.g. teaching, first aid, photography, accounting" />
                        <x-field name="message" type="textarea" rows="3" label="Anything else you’d like us to know?" />
                        <div class="form-check mb-4">
                            <input class="form-check-input @error('consent') is-invalid @enderror" type="checkbox" name="consent" value="1" id="consent" required @checked(old('consent'))>
                            <label class="form-check-label" for="consent">I agree to be contacted by {{ setting('site_name') }} about volunteering opportunities.</label>
                            @error('consent')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <button class="btn btn-primary btn-lg" type="submit"><i class="bi bi-hand-thumbs-up"></i> Join as a volunteer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
