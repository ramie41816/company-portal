<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')
</head>

<body>
    <nav class="topbar navbar py-3 mb-4">
        <div class="container-fluid px-4">
            <a class="navbar-brand d-flex align-items-center gap-2 font-serif text-brand fw-semibold"
                href="{{ route('home') }}">
                @if (config('company.logo'))
                    <img src="{{ asset(config('company.logo')) }}" alt="{{ config('company.name') }}" height="36"
                        class="brand-logo">
                @endif

                @if (config('company.show_name') || !config('company.logo'))
                    <span>{{ config('company.name') }}</span>
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
                        <span class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                        <span class="d-none d-md-inline fw-medium">{{ auth()->user()->name }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                        <li>
                            <h6 class="dropdown-header">{{ auth()->user()->email }}</h6>
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">
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

    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: "{{ session('success') }}",
                confirmButtonColor: '#2b3a67',
                timer: 3500,
                timerProgressBar: true,
                customClass: {
                    popup: 'rounded-4 shadow'
                }
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: "{{ session('error') }}",
                confirmButtonColor: '#2b3a67',
                customClass: {
                    popup: 'rounded-4 shadow'
                }
            });
        </script>
    @endif
</body>

</html>
