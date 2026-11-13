@extends('layouts.site')
@section('handlesFlash', true)

@section('content')
<x-page-hero :title="$page->title" :subtitle="$page->subtitle" :image="$page->image" />

<section class="section">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-5">
                <div class="prose mb-4 reveal">{!! $page->body !!}</div>
                <div class="d-grid gap-3">
                    @foreach ([
                        ['General Member', 'person', 'For anyone who believes in our mission and wants a voice in our work.'],
                        ['Student Member', 'mortarboard', 'For school and college students who want to learn by serving.'],
                        ['Life Member', 'infinity', 'A lifelong association with the foundation and its programmes.'],
                        ['Patron', 'star', 'For well-wishers who support us in a significant way.'],
                    ] as [$t, $i, $d])
                        <div class="feature-tile reveal"><span class="ic"><i class="bi bi-{{ $i }}"></i></span><div><h3>{{ $t }}</h3><p>{{ $d }}</p></div></div>
                    @endforeach
                </div>
            </div>
            <div class="col-lg-7">
                <div class="form-card reveal">
                    <h2 class="h4 mb-1">Membership application</h2>
                    <p class="text-muted mb-4">Our team reviews every application and confirms membership by email.</p>
                    @include('site.partials.form-alerts')
                    <form method="post" action="{{ route('membership.submit') }}" data-busy novalidate>
                        @csrf
                        <x-honeypot />
                        <div class="row">
                            <x-field class="col-md-6" name="name" label="Full name" required autocomplete="name" />
                            <x-field class="col-md-6" name="email" type="email" label="Email" required autocomplete="email" />
                            <x-field class="col-md-6" name="phone" type="tel" label="Mobile number" required autocomplete="tel" inputmode="tel" />
                            <x-field class="col-md-6" name="membership_type" type="select" label="Membership type" required :options="\App\Models\Member::TYPES" />
                            <x-field class="col-md-6" name="gender" type="select" label="Gender" :options="['Female' => 'Female', 'Male' => 'Male', 'Other' => 'Other']" empty="Prefer not to say" />
                            <x-field class="col-md-6" name="dob" type="date" label="Date of birth" autocomplete="bday" />
                            <x-field class="col-md-6" name="occupation" label="Occupation" />
                            <x-field class="col-md-6" name="city" label="City" required autocomplete="address-level2" />
                            <x-field class="col-12" name="address" type="textarea" rows="2" label="Full address" required autocomplete="street-address" />
                            <x-field class="col-12" name="message" type="textarea" rows="3" label="Why would you like to join?" />
                        </div>
                        <div class="form-check mb-4">
                            <input class="form-check-input @error('consent') is-invalid @enderror" type="checkbox" name="consent" value="1" id="consent" required @checked(old('consent'))>
                            <label class="form-check-label" for="consent">I support the objectives of {{ setting('site_name') }} and agree to the <a href="{{ route('terms') }}">terms</a> and <a href="{{ route('privacy') }}">privacy policy</a>.</label>
                            @error('consent')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <button class="btn btn-primary btn-lg" type="submit"><i class="bi bi-person-check"></i> Apply for membership</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
