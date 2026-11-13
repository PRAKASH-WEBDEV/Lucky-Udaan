@extends('layouts.site')

@section('content')
<x-page-hero :title="$page->title" :subtitle="$page->subtitle" :image="$page->image" />

<section class="section">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-8">
                @if ($page->body)<div class="prose mb-4">{!! $page->body !!}</div>@endif
                @forelse ($groups as $category => $faqs)
                    <h2 class="h4 mb-3 @if (! $loop->first) mt-5 @endif">{{ $category }}</h2>
                    <div class="accordion" id="faq-{{ $loop->index }}">
                        @foreach ($faqs as $faq)
                            <div class="accordion-item">
                                <h3 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#q{{ $faq->id }}" aria-expanded="false" aria-controls="q{{ $faq->id }}">{{ $faq->question }}</button>
                                </h3>
                                <div id="q{{ $faq->id }}" class="accordion-collapse collapse" data-bs-parent="#faq-{{ $loop->parent->index }}">
                                    <div class="accordion-body text-muted">{!! nl2br(e($faq->answer)) !!}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @empty
                    <x-empty icon="question-circle" title="FAQs coming soon" />
                @endforelse
            </div>
            <aside class="col-lg-4">
                <div class="bank-card position-sticky" style="top: calc(var(--header-h) + 1.5rem)">
                    <h2 class="h4 text-white">Still have a question?</h2>
                    <p class="text-white-50">We’re happy to help. Reach out and we’ll respond within two working days.</p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('contact') }}" class="btn btn-accent">Contact us</a>
                        <a href="tel:{{ phone_link(setting('phone')) }}" class="btn btn-light-ghost"><i class="bi bi-telephone"></i> +91 {{ setting('phone') }}</a>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</section>
@endsection
