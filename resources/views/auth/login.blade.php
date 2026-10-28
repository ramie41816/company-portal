    @extends('layouts.guest')

    @section('content')
        {{-- Welcome Header Section --}}
        <div class="mb-4">
            <h3 class="fw-bold text-brand mb-1">Welcome Back!</h3>
            <p class="text-muted small">Please sign in to access your dashboard.</p>
        </div>

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
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            {{-- Password Field --}}
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <label for="password" class="form-label">Password</label>
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
                        placeholder="••••••••" required>
                    <button class="btn btn-outline-secondary border-start-0 bg-transparent text-muted" type="button"
                        id="togglePassword">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
                @error('password')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            {{-- Remember Me --}}
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                <label class="form-check-label small" for="remember">Remember me</label>
            </div>

            <button type="submit" class="btn btn-brand w-100 py-2 fw-semibold">Sign In</button>
        </form>
    @endsection
