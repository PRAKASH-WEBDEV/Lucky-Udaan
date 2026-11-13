@extends('admin.auth.layout')
@section('title', 'Forgot password')

@section('content')
<h1 class="h3 mb-1">Reset your password</h1>
<p class="text-muted mb-4">Enter your admin email and we’ll send you a secure reset link.</p>

<form method="post" action="{{ route('admin.password.email') }}" novalidate>
    @csrf
    <div class="mb-4">
        <label class="form-label" for="email">Email address</label>
        <input type="email" name="email" id="email" value="{{ old('email') }}" class="form-control form-control-lg @error('email') is-invalid @enderror" required autofocus autocomplete="username">
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <button type="submit" class="btn btn-primary btn-lg w-100">Email reset link</button>
</form>
<p class="text-center mt-4"><a href="{{ route('admin.login') }}" class="text-decoration-none">Back to sign in</a></p>
@endsection
