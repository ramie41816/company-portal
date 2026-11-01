@extends('layouts.guest')

@section('content')
    {{-- Welcome Header Section --}}
    <div class="mb-4">
        <h3 class="fw-bold text-brand mb-1">Welcome Back!</h3>
        <p class="text-muted small mb-0">Please sign in to access your dashboard.</p>
    </div>

    <form method="POST" action="{{ route('login') }}" id="loginForm">
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

        {{-- Password Field with Forgot Password Link --}}
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

        {{-- Submit Button with Loading State --}}
        <button type="submit" id="submitBtn" class="btn btn-brand w-100 py-2 fw-semibold position-relative">
            <span id="btnText">
                <i class="bi bi-box-arrow-in-right me-1"></i> Sign In
            </span>
            <span id="btnSpinner" class="d-none">
                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                Signing in...
            </span>
        </button>
    </form>

    @push('scripts')
        <script>
            document.getElementById('loginForm').addEventListener('submit', function() {
                const btn = document.getElementById('submitBtn');
                const btnText = document.getElementById('btnText');
                const btnSpinner = document.getElementById('btnSpinner');

                // Disable button and swap text with spinner
                btn.disabled = true;
                btnText.classList.add('d-none');
                btnSpinner.classList.remove('d-none');
            });
        </script>
    @endpush
@endsection
