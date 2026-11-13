@extends('layouts.admin')
@section('title', 'Website settings')

@section('content')
@php
    $tab = request('tab');
    if (! array_key_exists($tab, $groups)) {
        $tab = $errors->any() ? collect($groups)->keys()->first(fn ($g) => collect($groups[$g][2])->contains(fn ($f) => $errors->has($f[0]))) : 'general';
        $tab ??= 'general';
    }
@endphp
<div class="page-head">
    <div>
        <h1><i class="bi bi-gear text-primary"></i> Website settings</h1>
        <p class="sub">Organisation details used across the header, footer, contact page, donation page, emails and search results.</p>
    </div>
</div>

@if ($errors->any())
    <div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill me-1"></i> Some settings need attention — check the highlighted fields.</div>
@endif

<form method="post" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" novalidate data-dirty-check>
    @csrf @method('PUT')
    <input type="hidden" name="_tab" id="_tab" value="{{ $tab }}">
    <div class="card">
        <ul class="nav settings-tabs px-3" role="tablist">
            @foreach ($groups as $key => [$label, $icon])
                <li class="nav-item" role="presentation">
                    <button class="nav-link @if ($tab === $key) active @endif" data-bs-toggle="tab" data-bs-target="#tab-{{ $key }}" type="button" role="tab"
                            onclick="document.getElementById('_tab').value='{{ $key }}'" aria-selected="{{ $tab === $key ? 'true' : 'false' }}">
                        <i class="bi bi-{{ $icon }} me-1"></i>{{ $label }}
                    </button>
                </li>
            @endforeach
        </ul>
        <div class="card-body tab-content">
            @foreach ($groups as $key => [$label, $icon, $fields])
                <div class="tab-pane fade @if ($tab === $key) show active @endif" id="tab-{{ $key }}" role="tabpanel">
                    @if ($key === 'donation')<p class="text-muted small">Bank and UPI details appear on the Donate page. Registration numbers appear on the Transparency page, footer and receipts. Leave blank to hide.</p>@endif
                    @if ($key === 'home')<p class="text-muted small">Impact counters animate on the homepage. Use real, verifiable figures.</p>@endif
                    @if ($key === 'theme')<p class="text-muted small">Colours apply instantly across the website. Choose a dark colour with enough contrast for white text.</p>@endif
                    <div class="row">
                        @foreach ($fields as [$fkey, $flabel, $ftype])
                            @php $val = old($fkey, $values[$fkey] ?? ''); $err = $errors->first($fkey); @endphp
                            <div class="{{ in_array($ftype, ['textarea', 'image']) || $fkey === 'map_embed' ? 'col-12 col-xxl-6' : 'col-md-6 col-xxl-4' }} mb-3">
                                <label class="form-label" for="s-{{ $fkey }}">{{ $flabel }}</label>
                                @if ($ftype === 'textarea')
                                    <textarea class="form-control @if ($err) is-invalid @endif" name="{{ $fkey }}" id="s-{{ $fkey }}" rows="3">{{ $val }}</textarea>
                                @elseif ($ftype === 'image')
                                    <div class="d-flex gap-3 align-items-center image-input" data-image-input>
                                        <div class="preview mb-0 flex-shrink-0" style="width:7rem;aspect-ratio:1">
                                            @if (! empty($values[$fkey]))<img src="{{ upload_url($values[$fkey]) }}" alt="">@else<i class="bi bi-image fs-3"></i>@endif
                                        </div>
                                        <div class="flex-grow-1 min-w-0">
                                            <input type="file" class="form-control form-control-sm @if ($err) is-invalid @endif" name="{{ $fkey }}" id="s-{{ $fkey }}" accept="image/jpeg,image/png,image/webp">
                                            @if (! empty($values[$fkey]))
                                                <div class="form-check mt-2"><input class="form-check-input" type="checkbox" name="remove_{{ $fkey }}" value="1" id="rm-{{ $fkey }}"><label class="form-check-label small" for="rm-{{ $fkey }}">Remove</label></div>
                                            @endif
                                        </div>
                                    </div>
                                @elseif ($ftype === 'color')
                                    <div class="d-flex gap-2">
                                        <input type="color" class="form-control form-control-color" value="{{ $val ?: '#1f7a4d' }}" aria-label="{{ $flabel }} picker" oninput="this.nextElementSibling.value=this.value">
                                        <input type="text" class="form-control @if ($err) is-invalid @endif" name="{{ $fkey }}" id="s-{{ $fkey }}" value="{{ $val }}" pattern="#[0-9a-fA-F]{6}" oninput="if(/^#[0-9a-f]{6}$/i.test(this.value))this.previousElementSibling.value=this.value">
                                    </div>
                                @else
                                    <input type="{{ in_array($ftype, ['email', 'url', 'number']) ? $ftype : 'text' }}" class="form-control @if ($err) is-invalid @endif" name="{{ $fkey }}" id="s-{{ $fkey }}" value="{{ $val }}">
                                @endif
                                @if ($err)<div class="invalid-feedback d-block">{{ $err }}</div>@endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
            <div class="sticky-actions">
                <button class="btn btn-primary" type="submit"><i class="bi bi-check-lg"></i> Save settings</button>
            </div>
        </div>
    </div>
</form>
@endsection
