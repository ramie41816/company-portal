<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')
</head>

<body>
    <nav class="topbar navbar py-3 mb-4 sticky-top shadow-sm">
        <div class="container-fluid px-4">
            <a class="navbar-brand d-flex align-items-center gap-2 font-serif text-brand fw-semibold"
                href="{{ route('home') }}">
                @if (config('company.logo'))
                    <img src="{{ asset(config('company.logo')) }}" alt="{{ config('company.name') }}" height="36"
                        class="brand-logo">
                @endif

                @if (config('company.show_name') || !config('company.logo'))
                    <span class="d-none d-md-inline">{{ config('company.app_name') }}</span>
                @endif
            </a>

            <div class="d-flex align-items-center gap-3">
                <button type="button" class="btn theme-toggle" data-theme-toggle aria-label="Toggle dark mode">
                    <i data-theme-icon class="bi bi-moon-stars"></i>
                </button>

                <div class="dropdown">
                    <a href="#"
                        class="d-flex align-items-center gap-2 text-decoration-none text-ink dropdown-toggle"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="avatar">
                            {{ strtoupper(auth()->user()->userInfo?->initials ?? 'DUP') }}
                        </span>
                        <span
                            class="d-none d-md-inline fw-medium">{{ auth()->user()->userInfo?->full_name ?? auth()->user()->email }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0"
                        style="background: var(--card); color: var(--ink);">
                        <li>
                            <h6 class="dropdown-header text-muted">{{ auth()->user()->email }}</h6>
                        </li>
                        <li>
                            <hr class="dropdown-divider" style="border-color: var(--line);">
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.show') }}">
                                <i class="bi bi-person-gear me-2"></i>My Profile
                            </a>
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i>Sign out
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <main class="pb-5">
        @yield('content')
    </main>

    <!-- Back to Top Button -->
    <button id="backToTopBtn" type="button"
        class="btn btn-primary rounded-circle shadow-lg d-none d-md-flex align-items-center justify-content-center"
        aria-label="Back to top">
        <i class="bi bi-arrow-up fs-5"></i>
    </button>

    @stack('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const backToTopBtn = document.getElementById('backToTopBtn');

            // Toggle visibility with fade transition based on scroll position
            window.addEventListener('scroll', function() {
                if (window.scrollY > 300) {
                    backToTopBtn.classList.add('show');
                } else {
                    backToTopBtn.classList.remove('show');
                }
            });

            // Smooth scroll to top on click
            backToTopBtn.addEventListener('click', function() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        });
    </script>

    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    toast: true,
                    position: 'bottom-end',
                    icon: 'success',
                    title: "{{ session('success') }}",
                    showConfirmButton: false,
                    timer: 4000,
                    timerProgressBar: true,
                    width: '450px',
                    customClass: {
                        popup: 'rounded-4 shadow px-3 py-2'
                    },
                    didOpen: (toast) => {
                        toast.onmouseenter = Swal.stopTimer;
                        toast.onmouseleave = Swal.resumeTimer;
                    }
                });
            });
        </script>
    @endif

    @if (session('info'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    toast: true,
                    position: 'bottom-end',
                    icon: 'info',
                    title: "{{ session('info') }}",
                    showConfirmButton: false,
                    timer: 5000,
                    timerProgressBar: true,
                    width: '450px',
                    customClass: {
                        popup: 'rounded-4 shadow px-3 py-2'
                    },
                    didOpen: (toast) => {
                        toast.onmouseenter = Swal.stopTimer;
                        toast.onmouseleave = Swal.resumeTimer;
                    }
                });
            });
        </script>
    @endif
</body>

</html>
