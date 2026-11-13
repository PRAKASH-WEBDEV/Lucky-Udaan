@extends('layouts.admin')
@section('title', 'My profile')

@section('content')
<div class="page-head"><h1><i class="bi bi-person-circle text-primary"></i> My profile</h1></div>

<form method="post" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data" novalidate>
    @csrf @method('PUT')
    <div class="row g-4">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header">Account details</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="name">Full name</label>
                            <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $admin->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="email">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $admin->email) }}" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="phone">Phone</label>
                            <input class="form-control" id="phone" name="phone" value="{{ old('phone', $admin->phone) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="avatar">Profile photo</label>
                            <input type="file" class="form-control @error('avatar') is-invalid @enderror" id="avatar" name="avatar" accept="image/*">
                            @error('avatar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="form-section-title">Change password</div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="current_password">Current password</label>
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" autocomplete="current-password">
                            @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="password">New password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" autocomplete="new-password">
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="password_confirmation">Confirm new password</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" autocomplete="new-password">
                        </div>
                    </div>
                    <button class="btn btn-primary" type="submit"><i class="bi bi-check-lg"></i> Save profile</button>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="user-chip justify-content-center mb-2"><span class="avatar" style="width:5rem;height:5rem;font-size:1.5rem">@if ($admin->avatar)<img src="{{ upload_url($admin->avatar) }}" alt="">@else{{ $admin->initials() }}@endif</span></div>
                    <div class="fw-bold">{{ $admin->name }}</div>
                    <div class="text-muted small mb-3">{{ $admin->role?->name }}</div>
                    <div class="small text-muted">Last sign-in: {{ $admin->last_login_at?->format('d M Y, h:i A') ?? '—' }}</div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
