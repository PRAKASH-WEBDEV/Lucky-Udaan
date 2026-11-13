@props(['title' => null, 'text' => null])
<section class="section-sm">
    <div class="container">
        <div class="cta-band reveal">
            @if (setting('about_image'))
                <div class="cta-img"><x-img :path="setting('about_image')" alt="" sizes="100vw" /></div>
            @endif
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <span class="eyebrow text-white" style="--accent: #f3c46b">Make a difference today</span>
                    <h2 class="mb-3">{{ $title ?? setting('cta_heading', 'Your small contribution can create a big smile') }}</h2>
                    <p class="mb-0">{{ $text ?? setting('cta_text') }}</p>
                </div>
                <div class="col-lg-4 d-flex flex-wrap gap-2 justify-content-lg-end">
                    <a href="{{ route('donate') }}" class="btn btn-accent btn-lg"><i class="bi bi-heart-fill"></i> Donate now</a>
                    <a href="{{ route('volunteer') }}" class="btn btn-light-ghost btn-lg">Volunteer</a>
                </div>
            </div>
        </div>
    </div>
</section>
