@extends('layouts.site')

@section('content')
<x-page-hero :title="$page->title" :subtitle="$page->subtitle" />

<section class="section">
    <div class="container">
        <div class="mx-auto" style="max-width: 50rem">
            <p class="small text-muted mb-4"><i class="bi bi-clock-history"></i> Last updated: {{ ($page->updated_at ?? now())->format('d F Y') }}</p>
            <div class="prose prose-wide">{!! $page->body !!}</div>
            <div class="notice mt-5">Questions about this policy? Write to <a href="mailto:{{ setting('email') }}">{{ setting('email') }}</a> or call <a href="tel:{{ phone_link(setting('phone')) }}">+91 {{ setting('phone') }}</a>.</div>
        </div>
    </div>
</section>
@endsection
