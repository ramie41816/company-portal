@extends('layouts.app')

@section('content')
    <div class="container-fluid px-4 pb-5 mb-md-0">
        <div class="row g-4">
            {{-- Desktop Sidebar Navigation --}}
            <div class="sticky-sidebar col-md-3 col-lg-2 d-none d-md-block">

                @if (auth()->user()->hasPermission('portal.view') || auth()->user()->hasPermission('portal.add'))
                    <nav class="side-nav mb-4">
                        <div class="side-title text-uppercase small fw-bold text-muted mb-2 px-2">Portal Management</div>

                        @if (auth()->user()->hasPermission('portal.view'))
                            <a href="{{ route('portal.dashboard') }}"
                                class="nav-link {{ request()->routeIs('portal.dashboard') ? 'active' : '' }}">
                                <i class="bi bi-speedometer2 me-2"></i> Dashboard
                            </a>
                        @endif

                        @if (auth()->user()->hasPermission('portal.add'))
                            <a href="{{ route('portal.services.create') }}"
                                class="nav-link {{ request()->routeIs('portal.services.create') ? 'active' : '' }}">
                                <i class="bi bi-plus-circle me-2"></i> Add Service
                            </a>
                        @endif

                        @if (auth()->user()->hasPermission('portal.view'))
                            <a href="{{ route('portal.services.index') }}"
                                class="nav-link {{ request()->routeIs('portal.services.index', 'portal.services.show', 'portal.services.edit') ? 'active' : '' }}">
                                <i class="bi bi-grid me-2"></i> Services List
                            </a>

                            <a href="{{ route('portal.services.archives') }}"
                                class="nav-link {{ request()->routeIs('portal.services.archives') ? 'active' : '' }}">
                                <i class="bi bi-archive me-2"></i> Archived Services
                            </a>
                        @endif
                    </nav>
                @endif

                @if (auth()->user()->hasPermission('portal.view'))
                    @php
                        $pendingCount = \App\Services\PortalService::getPendingInquiriesCount();
                    @endphp
                    <nav class="side-nav">
                        <div class="side-title text-uppercase small fw-bold text-muted mb-2 px-2">Inquiries</div>

                        <a href="{{ route('portal.inquiries.index') }}"
                            class="nav-link {{ request()->routeIs('portal.inquiries.index') && !request()->has('status') ? 'active' : '' }}">
                            <i class="bi bi-inbox me-2"></i> All Inquiries

                            @if ($pendingCount > 0)
                                <span class="badge rounded-pill bg-warning text-dark" style="font-size: 0.7rem;">
                                    {{ $pendingCount }}
                                </span>
                            @endif
                        </a>

                        <a href="{{ route('portal.inquiries.archives') }}"
                            class="nav-link {{ request()->routeIs('portal.inquiries.archives') ? 'active' : '' }}">
                            <i class="bi bi-archive me-2"></i> Archived Inquiries
                        </a>
                    </nav>
                @endif

                <a href="{{ route('home') }}" class="btn btn-link text-muted ps-1 mt-2 text-decoration-none">
                    <i class="bi bi-arrow-left me-1"></i> All services
                </a>
            </div>

            {{-- Main Body Content --}}
            <div class="col-12 col-md-9 col-lg-10">
                @yield('portal_content')
            </div>
        </div>
    </div>

    {{-- Mobile Navigation Fixed Bottom Bar --}}
    <nav class="navbar fixed-bottom bg-body border-top shadow-sm d-md-none py-2 px-3">
        <div class="container-fluid justify-content-around p-0">
            @if (auth()->user()->hasPermission('portal.view'))
                <a href="{{ route('portal.dashboard') }}"
                    class="nav-link d-flex flex-column align-items-center {{ request()->routeIs('portal.dashboard') ? 'text-primary fw-bold' : 'text-muted' }}"
                    style="font-size: 0.75rem;">
                    <i class="bi bi-speedometer2 fs-5 mb-1"></i>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('portal.services.index') }}"
                    class="nav-link d-flex flex-column align-items-center {{ request()->routeIs('portal.services.*') ? 'text-primary fw-bold' : 'text-muted' }}"
                    style="font-size: 0.75rem;">
                    <i class="bi bi-grid fs-5 mb-1"></i>
                    <span>Services</span>
                </a>

                <a href="{{ route('portal.inquiries.index') }}"
                    class="nav-link d-flex flex-column align-items-center {{ request()->routeIs('portal.inquiries.*') ? 'text-primary fw-bold' : 'text-muted' }}"
                    style="font-size: 0.75rem;">
                    <i class="bi bi-envelope-paper fs-5 mb-1"></i>
                    <span>Inquiries</span>
                </a>
            @endif

            <a href="{{ route('home') }}" class="nav-link d-flex flex-column align-items-center text-muted"
                style="font-size: 0.75rem;">
                <i class="bi bi-house fs-5 mb-1"></i>
                <span>Services</span>
            </a>
        </div>
    </nav>
@endsection
