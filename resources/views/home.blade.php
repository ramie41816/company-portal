@extends('layouts.app')

@section('content')
    @php
        $user = auth()->user();
        $services = [];

        if ($user->hasPermission('portal.view') || $user->hasPermission('portal.services.index')) {
            $pendingInquiries =
                $user->hasPermission('portal.view') && class_exists(\App\Services\PortalService::class)
                    ? \App\Services\PortalService::getPendingInquiriesCount()
                    : 0;

            $services[] = [
                'title' => 'Portal Management',
                'desc' => 'Manage public client website services, contents, and inquiries.',
                'icon' => 'bi-globe-americas',
                'url' => route('portal.dashboard'),
                'available' => true,
                'badge_count' => $pendingInquiries,
                'badge_label' => 'Pending',
                'badge_class' => 'bg-warning text-dark',
            ];
        }

        if ($user->hasPermission('quotations.view')) {
            $services[] = [
                'title' => 'Quotation',
                'desc' => 'Create, manage and send customer quotations.',
                'icon' => 'bi-file-earmark-text',
                'url' => route('quotations.dashboard'),
                'available' => true,
                'badge_count' => 0,
            ];
        }

        if ($user->hasPermission('schedules.view')) {
            $services[] = [
                'title' => 'Schedule',
                'desc' => 'Manage project schedules and track operations.',
                'icon' => 'bi-calendar-check',
                'url' => route('schedules.dashboard'),
                'available' => true,
                'badge_count' => 0,
            ];
        }

        if ($user->hasPermission('users.view')) {
            $services[] = [
                'title' => 'User Management',
                'desc' => 'Manage staff accounts and their access.',
                'icon' => 'bi-people',
                'url' => route('users.dashboard'),
                'available' => true,
                'badge_count' => 0,
            ];
        }

        if ($user->hasPermission('templates.manage')) {
            $services[] = [
                'title' => 'Template Manager',
                'desc' => 'Preview and Render System Templates.',
                'icon' => 'bi-file-earmark-code',
                'url' => route('templates.index'),
                'available' => true,
                'badge_count' => 0,
            ];
        }

        // My Profile service card
        $services[] = [
            'title' => 'My Profile',
            'desc' => 'Manage your personal details and account password.',
            'icon' => 'bi-person-badge',
            'url' => route('profile.show'),
            'available' => true,
            'badge_count' => 0,
        ];
    @endphp

    <style>
        .service-card {
            transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
        }

        .service-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.15) !important;
        }
    </style>

    <div class="container py-3 py-md-4 px-3 px-md-4">
        <div class="hero mb-4 mb-md-5 text-center text-md-start">
            @if (config('company.tagline'))
                <p class="text-uppercase small mb-1" style="letter-spacing: .12em;">
                    {{ config('company.tagline') }}
                </p>
            @endif
            <h1 class="h3 h-md-display-6 fw-bold mb-2">
                Welcome back, {{ optional(auth()->user()->userInfo)->first_name ?? (auth()->user()->name ?? 'there') }}
            </h1>
            <p class="small mb-0">What would you like to work on
                today?</p>
        </div>

        <div class="d-flex align-items-center justify-content-between mb-3 px-1">
            <h5 class="fw-semibold mb-0">Our Services</h5>
            <span class="small">
                {{ count($services) }} {{ \Illuminate\Support\Str::plural('module', count($services)) }}
            </span>
        </div>

        <div class="row g-3 g-md-4">
            @forelse ($services as $service)
                <div class="col-12 col-md-6 col-lg-4 d-flex">
                    <div class="service-card p-3 p-md-4 p-lg-5 d-flex flex-column text-center justify-content-between shadow-sm w-100 h-100"
                        style="background: var(--card, var(--bs-body-bg)); border: 1px solid var(--line, var(--bs-border-color)); border-radius: 1rem;">

                        <div>
                            <div class="service-icon mx-auto mb-3 mb-md-4 d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle shadow-sm"
                                style="width: 75px; height: 75px; font-size: 2.25rem;">
                                <i class="bi {{ $service['icon'] }}"></i>
                            </div>

                            <div class="d-inline-flex align-items-center justify-content-center gap-2 flex-wrap mb-2">
                                <h5 class="fw-bold mb-0 fs-5 fs-md-4">
                                    {{ $service['title'] }}
                                </h5>

                                @if (($service['badge_count'] ?? 0) > 0)
                                    <span class="badge rounded-pill {{ $service['badge_class'] ?? 'bg-warning text-dark' }}"
                                        style="font-size: 0.72rem;">
                                        {{ $service['badge_count'] }} {{ $service['badge_label'] ?? '' }}
                                    </span>
                                @endif
                            </div>

                            <p class="small px-2 mb-0" style="color: var(--muted, var(--bs-secondary-color));">
                                {{ $service['desc'] }}</p>
                        </div>

                        <div class="mt-3 mt-md-4 pt-2 pt-md-3">
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
