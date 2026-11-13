@extends('admin.auth.layout')
@section('title', 'Choose a new password')

@section('content')
<h1 class="h3 mb-1">Choose a new password</h1>
<p class="text-muted mb-4">At least 8 characters, with letters and numbers.</p>

<form method="post" action="{{ route('admin.password.update') }}" novalidate>
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    <div class="mb-3">
        <label class="form-label" for="email">Email address</label>
        <input type="email" name="email" id="email" value="{{ old('email', $email) }}" class="form-control @error('email') is-invalid @enderror" required autocomplete="username">
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
        <label class="form-label" for="password">New password</label>
        <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="new-password">
        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="mb-4">
        <label class="form-label" for="password_confirmation">Confirm new password</label>
        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required autocomplete="new-password">
    </div>
    <button type="submit" class="btn btn-primary btn-lg w-100">Update password</button>
</form>
@endsection
