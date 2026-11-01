@extends('layouts.guest')

@section('content')
    {{-- Header Section --}}
    <div class="mb-4">
        <h3 class="fw-bold text-brand mb-1">Set New Password</h3>
        <p class="text-muted small mb-0">Please enter your new password below.</p>
    </div>

    <form method="POST" action="{{ route('password.update') }}" id="resetForm">
        @csrf

        {{-- Hidden Token Field --}}
        <input type="hidden" name="token" value="{{ $token ?? request()->route('token') }}">

        {{-- Email Field --}}
        <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <div class="input-group">
                <span class="input-group-text bg-transparent text-muted border-end-0">
                    <i class="bi bi-envelope"></i>
                </span>
                <input type="email" id="email" name="email" value="{{ $email ?? old('email') }}"
                    class="form-control border-start-0 ps-2 @error('email') is-invalid @enderror"
                    placeholder="name@example.com" required autofocus autocomplete="email">
            </div>
            @error('email')
                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
            @enderror
        </div>

        {{-- New Password Field --}}
        <div class="mb-3">
            <label for="password" class="form-label">New Password</label>
            <div class="input-group">
                <span class="input-group-text bg-transparent text-muted border-end-0">
                    <i class="bi bi-lock"></i>
                </span>
                <input type="password" id="password" name="password"
                    class="form-control border-start-0 border-end-0 px-2 @error('password') is-invalid @enderror"
                    placeholder="••••••••" required autocomplete="new-password">
                <button class="btn btn-outline-secondary border-start-0 bg-transparent text-muted px-3" type="button"
                    id="togglePassword" aria-label="Toggle password visibility">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
            @error('password')
                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
            @enderror
        </div>

        {{-- Confirm Password Field --}}
        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Confirm Password</label>
            <div class="input-group">
                <span class="input-group-text bg-transparent text-muted border-end-0">
                    <i class="bi bi-lock-fill"></i>
                </span>
                <input type="password" id="password_confirmation" name="password_confirmation"
                    class="form-control border-start-0 ps-2 @error('password_confirmation') is-invalid @enderror"
                    placeholder="••••••••" required autocomplete="new-password">
            </div>
            @error('password_confirmation')
                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
            @enderror
        </div>

        {{-- Submit Button with Loading State --}}
        <button type="submit" id="submitBtn" class="btn btn-brand w-100 py-2 fw-semibold mb-3 position-relative">
            <span id="btnText">
                <i class="bi bi-check2-circle me-1"></i> Reset Password
            </span>
            <span id="btnSpinner" class="d-none">
                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                Resetting password...
            </span>
        </button>
    </form>

    @push('scripts')
        <script>
            document.getElementById('resetForm').addEventListener('submit', function() {
                const btn = document.getElementById('submitBtn');
                const btnText = document.getElementById('btnText');
                const btnSpinner = document.getElementById('btnSpinner');

                btn.disabled = true;
                btnText.classList.add('d-none');
                btnSpinner.classList.remove('d-none');
            });
        </script>
    @endpush
@endsection
