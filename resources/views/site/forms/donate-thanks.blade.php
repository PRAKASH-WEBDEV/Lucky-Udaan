@extends('layouts.site')

@section('content')
<section class="section">
    <div class="container">
        <div class="form-card mx-auto text-center" style="max-width: 44rem">
            <span class="program-icon mx-auto mb-3" style="width:4.5rem;height:4.5rem;border-radius:50%;display:grid;place-items:center;font-size:2rem;background:rgba(var(--brand-rgb),.12);color:var(--brand)"><i class="bi bi-heart-fill"></i></span>
            <h1 class="h2">Thank you, {{ $donation->donor?->name }}!</h1>
            <p class="lead">We have recorded your pledge of <strong>{{ inr($donation->amount) }}</strong>@if ($donation->campaign) towards <strong>{{ $donation->campaign->title }}</strong>@endif.</p>
            <div class="notice text-start my-4">
                <strong>Your reference number: {{ $donation->receipt_no }}</strong><br>
                @if ($donation->transaction_ref)
                    We will verify your payment (ref. {{ $donation->transaction_ref }}) and email your receipt to {{ $donation->donor?->email }}.
                @else
                    Please complete your payment by {{ $donation->method === 'upi' ? 'UPI' : 'bank transfer' }} using the details below and mention this reference number in the remarks. Your receipt will be emailed once the payment is confirmed.
                @endif
            </div>
            @if (! $donation->transaction_ref)
                <div class="bank-card text-start mb-4">
                    <dl>
                        @if (setting('upi_id'))<dt>UPI ID</dt><dd>{{ setting('upi_id') }} <button type="button" class="copy" data-copy="{{ setting('upi_id') }}">Copy</button></dd>@endif
                        @if (setting('bank_account_number'))
                            <dt>Account name</dt><dd>{{ setting('bank_account_name') }}</dd>
                            <dt>Account no.</dt><dd>{{ setting('bank_account_number') }}</dd>
                            <dt>IFSC</dt><dd>{{ setting('bank_ifsc') }}</dd>
                        @endif
                        @if (! setting('upi_id') && ! setting('bank_account_number'))
                            <dt>Call us</dt><dd>+91 {{ setting('phone') }}</dd>
                        @endif
                    </dl>
                    @if (setting('upi_qr'))<img src="{{ upload_url(setting('upi_qr')) }}" alt="UPI QR code" class="bg-white rounded-3 p-1 mt-3" style="width:9rem">@endif
                </div>
            @endif
            <div class="d-flex flex-wrap gap-2 justify-content-center">
                <a href="{{ route('home') }}" class="btn btn-primary">Back to home</a>
                <a class="btn btn-outline-primary" target="_blank" rel="noopener" href="{{ whatsapp_link('', 'I just supported '.setting('site_name').'. Join me: '.route('donate')) }}"><i class="bi bi-whatsapp"></i> Tell your friends</a>
            </div>
        </div>
    </div>
</section>
@endsection
