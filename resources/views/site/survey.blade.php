@extends('layouts.site')

@section('content')
<x-page-hero :title="$survey->title" :subtitle="$survey->description ? \Illuminate\Support\Str::limit($survey->description, 180) : 'Your views help us serve the community better.'" />

<section class="section">
    <div class="container" style="max-width: 760px">
        @include('site.partials.form-alerts')

        @if (! $survey->isOpen())
            <div class="alert alert-info">This survey closed on {{ $survey->ends_on?->format('d F Y') }}. Thank you for your interest.</div>
        @elseif (! session('success'))
            @if ($survey->description)<p class="lead">{{ $survey->description }}</p>@endif
            <form method="post" action="{{ route('surveys.submit', $survey) }}" class="card p-4" novalidate>
                @csrf
                <x-honeypot />
                @error('answers')<div class="alert alert-danger">{{ $message }}</div>@enderror
                @foreach ($questions as $i => $q)
                    <div class="mb-4">
                        <label class="form-label fw-semibold" for="q{{ $i }}">{{ $loop->iteration }}. {{ $q }}</label>
                        <textarea class="form-control" id="q{{ $i }}" name="answers[{{ $i }}]" rows="2" maxlength="2000">{{ old('answers.'.$i) }}</textarea>
                    </div>
                @endforeach
                <div class="row g-3 mb-3">
                    <div class="col-sm-6"><label class="form-label" for="s-name">Your name <span class="text-muted small">(optional)</span></label><input class="form-control" id="s-name" name="name" value="{{ old('name') }}" maxlength="120"></div>
                    <div class="col-sm-6"><label class="form-label" for="s-phone">Phone <span class="text-muted small">(optional)</span></label><input class="form-control" id="s-phone" name="phone" value="{{ old('phone') }}" maxlength="20" inputmode="tel"></div>
                </div>
                <button class="btn btn-primary" type="submit"><i class="bi bi-send"></i> Submit response</button>
            </form>
        @endif
    </div>
</section>
@endsection
