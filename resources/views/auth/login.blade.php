@extends('layouts.guest')

@section('content')
    {{-- Welcome Header Section --}}
    <div class="mb-4">
        <h3 class="fw-bold text-brand mb-1">Welcome Back!</h3>
        <p class="text-muted small mb-0">Please sign in to access your dashboard.</p>
    </div>

    {{-- Session Status Alert (e.g., after password reset) --}}
    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show small py-2" role="alert">
            {{ session('status') }}
            <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        {{-- Email Field --}}
        <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <div class="input-group">
                <span class="input-group-text bg-transparent text-muted border-end-0">
                    <i class="bi bi-envelope"></i>
                </span>
                <input type="email" id="email" name="email" value="{{ old('email') }}"
                    class="form-control border-start-0 ps-2 @error('email') is-invalid @enderror"
                    placeholder="name@example.com" required autofocus autocomplete="email">
            </div>
            @error('email')
                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
            @enderror
        </div>

        {{-- Password Field --}}
        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <label for="password" class="form-label mb-0">Password</label>
                @if (Route::has('password.request'))
                    <a class="small text-decoration-none" href="{{ route('password.request') }}">Forgot password?</a>
                @endif
            </div>
            <div class="input-group">
                <span class="input-group-text bg-transparent text-muted border-end-0">
                    <i class="bi bi-lock"></i>
                </span>
                <input type="password" id="password" name="password"
                    class="form-control border-start-0 border-end-0 px-2 @error('password') is-invalid @enderror"
                    placeholder="••••••••" required autocomplete="current-password">
                <button class="btn btn-outline-secondary border-start-0 bg-transparent text-muted px-3" type="button"
                    id="togglePassword" aria-label="Toggle password visibility">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
            @error('password')
                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
            @enderror
        </div>

        {{-- Remember Me & Submit --}}
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="form-check mb-0">
                <input type="checkbox" class="form-check-input" id="remember" name="remember"
                    {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label small text-muted" for="remember">Remember me</label>
            </div>
        </div>

        <button type="submit" class="btn btn-brand w-100 py-2 fw-semibold">
            <i class="bi bi-box-arrow-in-right me-1"></i> Sign In
        </button>
    </form>
@endsection
