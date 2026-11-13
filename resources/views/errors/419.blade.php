@extends('layouts.site')

@section('content')
@php seo()->set('Page expired')->noindex = true; @endphp
<section class="section">
    <div class="container text-center" style="max-width: 40rem">
        <p class="display-4 fw-bold text-brand mb-2"><i class="bi bi-clock-history"></i></p>
        <h1 class="h2">This page has expired</h1>
        <p class="lead">For your security, forms expire after a while. Please go back, refresh the page and submit again.</p>
        <a href="{{ url()->previous() }}" class="btn btn-primary mt-3">Go back</a>
    </div>
</section>
@endsection
