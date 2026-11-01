@extends('layouts.guest')

@section('content')
    {{-- Header Section --}}
    <div class="mb-4">
        <h3 class="fw-bold text-brand mb-1">Reset Password</h3>
        <p class="text-muted small mb-0">Enter your email address and we'll send you a password reset link.</p>
    </div>

    <form method="POST" action="{{ route('password.email') }}" id="forgotForm">
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

        {{-- Submit Button with Loading State --}}
        <button type="submit" id="submitBtn" class="btn btn-brand w-100 py-2 fw-semibold mb-3 position-relative">
            <span id="btnText">
                <i class="bi bi-envelope-paper me-1"></i> Send Password Reset Link
            </span>
            <span id="btnSpinner" class="d-none">
                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                Sending reset link...
            </span>
        </button>

        {{-- Back to Login Link --}}
        <div class="text-center">
            <a href="{{ route('login') }}" class="small text-decoration-none text-muted">
                <i class="bi bi-arrow-left me-1"></i> Back to Sign In
            </a>
        </div>
    </form>

    @push('scripts')
        <script>
            document.getElementById('forgotForm').addEventListener('submit', function() {
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
