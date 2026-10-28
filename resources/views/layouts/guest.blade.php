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

<body>

    <button type="button" class="btn theme-toggle position-fixed top-0 end-0 m-3" style="z-index: 10;" data-theme-toggle
        aria-label="Toggle dark mode">
        <i data-theme-icon class="bi bi-moon-stars"></i>
    </button>

    <div class="container-fluid">
        <div class="row min-vh-100">

            {{-- Brand panel (banner) --}}
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
                        <h1 class="display-5 mb-3">{{ config('company.name') }}</h1>
                    @endif

                    <div class="auth-accent-line mb-3"></div>
                    <p class="lead opacity-75 mb-0">{{ config('company.tagline') }}</p>
                </div>
            </div>

            {{-- Form panel --}}
            <div class="col-lg-6 d-flex align-items-center justify-content-center p-4 auth-form-side">
                {{-- Increased max-width from 420px to 480px to give the form more breathing room --}}
                <div class="w-100" style="max-width: 480px;">

                    <div class="d-lg-none text-center mb-4">
                        @if (config('company.logo'))
                            <img src="{{ asset(config('company.logo')) }}" alt="{{ config('company.name') }}"
                                height="56" class="brand-logo mx-auto mb-2">
                        @endif

                        @if (config('company.show_name') || !config('company.logo'))
                            <h3 class="text-brand mb-0">{{ config('company.name') }}</h3>
                        @endif
                    </div>

                    <div class="card border shadow-sm rounded-4 p-4 p-md-5 bg-body">
                        @yield('content')
                    </div>

                    <p class="text-center text-muted small mt-4 mb-0">
                        &copy; {{ date('Y') }} {{ config('company.name') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Script for Password Visibility Toggle --}}
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
