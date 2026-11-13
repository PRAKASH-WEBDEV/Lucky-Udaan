@extends('layouts.site')

@section('content')
@php seo()->set('Page not found')->noindex = true; @endphp
<section class="section">
    <div class="container text-center" style="max-width: 40rem">
        <p class="display-1 fw-bold text-brand mb-0">404</p>
        <h1 class="h2">We couldn’t find that page</h1>
        <p class="lead">It may have moved or no longer exists. Here are some places to start:</p>
        <div class="d-flex flex-wrap gap-2 justify-content-center mt-4">
            <a href="{{ route('home') }}" class="btn btn-primary">Home</a>
            <a href="{{ route('programs.index') }}" class="btn btn-outline-primary">Programs</a>
            <a href="{{ route('donate') }}" class="btn btn-outline-primary">Donate</a>
            <a href="{{ route('contact') }}" class="btn btn-outline-primary">Contact</a>
        </div>
    </div>
</section>
@endsection
