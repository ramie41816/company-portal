@extends('layouts.app')

@section('content')
    <div class="container py-4">

        <div class="hero mb-5">
            <p class="text-uppercase small opacity-75 mb-2" style="letter-spacing: .12em;">{{ config('company.tagline') }}</p>
            <h1 class="display-6 mb-2">Welcome back, {{ auth()->user()->userInfo->first_name }}</h1>
            <p class="opacity-75 mb-0">What would you like to work on today?</p>
        </div>

        @php
            $services = [];

            if (auth()->user()->hasPermission('quotations.index')) {
                $services[] = [
                    'title' => 'Quotation',
                    'desc' => 'Create, manage and send customer quotations.',
                    'icon' => 'bi-file-earmark-text',
                    'url' => route('quotations.dashboard'),
                    'available' => true,
                ];
            }

            if (auth()->user()->hasPermission('users.index')) {
                $services[] = [
                    'title' => 'User Management',
                    'desc' => 'Manage staff accounts and their access.',
                    'icon' => 'bi-people',
                    'url' => route('users.dashboard'),
                    'available' => true,
                ];
            }

            // Added My Profile service card
            $services[] = [
                'title' => 'My Profile',
                'desc' => 'Manage your personal details and account password.',
                'icon' => 'bi-person-badge',
                'url' => route('profile.show'),
                'available' => true,
            ];
        @endphp

        <h5 class="fw-semibold mb-3">Our Services</h5>

        <div class="row g-4">
            @forelse ($services as $service)
                <div class="col-md-6 col-lg-4 d-flex">
                    <div class="service-card p-4 p-lg-5 d-flex flex-column text-center justify-content-between shadow-sm w-100 h-100"
                        style="background: var(--card); border: 1px solid var(--line); border-radius: 1rem;">

                        <div>
                            <div class="service-icon mx-auto mb-4 d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle shadow-sm"
                                style="width: 90px; height: 90px; font-size: 2.75rem;">
                                <i class="bi {{ $service['icon'] }}"></i>
                            </div>
                            <h5 class="fw-bold mb-2 fs-4" style="color: var(--ink);">{{ $service['title'] }}</h5>
                            <p class="text-muted small px-2 mb-0">{{ $service['desc'] }}</p>
                        </div>

                        <div class="mt-4 pt-3">
                            @if ($service['available'])
                                <a href="{{ $service['url'] }}" class="btn btn-primary w-100 py-2">
                                    Open Module <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            @else
                                <span class="badge rounded-pill bg-secondary w-100 py-2">Coming soon</span>
                            @endif
                        </div>

                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-warning text-center py-4" role="alert">
                        <i class="bi bi-exclamation-triangle fs-3 d-block mb-2"></i>
                        You currently do not have access permissions assigned to any modules. Please contact your
                        administrator.
                    </div>
                </div>
            @endforelse
        </div>
    </div>
@endsection
