@if (config('company.logo'))
    <img src="{{ asset(config('company.logo')) }}" alt="{{ config('company.name') }}" height="56">
@else
    <h3 class="fw-bold text-brand mb-0">{{ config('company.name') }}</h3>
@endif
<div class="text-muted small mt-1">{{ config('company.tagline') }}</div>
