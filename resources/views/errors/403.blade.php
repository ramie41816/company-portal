@extends('layouts.app')

@section('content')
    {{-- Meta refresh redirection after 5 seconds --}}
    @php
        $redirectUrl = auth()->check() ? route('home') : url('/');
    @endphp
    @push('head')
        <meta http-equiv="refresh" content="5;url={{ $redirectUrl }}">
    @endpush

    <div class="container d-flex flex-column align-items-center justify-content-center py-5" style="min-height: 75vh;">
        <div class="col-md-6 col-lg-5 text-center">

            {{-- Portrait Style Error Card --}}
            <div class="p-4 p-lg-5 d-flex flex-column text-center justify-content-between shadow-sm"
                style="background: var(--card); color: var(--ink); border: 1px solid var(--line); border-radius: 1rem;">

                <div>
                    {{-- Large Icon Container --}}
                    <div class="mx-auto mb-4 d-flex align-items-center justify-content-center bg-danger bg-opacity-10 text-danger rounded-circle shadow-sm"
                        style="width: 90px; height: 90px; font-size: 2.75rem;">
                        <i class="bi bi-shield-exclamation"></i>
                    </div>

                    <h1 class="fw-bold mb-2 fs-2" style="color: var(--ink);">403</h1>
                    <h5 class="fw-semibold mb-2" style="color: var(--ink);">Access Denied</h5>
                    <p class="text-muted small px-2 mb-4">
                        You do not have the required permissions to access this page or resource.
                    </p>

                    {{-- Countdown Badge --}}
                    <div class="mb-4">
                        <span class="badge rounded-pill bg-warning bg-opacity-15 text-dark px-3 py-2 fw-medium">
                            <i class="bi bi-clock-history me-1"></i> Redirecting in <span id="timer">5</span> seconds...
                        </span>
                    </div>
                </div>

                <div class="mt-2">
                    <a href="{{ $redirectUrl }}" class="btn btn-primary w-100 py-2">
                        <i class="bi bi-arrow-left me-1"></i> Return Home Now
                    </a>
                </div>

            </div>

        </div>
    </div>

    @push('scripts')
        <script>
            let seconds = 5;
            const timerElement = document.getElementById('timer');
            const interval = setInterval(() => {
                seconds--;
                if (seconds >= 0 && timerElement) {
                    timerElement.textContent = seconds;
                } else {
                    clearInterval(interval);
                }
            }, 1000);
        </script>
    @endpush
@endsection
