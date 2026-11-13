@extends('layouts.site')

@section('content')
@php seo()->set('Too many requests')->noindex = true; @endphp
<section class="section">
    <div class="container text-center" style="max-width: 40rem">
        <p class="display-4 fw-bold text-brand mb-2"><i class="bi bi-hourglass-split"></i></p>
        <h1 class="h2">Please wait a moment</h1>
        <p class="lead">We received several submissions from your connection in a short time. Please wait a minute and try again.</p>
        <a href="{{ url()->previous() }}" class="btn btn-primary mt-3">Go back</a>
    </div>
</section>
@endsection
