<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ config('company.app_name') }}</title>

@php $favicon = config('company.favicon') ?: config('company.logo'); @endphp
@if ($favicon)
    <link rel="icon" href="{{ asset($favicon) }}">
@endif

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

@vite(['resources/sass/app.scss', 'resources/js/app.js'])

<style>
    :root {
        --brand: {{ config('company.primary_color') }};
        --accent: {{ config('company.accent_color') }};
    }

    @if (config('company.login_bg'))
        .hero {
            background-image: linear-gradient(rgba(0, 0, 0, 0.65), rgba(0, 0, 0, 0.65)), url('{{ asset(config('company.login_bg')) }}');
        }
    @endif
</style>
