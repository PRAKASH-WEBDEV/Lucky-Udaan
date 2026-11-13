@extends('layouts.site')

@section('content')
<x-page-hero :title="$page->title" :subtitle="$page->subtitle" :image="$page->image" />

<section class="section">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-7">
                <div class="prose reveal">{!! $page->body !!}</div>
            </div>
            <aside class="col-lg-5">
                <div class="form-card reveal">
                    <h2 class="h4 mb-4">Legal & registration details</h2>
                    <dl class="row mb-0 small">
                        @foreach ([
                            'Legal status' => setting('legal_name'),
                            'CIN' => setting('cin'),
                            'PAN' => setting('pan'),
                            '12A registration' => setting('reg_12a'),
                            '80G registration' => setting('reg_80g'),
                            'CSR-1 registration' => setting('csr1'),
                            'NGO Darpan ID' => setting('darpan_id'),
                            'Registered office' => full_address(),
                        ] as $label => $value)
                            <dt class="col-sm-5 text-muted fw-semibold">{{ $label }}</dt>
                            <dd class="col-sm-7 mb-3">{{ $value ?: 'Details will be published soon' }}</dd>
                        @endforeach
                    </dl>
                </div>
            </aside>
        </div>
    </div>
</section>

<section class="section bg-surface">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-end gap-3">
            <x-section-head eyebrow="Documents" title="Reports & financials" />
            <a href="{{ route('reports') }}" class="btn btn-outline-primary mb-5">All reports</a>
        </div>
        <div class="grid grid-3" style="--min: 18rem">
            @forelse ($reports as $r)
                @include('site.partials.doc-card', ['title' => $r->title, 'meta' => trim((\App\Models\Report::TYPES[$r->type] ?? '').' · '.$r->year, ' ·'), 'text' => $r->summary, 'file' => $r->file, 'image' => $r->cover, 'pending' => true])
            @empty
                <x-empty icon="file-earmark-bar-graph" title="Reports will be published after the first financial year audit" />
            @endforelse
        </div>

        <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mt-5 pt-3">
            <x-section-head eyebrow="Compliance" title="Certifications" />
            <a href="{{ route('certifications') }}" class="btn btn-outline-primary mb-5">View all</a>
        </div>
        <div class="grid grid-3" style="--min: 18rem">
            @foreach ($certifications as $c)
                @include('site.partials.doc-card', ['title' => $c->title, 'meta' => $c->number ? 'No. '.$c->number : null, 'text' => $c->description, 'file' => $c->file, 'image' => $c->image, 'pending' => ! $c->number])
            @endforeach
        </div>
    </div>
</section>
<x-donate-cta title="Give with confidence" text="We publish our registrations and reports so that every supporter knows exactly how their contribution is used." />
@endsection
