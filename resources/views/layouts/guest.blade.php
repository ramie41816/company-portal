@php $loginBg = config('company.login_bg'); @endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')

    @if ($loginBg)
        <style>
            .auth-side.has-bg {
                background:
                    linear-gradient(to top,
                        color-mix(in srgb, var(--brand-dark) 94%, transparent) 0%,
                        color-mix(in srgb, var(--brand-dark) 72%, transparent) 38%,
                        color-mix(in srgb, var(--brand) 22%, transparent) 100%),
                    url('{{ asset($loginBg) }}') center / cover no-repeat;
            }

            .auth-side.has-bg::after {
                display: none;
            }
        </style>
    @endif
</head>

<body class="bg-body" style="color: var(--ink);">

    {{-- Theme Toggle Button --}}
    <button type="button" class="btn theme-toggle position-fixed top-0 end-0 m-3 shadow-sm" style="z-index: 10;"
        data-theme-toggle aria-label="Toggle dark mode">
        <i data-theme-icon class="bi bi-moon-stars"></i>
    </button>

    <div class="container-fluid">
        <div class="row min-vh-100">

            {{-- Brand panel (banner) - Visible on large screens --}}
            <div
                class="col-lg-6 auth-side d-none d-lg-flex p-5 {{ $loginBg ? 'has-bg align-items-end justify-content-start' : 'align-items-center justify-content-center' }}">
                <div class="position-relative" style="max-width: 460px; z-index: 1;">
                    @if (config('company.logo'))
                        <div class="bg-white rounded-4 p-3 d-inline-block mb-4 shadow-sm">
                            <img src="{{ asset(config('company.logo')) }}" alt="{{ config('company.name') }}"
                                style="height: 72px; width: auto; max-width: 100%; display: block;">
                        </div>
                    @endif

                    @if (config('company.show_name') || !config('company.logo'))
                        <h1 class="display-5 mb-3 fw-bold text-white">{{ config('company.name') }}</h1>
                    @endif

                    <div class="auth-accent-line mb-3"></div>
                    <p class="lead opacity-75 mb-0 text-white-50">{{ config('company.tagline') }}</p>
                </div>
            </div>

            {{-- Form panel --}}
            <div class="col-lg-6 d-flex align-items-center justify-content-center p-3 p-md-4 auth-form-side">
                <div class="w-100" style="max-width: 480px;">

                    {{-- Session Success Alert --}}
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show small p-3 pe-5 mb-3 rounded-4 shadow-sm"
                            role="alert">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-check-circle-fill me-2 flex-shrink-0"></i>
                                <span>{{ session('success') }}</span>
                            </div>
                            <button type="button" class="btn-close py-2" data-bs-dismiss="alert"
                                aria-label="Close"></button>
                        </div>
                    @endif

                    {{-- Session Error Alert --}}
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show small p-3 pe-5 mb-3 rounded-4 shadow-sm"
                            role="alert">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-exclamation-triangle-fill me-2 flex-shrink-0"></i>
                                <span>{{ session('error') }}</span>
                            </div>
                            <button type="button" class="btn-close py-2" data-bs-dismiss="alert"
                                aria-label="Close"></button>
                        </div>
                    @endif

                    {{-- Mobile Branding Header --}}
                    <div class="d-lg-none text-center mb-4 pt-3">
                        @if (config('company.logo'))
                            <img src="{{ asset(config('company.logo')) }}" alt="{{ config('company.name') }}"
                                height="80" class="brand-logo mx-auto mb-2 shadow-sm rounded-3 p-2 bg-white">
                        @endif

                        @if (config('company.show_name') || !config('company.logo'))
                            <h3 class="fw-bold mb-1" style="color: var(--ink);">{{ config('company.name') }}</h3>
                        @endif
                        <p class="text-muted small mb-0">{{ config('company.tagline') }}</p>
                    </div>

                    {{-- Main Login Form Card with Portal CSS Variables --}}
                    <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5"
                        style="background: var(--card); color: var(--ink); border: 1px solid var(--line) !important;">
                        @yield('content')
                    </div>

                    {{-- Footer Copyright --}}
                    <p class="text-center text-muted small mt-4 mb-0">
                        &copy; {{ date('Y') }} {{ config('company.name') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    @stack('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');

            if (toggleBtn && passwordInput) {
                toggleBtn.addEventListener('click', function() {
                    const icon = toggleBtn.querySelector('i');
                    if (passwordInput.type === 'password') {
                        passwordInput.type = 'text';
                        icon.classList.remove('bi-eye');
                        icon.classList.add('bi-eye-slash');
                    } else {
                        passwordInput.type = 'password';
                        icon.classList.remove('bi-eye-slash');
                        icon.classList.add('bi-eye');
                    }
                });
            }
        });
    </script>
</body>

</html>
