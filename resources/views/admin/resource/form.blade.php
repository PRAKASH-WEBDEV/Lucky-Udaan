@extends('layouts.admin')
@php $editing = $item->exists; @endphp
@section('title', ($editing ? 'Edit ' : 'Add ').strtolower($singular))

@section('content')
@php
    $main = array_values(array_filter($fields, fn ($f) => empty($f['side'])));
    $side = array_values(array_filter($fields, fn ($f) => ! empty($f['side'])));
@endphp

<div class="page-head">
    <div>
        <a href="{{ route($route.'.index') }}" class="text-decoration-none small"><i class="bi bi-arrow-left"></i> {{ $title }}</a>
        <h1 class="mt-1">{{ $editing ? 'Edit '.strtolower($singular) : 'Add '.strtolower($singular) }}</h1>
    </div>
    @if ($editing && ! empty($actions))
        <div class="d-flex flex-wrap gap-2">
            @foreach ($actions as [$label, $url, $aicon])
                <a href="{{ $url }}" class="btn btn-light border" @if (! str_contains($url, '/admin/')) target="_blank" rel="noopener" @endif><i class="bi bi-{{ $aicon }}"></i> {{ $label }}</a>
            @endforeach
        </div>
    @endif
</div>

@if ($errors->any())
    <div class="alert alert-danger d-flex gap-2" role="alert">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <div><strong>Please fix the following:</strong>
            <ul class="mb-0 ps-3">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    </div>
@endif

<form method="post" action="{{ $editing ? route($route.'.update', $item->getKey()) : route($route.'.store') }}" enctype="multipart/form-data" novalidate data-dirty-check>
    @csrf
    @if ($editing) @method('PUT') @endif
    <div class="row g-4">
        <div class="{{ $side ? 'col-xl-8' : 'col-12 col-xxl-10' }}">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        @foreach ($main as $field)
                            @include('admin.resource.field', ['field' => $field, 'item' => $item])
                        @endforeach
                    </div>
                    <div class="sticky-actions">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> {{ $editing ? 'Save changes' : 'Create '.strtolower($singular) }}</button>
                        <button type="submit" name="_after" value="stay" class="btn btn-light border">Save &amp; continue editing</button>
                        @if (! $editing && $canCreate)<button type="submit" name="_after" value="new" class="btn btn-light border d-none d-sm-inline-flex">Save &amp; add another</button>@endif
                        <a href="{{ route($route.'.index') }}" class="btn btn-link text-decoration-none ms-auto">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
        @if ($side)
            <div class="col-xl-4">
                <div class="card position-sticky" style="top: calc(var(--top-h) + 1rem)">
                    <div class="card-header">Settings</div>
                    <div class="card-body">
                        <div class="row">
                            @foreach ($side as $field)
                                @include('admin.resource.field', ['field' => $field + ['col' => 12], 'item' => $item])
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</form>

@if ($editing && $canDelete)
    <form method="post" action="{{ route($route.'.destroy', $item->getKey()) }}" class="mt-4" data-confirm="This {{ strtolower($singular) }} will be permanently deleted. This cannot be undone.">
        @csrf @method('DELETE')
        <button class="btn btn-outline-danger btn-sm" type="submit"><i class="bi bi-trash"></i> Delete this {{ strtolower($singular) }}</button>
    </form>
@endif
@endsection
