@extends('layouts.site')
@section('handlesFlash', true)

@section('content')
<x-page-hero :title="$page->title" :subtitle="$page->subtitle" :image="$page->image" />

<section class="section">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-6">
                <div class="prose mb-4 reveal">{!! $page->body !!}</div>
                <div class="grid grid-2" style="--min: 14rem">
                    @foreach ([
                        ['building', 'CSR partnerships', 'Fund measurable projects aligned with Schedule VII of the Companies Act.'],
                        ['bank', 'Government bodies', 'Implement schemes and awareness drives at the grassroots.'],
                        ['mortarboard', 'Educational institutions', 'Internships, NSS/NCC collaborations and research.'],
                        ['people', 'NGOs & trusts', 'Join hands for larger reach and shared learning.'],
                        ['briefcase', 'Employee volunteering', 'Engage your team in meaningful days of service.'],
                        ['box-seam', 'In-kind support', 'Books, medicines, sewing machines, food and more.'],
                    ] as [$i, $t, $d])
                        <div class="feature-tile reveal"><span class="ic"><i class="bi bi-{{ $i }}"></i></span><div><h3>{{ $t }}</h3><p>{{ $d }}</p></div></div>
                    @endforeach
                </div>
            </div>
            <div class="col-lg-6">
                <div class="form-card reveal">
                    <h2 class="h4 mb-1">Start a conversation</h2>
                    <p class="text-muted mb-4">Tell us a little about your organisation and what you have in mind.</p>
                    @include('site.partials.form-alerts')
                    <form method="post" action="{{ route('partnership.submit') }}" data-busy novalidate>
                        @csrf
                        <x-honeypot />
                        <div class="row">
                            <x-field class="col-md-6" name="organisation" label="Organisation name" required autocomplete="organization" />
                            <x-field class="col-md-6" name="partnership_type" type="select" label="Type of partnership" required :options="\App\Support\Options::partnershipTypes()" />
                            <x-field class="col-md-6" name="name" label="Your name" required autocomplete="name" />
                            <x-field class="col-md-6" name="designation" label="Designation" autocomplete="organization-title" />
                            <x-field class="col-md-6" name="email" type="email" label="Work email" required autocomplete="email" />
                            <x-field class="col-md-6" name="phone" type="tel" label="Phone" required autocomplete="tel" inputmode="tel" />
                            <x-field class="col-12" name="website_url" type="url" label="Website" placeholder="https://" />
                            <x-field class="col-12" name="message" type="textarea" rows="4" label="How would you like to partner?" required />
                        </div>
                        <button class="btn btn-primary btn-lg" type="submit"><i class="bi bi-send"></i> Send enquiry</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
