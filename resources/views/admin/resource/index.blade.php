@extends('layouts.admin')
@section('title', $title)

@section('content')
@php
    $sortLink = function ($col) {
        $dir = request('sort') === $col && request('dir') === 'asc' ? 'desc' : 'asc';
        return request()->fullUrlWithQuery(['sort' => $col, 'dir' => $dir, 'page' => null]);
    };
    $filtered = collect(request()->only(array_merge(['q', 'active', 'from', 'to'], array_keys($filters))))->filter(fn ($v) => $v !== null && $v !== '')->isNotEmpty();
@endphp

<div class="page-head">
    <div>
        <h1><i class="bi bi-{{ $icon }} text-primary"></i> {{ $title }}</h1>
        <p class="sub">{{ number_format($items->total()) }} {{ \Illuminate\Support\Str::plural(strtolower($singular), $items->total()) }}{{ $filtered ? ' match your filters' : '' }}</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        @if ($exportable)
            <a href="{{ route($route.'.export', request()->query()) }}" class="btn btn-light border"><i class="bi bi-download"></i> Export CSV</a>
        @endif
        @if ($canCreate)
            <a href="{{ route($route.'.create', request()->only('type')) }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Add {{ strtolower($singular) }}</a>
        @endif
    </div>
</div>

<div class="card table-card">
    <form class="toolbar" method="get" role="search">
        <div class="search">
            <i class="bi bi-search"></i>
            <label class="visually-hidden" for="q">Search</label>
            <input type="search" class="form-control" id="q" name="q" value="{{ request('q') }}" placeholder="{{ $searchPlaceholder }}">
        </div>
        @foreach ($filters as $name => [$label, $options])
            <label class="visually-hidden" for="filter-{{ $name }}">{{ $label }}</label>
            <select class="form-select" name="{{ $name }}" id="filter-{{ $name }}" data-autosubmit>
                <option value="">All {{ strtolower(\Illuminate\Support\Str::plural($label)) }}</option>
                @foreach ($options as $k => $v)
                    <option value="{{ $k }}" @selected((string) request($name) === (string) $k)>{{ $v }}</option>
                @endforeach
            </select>
        @endforeach
        @if ($hasStatus)
            <label class="visually-hidden" for="filter-active">Status</label>
            <select class="form-select" name="active" id="filter-active" data-autosubmit>
                <option value="">Active &amp; inactive</option>
                <option value="1" @selected(request('active') === '1')>Active only</option>
                <option value="0" @selected(request('active') === '0')>Inactive only</option>
            </select>
        @endif
        @if ($dateFilter)
            <label class="visually-hidden" for="from">From date</label>
            <input type="date" class="form-control" name="from" id="from" value="{{ request('from') }}" title="From date">
            <label class="visually-hidden" for="to">To date</label>
            <input type="date" class="form-control" name="to" id="to" value="{{ request('to') }}" title="To date">
        @endif
        <button class="btn btn-primary" type="submit">Filter</button>
        @if ($filtered)<a href="{{ route($route.'.index') }}" class="btn btn-link text-decoration-none">Clear</a>@endif
    </form>

    @if ($items->isEmpty())
        <div class="text-center py-5 px-3">
            <div class="tone-primary rounded-circle d-inline-grid mb-3" style="width:4rem;height:4rem;place-items:center;font-size:1.6rem"><i class="bi bi-{{ $icon }}"></i></div>
            <h2 class="h5">{{ $filtered ? 'No results found' : 'No '.strtolower($title).' yet' }}</h2>
            <p class="text-muted">{{ $filtered ? 'Try a different search or clear the filters.' : 'Items you add will appear here.' }}</p>
            @if ($canCreate && ! $filtered)<a href="{{ route($route.'.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Add the first {{ strtolower($singular) }}</a>@endif
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover table-stack align-middle">
                <thead>
                    <tr>
                        @foreach ($columns as $col)
                            <th scope="col">
                                @if (! empty($col[2]))
                                    <a href="{{ $sortLink($col[2]) }}">{{ $col[0] }}
                                        @if (request('sort') === $col[2])<i class="bi bi-caret-{{ request('dir') === 'asc' ? 'up' : 'down' }}-fill"></i>@endif
                                    </a>
                                @else
                                    {{ $col[0] }}
                                @endif
                            </th>
                        @endforeach
                        @if ($hasStatus)<th scope="col">Status</th>@endif
                        <th scope="col" class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $item)
                        <tr>
                            @foreach ($columns as $col)
                                <td data-label="{{ $col[0] }}">{{ $col[1]($item) }}</td>
                            @endforeach
                            @if ($hasStatus)
                                <td data-label="Status">
                                    <form method="post" action="{{ route($route.'.toggle', $item->getKey()) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="status-toggle" title="Click to mark {{ $item->is_active ? 'inactive' : 'active' }}">
                                            {!! $item->is_active ? '<span class="badge-soft badge-soft--success"><i class="bi bi-circle-fill me-1" style="font-size:.5rem"></i>Active</span>' : '<span class="badge-soft badge-soft--secondary">Inactive</span>' !!}
                                        </button>
                                    </form>
                                </td>
                            @endif
                            <td data-label="">
                                <div class="row-actions">
                                    @foreach ($controller->actionsFor($item) as [$label, $url, $aicon])
                                        <a href="{{ $url }}" class="btn btn-light border btn-icon" title="{{ $label }}" aria-label="{{ $label }}" @if (str_starts_with($url, 'http') && ! str_contains($url, '/admin/')) target="_blank" rel="noopener" @endif><i class="bi bi-{{ $aicon }}"></i></a>
                                    @endforeach
                                    <a href="{{ route($route.'.edit', $item->getKey()) }}" class="btn btn-light border btn-icon" title="Edit" aria-label="Edit"><i class="bi bi-pencil"></i></a>
                                    @if ($canDelete)
                                        <form method="post" action="{{ route($route.'.destroy', $item->getKey()) }}" data-confirm="This {{ strtolower($singular) }} will be permanently deleted. This cannot be undone.">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-light border btn-icon text-danger" title="Delete" aria-label="Delete"><i class="bi bi-trash"></i></button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if ($items->hasPages())
            <div class="p-3 border-top">{{ $items->onEachSide(1)->links() }}</div>
        @endif
    @endif
</div>
@endsection
