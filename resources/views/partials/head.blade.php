<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ config('company.name') }}</title>

{{-- Browser tab icon: uses the favicon, or the logo if no favicon is set --}}
@php $favicon = config('company.favicon') ?: config('company.logo'); @endphp
@if ($favicon)
    <link rel="icon" href="{{ asset($favicon) }}">
@endif

{{-- Sets light/dark before the page paints, so there is no flash --}}
<script>
    (function() {
        var saved = null;
        try {
            saved = localStorage.getItem('theme');
        } catch (e) {}
        var theme = saved || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        document.documentElement.setAttribute('data-bs-theme', theme);
    })();
</script>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Playfair+Display:wght@500;600;700&display=swap"
    rel="stylesheet">
<!-- SweetAlert2 CSS & JS CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@vite(['resources/sass/app.scss', 'resources/js/app.js'])

<style>
    /* Light mode (default) */
    :root {
        --brand: {{ config('company.primary_color') }};
        --accent: {{ config('company.accent_color') }};
        --brand-dark: color-mix(in srgb, var(--brand) 78%, black);
        --brand-soft: color-mix(in srgb, var(--brand) 8%, white);
        --brand-text: var(--brand);
        --ink: #1f2937;
        --muted: #6b7280;
        --line: #e8eaee;
        --surface: #f6f7f9;
        --card: #ffffff;
        --field: #ffffff;
    }

    /* Dark mode */
    [data-bs-theme="dark"] {
        --brand-soft: color-mix(in srgb, var(--brand) 35%, var(--card));
        --brand-text: color-mix(in srgb, var(--brand) 40%, white);
        --ink: #e5e7eb;
        --muted: #9ca3af;
        --line: #2a3340;
        --surface: #0f1620;
        --card: #161e2a;
        --field: #0f1620;
    }

    /* Company logo: in dark mode it sits on a light chip so it stays visible */
    .brand-logo {
        display: block;
        width: auto;
        max-width: 100%;
        object-fit: contain;
    }

    [data-bs-theme="dark"] .brand-logo {
        background: #ffffff;
        padding: .25rem .5rem;
        border-radius: .5rem;
    }

    .hero {
        background-image: linear-gradient(rgba(0, 0, 0, 0.65), rgba(0, 0, 0, 0.65)), url('{{ config('company.login_bg') }}') !important;
        background-position: center !important;
        background-size: cover !important;
        background-repeat: no-repeat !important;
        background-attachment: fixed !important;
        color: #ffffff !important;
    }

    /* Disable fixed background on mobile for better compatibility */
    @media (max-width: 768px) {
        .hero {
            background-attachment: scroll !important;
        }
    }
</style>
