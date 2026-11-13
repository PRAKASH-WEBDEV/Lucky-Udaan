@extends('layouts.site')
@section('handlesFlash', true)

@section('content')
<x-page-hero :title="$page->title" :subtitle="$page->subtitle" :image="$page->image" />

<section class="section">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-5">
                <x-section-head eyebrow="Reach us" title="We’d love to hear from you" :text="strip_tags($page->body) ?: null" class="mb-4" />
                <ul class="info-list reveal">
                    <li><span class="ic"><i class="bi bi-geo-alt"></i></span><div><strong>Visit</strong><address class="mb-0">{!! nl2br(e(full_address(true))) !!}</address></div></li>
                    <li><span class="ic"><i class="bi bi-telephone"></i></span><div><strong>Call / WhatsApp</strong><a href="tel:{{ phone_link(setting('phone')) }}">+91 {{ setting('phone') }}</a></div></li>
                    <li><span class="ic"><i class="bi bi-envelope"></i></span><div><strong>Email</strong><a href="mailto:{{ setting('email') }}">{{ setting('email') }}</a></div></li>
                    @if (setting('office_hours'))<li><span class="ic"><i class="bi bi-clock"></i></span><div><strong>Office hours</strong>{{ setting('office_hours') }}</div></li>@endif
                    @if (setting('contact_person'))<li><span class="ic"><i class="bi bi-person"></i></span><div><strong>Contact person</strong>{{ setting('contact_person') }}</div></li>@endif
                </ul>
                <div class="d-flex flex-wrap gap-2 mt-4">
                    @if (setting('whatsapp'))<a class="btn btn-primary" href="{{ whatsapp_link(setting('whatsapp')) }}" target="_blank" rel="noopener"><i class="bi bi-whatsapp"></i> WhatsApp us</a>@endif
                    @include('site.partials.social', ['class' => 'd-flex gap-2 social-inline'])
                </div>
            </div>
            <div class="col-lg-7">
                <div class="form-card reveal">
                    <h2 class="h4 mb-1">Send us a message</h2>
                    <p class="text-muted mb-4">Fields marked <span class="req">*</span> are required.</p>
                    @include('site.partials.form-alerts')
                    <form method="post" action="{{ route('contact.submit') }}" data-busy novalidate>
                        @csrf
                        <x-honeypot />
                        <div class="row">
                            <x-field class="col-md-6" name="name" label="Your name" required autocomplete="name" />
                            <x-field class="col-md-6" name="email" type="email" label="Email" required autocomplete="email" />
                            <x-field class="col-md-6" name="phone" type="tel" label="Phone" autocomplete="tel" inputmode="tel" />
                            <x-field class="col-md-6" name="subject" label="Subject" required />
                            <x-field class="col-12" name="message" type="textarea" label="Message" rows="5" required />
                        </div>
                        <button class="btn btn-primary btn-lg" type="submit"><i class="bi bi-send"></i> Send message</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

@if (setting('map_embed'))
<section class="pb-5">
    <div class="container">
        <div class="map-frame reveal">
            <iframe src="{{ setting('map_embed') }}" title="Map: {{ setting('site_name') }}, {{ setting('city') }}" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
        </div>
    </div>
</section>
@endif
@endsection
