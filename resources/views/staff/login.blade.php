@extends('admin.auth.layout')
@section('title', 'Staff sign in')

@section('content')
<h1 class="h3 mb-1">Staff sign in</h1>
<p class="text-muted mb-4">Sign in to mark your attendance.</p>

@if (session('error'))
    <div class="alert alert-danger d-flex gap-2" role="alert"><i class="bi bi-exclamation-circle-fill"></i><div>{{ session('error') }}</div></div>
@endif

<form method="post" action="{{ route('staff.login.attempt') }}" novalidate>
    @csrf
    <div class="mb-3">
        <label class="form-label" for="email">Email address</label>
        <input type="email" name="email" id="email" value="{{ old('email') }}" class="form-control form-control-lg @error('email') is-invalid @enderror" required autofocus autocomplete="username">
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
        <label class="form-label" for="password">Password</label>
        <div class="input-group">
            <input type="password" name="password" id="password" class="form-control form-control-lg @error('password') is-invalid @enderror" required autocomplete="current-password">
            <button class="btn btn-light border" type="button" onclick="const p=document.getElementById('password');p.type=p.type==='password'?'text':'password';this.firstElementChild.className=p.type==='password'?'bi bi-eye':'bi bi-eye-slash'" aria-label="Show or hide password"><i class="bi bi-eye"></i></button>
        </div>
        @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    </div>
    <div class="form-check mb-4">
        <input class="form-check-input" type="checkbox" name="remember" value="1" id="remember">
        <label class="form-check-label" for="remember">Keep me signed in on this device</label>
    </div>
    <button type="submit" class="btn btn-primary btn-lg w-100">Sign in <i class="bi bi-arrow-right"></i></button>
    <p class="small text-muted mt-3 mb-0">Forgot your password? Ask the office to reset it for you.</p>
</form>
@endsection
