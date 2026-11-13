@extends('layouts.site')
@section('handlesFlash', true)

@php
    $hasBank = setting('bank_account_number') && setting('bank_ifsc');
    $hasUpi = (bool) setting('upi_id');
@endphp

@section('content')
<x-page-hero :title="$page->title" :subtitle="$page->subtitle" :image="$page->image" />

<section class="section">
    <div class="container">
        <div class="row g-5">
            {{-- Payment details --}}
            <div class="col-lg-5 order-lg-2">
                <div class="position-sticky d-grid gap-4" style="top: calc(var(--header-h) + 1.5rem)">
                    <div class="bank-card reveal">
                        <h2 class="h4 text-white mb-1">How to give</h2>
                        <p class="text-white-50 small mb-4">Pay by UPI or bank transfer, then fill the form so we can send your receipt.</p>
                        @if ($hasUpi || setting('upi_qr'))
                            <div class="d-flex gap-3 align-items-center mb-4">
                                @if (setting('upi_qr'))
                                    <img src="{{ upload_url(setting('upi_qr')) }}" alt="UPI QR code for {{ setting('site_name') }}" width="120" height="120" class="rounded-3 bg-white p-1" style="width:7.5rem;height:auto">
                                @endif
                                <div>
                                    <div class="text-white-50 small">UPI ID</div>
                                    <div class="fw-bold text-break">{{ setting('upi_id') ?: 'Scan the QR code' }}
                                        @if ($hasUpi)<button type="button" class="copy" data-copy="{{ setting('upi_id') }}">Copy</button>@endif
                                    </div>
                                    <div class="small text-white-50 mt-1">GPay · PhonePe · Paytm · BHIM</div>
                                </div>
                            </div>
                        @endif
                        @if ($hasBank)
                            <dl>
                                <dt>Account name</dt><dd>{{ setting('bank_account_name') }}</dd>
                                <dt>Account no.</dt><dd>{{ setting('bank_account_number') }} <button type="button" class="copy" data-copy="{{ setting('bank_account_number') }}">Copy</button></dd>
                                <dt>IFSC</dt><dd>{{ setting('bank_ifsc') }} <button type="button" class="copy" data-copy="{{ setting('bank_ifsc') }}">Copy</button></dd>
                                @if (setting('bank_name'))<dt>Bank</dt><dd>{{ setting('bank_name') }}{{ setting('bank_branch') ? ', '.setting('bank_branch') : '' }}</dd>@endif
                            </dl>
                        @endif
                        @if (! $hasBank && ! $hasUpi && ! setting('upi_qr'))
                            <p class="mb-0">Bank and UPI details will be published here shortly. Meanwhile, please call <a class="text-white fw-bold" href="tel:{{ phone_link(setting('phone')) }}">+91 {{ setting('phone') }}</a> to donate.</p>
                        @endif
                    </div>
                    <div class="form-card reveal">
                        <h2 class="h5 mb-3"><i class="bi bi-shield-check text-brand me-1"></i> Your trust matters</h2>
                        <ul class="check-list small">
                            <li>{{ setting('legal_name') ?: 'Registered not-for-profit organisation' }}</li>
                            <li>{{ setting('reg_80g') ? '80G registered — eligible donations qualify for tax deduction (No. '.setting('reg_80g').')' : '80G tax-exemption details will be shared once registration is complete' }}</li>
                            <li>Every donation is recorded and acknowledged with a receipt</li>
                            <li>Read our <a href="{{ route('refund') }}">refund policy</a> and <a href="{{ route('transparency') }}">transparency</a> page</li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Pledge form --}}
            <div class="col-lg-7 order-lg-1">
                @if ($page->body)<div class="prose mb-4 reveal">{!! $page->body !!}</div>@endif
                <div class="form-card reveal">
                    <h2 class="h4 mb-1">Donation details</h2>
                    <p class="text-muted mb-4">No online payment is taken on this page. Your details help us match your transfer and issue a receipt.</p>
                    @include('site.partials.form-alerts')
                    <form method="post" action="{{ route('donate.submit') }}" id="donateForm" data-busy novalidate>
                        @csrf
                        <x-honeypot />

                        <fieldset class="mb-3">
                            <legend class="form-label fs-6">Choose an amount <span class="req">*</span></legend>
                            <div class="amount-chips mb-3">
                                @foreach ($amounts as $a)
                                    <label class="choice"><input type="radio" name="preset" value="{{ $a }}" @checked((int) old('amount', 1000) === $a)><span>{{ inr($a) }}</span></label>
                                @endforeach
                            </div>
                            <label for="amount" class="visually-hidden">Amount in rupees</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" min="{{ setting('donation_min', 100) }}" step="1" name="amount" id="amount" value="{{ old('amount', 1000) }}" class="form-control @error('amount') is-invalid @enderror" required inputmode="numeric" aria-describedby="amount-help">
                                @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-text" id="amount-help">Or enter any amount (minimum ₹{{ setting('donation_min', 100) }}).</div>
                        </fieldset>

                        <div class="row">
                            @if ($campaigns->isNotEmpty())
                                <x-field class="col-md-6" name="campaign_id" type="select" label="Donate towards" :options="$campaigns->pluck('title', 'id')->all()" :value="$selectedCampaign" empty="Where it’s needed most" />
                            @endif
                            <div class="col-md-6 mb-3">
                                <span class="form-label d-block">Payment method <span class="req">*</span></span>
                                <div class="d-flex gap-2">
                                    <label class="choice flex-fill"><input type="radio" name="method" value="upi" @checked(old('method', 'upi') === 'upi')><span><i class="bi bi-qr-code"></i> UPI</span></label>
                                    <label class="choice flex-fill"><input type="radio" name="method" value="bank_transfer" @checked(old('method') === 'bank_transfer')><span><i class="bi bi-bank"></i> Bank</span></label>
                                </div>
                            </div>
                            <x-field class="col-md-6" name="name" label="Full name" required autocomplete="name" />
                            <x-field class="col-md-6" name="phone" type="tel" label="Mobile number" required autocomplete="tel" inputmode="tel" />
                            <x-field class="col-md-6" name="email" type="email" label="Email (for receipt)" required autocomplete="email" />
                            <x-field class="col-md-6" name="transaction_ref" label="UPI / UTR reference (if paid)" help="Helps us confirm your payment faster." />
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" role="switch" name="wants_80g" value="1" id="wants_80g" @checked(old('wants_80g'))>
                            <label class="form-check-label" for="wants_80g">I would like an 80G tax-exemption receipt</label>
                        </div>
                        <div id="taxFields" class="row" hidden>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="pan">PAN <span class="req">*</span></label>
                                <input type="text" name="pan" id="pan" value="{{ old('pan') }}" class="form-control text-uppercase @error('pan') is-invalid @enderror" maxlength="10" data-req80g placeholder="ABCDE1234F">
                                @error('pan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <x-field class="col-md-6" name="city" label="City" autocomplete="address-level2" />
                            <div class="col-12 mb-3">
                                <label class="form-label" for="address">Address <span class="req">*</span></label>
                                <textarea name="address" id="address" rows="2" class="form-control @error('address') is-invalid @enderror" data-req80g autocomplete="street-address">{{ old('address') }}</textarea>
                                @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <x-field name="message" type="textarea" rows="2" label="Message (optional)" placeholder="In memory of… / Dedicated to…" />

                        <div class="form-check mb-4">
                            <input class="form-check-input @error('consent') is-invalid @enderror" type="checkbox" name="consent" value="1" id="consent" required @checked(old('consent'))>
                            <label class="form-check-label small" for="consent">I am donating voluntarily and agree to the <a href="{{ route('terms') }}">terms</a>, <a href="{{ route('privacy') }}">privacy</a> and <a href="{{ route('refund') }}">refund</a> policies.</label>
                            @error('consent')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <button class="btn btn-accent btn-lg w-100" type="submit"><i class="bi bi-heart-fill"></i> Submit donation details</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
