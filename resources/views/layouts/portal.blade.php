@extends('layouts.app')

@section('content')
    <div class="container-fluid px-3 px-md-4 pb-5 mb-md-0">
        <div class="row g-4">
            {{-- Desktop Sidebar Navigation --}}
            <div class="sticky-sidebar col-md-3 col-lg-2 d-none d-md-block">

                @if (auth()->user()->hasPermission('portal.add') ||
                        auth()->user()->hasPermission('portal.view') ||
                        auth()->user()->hasPermission('portal.update'))
                    <nav class="side-nav">
                        <div class="side-title text-uppercase small fw-bold text-muted mb-3 px-2"
                            style="letter-spacing: .05em;">
                            Portal Management
                        </div>

                        {{-- Dashboard --}}
                        @if (auth()->user()->hasPermission('portal.view'))
                            <a href="{{ route('portal.dashboard') }}"
                                class="nav-link {{ request()->routeIs('portal.dashboard') ? 'active' : '' }}">
                                <i class="bi bi-speedometer2 me-2"></i> Dashboard
                            </a>
                        @endif

                        {{-- Services Sub-Module --}}
                        <div class="nav-group mt-3">
                            <div class="nav-group-title small fw-bold text-muted px-2 mb-1 d-flex align-items-center">
                                <i class="bi bi-tools me-2 text-primary"></i> Services
                            </div>
                            <div class="ps-3 d-flex flex-column gap-1">
                                @if (auth()->user()->hasPermission('portal.add'))
                                    <a href="{{ route('portal.services.create') }}"
                                        class="nav-link small py-1 px-2 rounded {{ request()->routeIs('portal.services.create') ? 'active' : '' }}">
                                        <i class="bi bi-plus-circle me-2"></i> Add Service
                                    </a>
                                @endif

                                @if (auth()->user()->hasPermission('portal.view'))
                                    <a href="{{ route('portal.services.index') }}"
                                        class="nav-link small py-1 px-2 rounded {{ request()->routeIs('portal.services.index', 'portal.services.show', 'portal.services.edit') ? 'active' : '' }}">
                                        <i class="bi bi-grid me-2"></i> Services List
                                    </a>
                                @endif

                                @if (auth()->user()->hasPermission('portal.view'))
                                    <a href="{{ route('portal.services.archives') }}"
                                        class="nav-link small py-1 px-2 rounded {{ request()->routeIs('portal.services.archives') ? 'active' : '' }}">
                                        <i class="bi bi-archive me-2"></i> Archived Services
                                    </a>
                                @endif
                            </div>
                        </div>

                        {{-- Inquiries Sub-Module --}}
                        <div class="nav-group mt-3">
                            <div class="nav-group-title small fw-bold text-muted px-2 mb-1 d-flex align-items-center">
                                <i class="bi bi-envelope-paper me-2 text-success"></i> Inquiries
                            </div>
                            <div class="ps-3 d-flex flex-column gap-1">
                                @if (auth()->user()->hasPermission('portal.view'))
                                    <a href="{{ route('portal.inquiries.index') }}"
                                        class="nav-link small py-1 px-2 rounded {{ request()->routeIs('portal.inquiries.index') && !request()->has('status') ? 'active' : '' }}">
                                        <i class="bi bi-inbox me-2"></i> All Inquiries
                                    </a>

                                    <a href="{{ route('portal.inquiries.index', ['status' => 'pending']) }}"
                                        class="nav-link small py-1 px-2 rounded {{ request()->query('status') === 'pending' ? 'active' : '' }}">
                                        <i class="bi bi-clock-history me-2"></i> Pending
                                    </a>

                                    <a href="{{ route('portal.inquiries.archives') }}"
                                        class="nav-link small py-1 px-2 rounded {{ request()->routeIs('portal.inquiries.archives') ? 'active' : '' }}">
                                        <i class="bi bi-archive me-2"></i> Archived Inquiries
                                    </a>
                                @endif
                            </div>
                        </div>
                    </nav>
                @endif

                <a href="{{ route('home') }}" class="btn btn-link text-muted ps-1 mt-2 text-decoration-none">
                    <i class="bi bi-arrow-left me-1"></i> Back to Home
                </a>
            </div>

            {{-- Main Body Content --}}
            <div class="col-12 col-md-9 col-lg-10">
                @yield('portal_content')
            </div>
        </div>
    </div>

    <!-- Mobile Navigation Fixed Bottom Bar -->
    <nav class="navbar fixed-bottom border-top shadow-sm d-md-none py-2 px-3"
        style="background: var(--card); border-color: var(--line) !important;">
        <div class="container-fluid justify-content-around p-0">
            @if (auth()->user()->hasPermission('portal.view'))
                <a href="{{ route('portal.dashboard') }}"
                    class="nav-link d-flex flex-column align-items-center {{ request()->routeIs('portal.dashboard') ? 'text-primary fw-bold' : 'text-muted' }}"
                    style="font-size: 0.725rem;">
                    <i class="bi bi-speedometer2 fs-5 mb-1"></i>
                    <span>Dashboard</span>
                </a>
            @endif

            @if (auth()->user()->hasPermission('portal.view'))
                <a href="{{ route('portal.services.index') }}"
                    class="nav-link d-flex flex-column align-items-center {{ request()->routeIs('portal.services.*') ? 'text-primary fw-bold' : 'text-muted' }}"
                    style="font-size: 0.725rem;">
                    <i class="bi bi-tools fs-5 mb-1"></i>
                    <span>Services</span>
                </a>
            @endif

            @if (auth()->user()->hasPermission('portal.view'))
                <a href="{{ route('portal.inquiries.index') }}"
                    class="nav-link d-flex flex-column align-items-center {{ request()->routeIs('portal.inquiries.*') ? 'text-primary fw-bold' : 'text-muted' }}"
                    style="font-size: 0.725rem;">
                    <i class="bi bi-envelope-paper fs-5 mb-1"></i>
                    <span>Inquiries</span>
                </a>
            @endif

            <a href="{{ route('home') }}" class="nav-link d-flex flex-column align-items-center text-muted"
                style="font-size: 0.725rem;">
                <i class="bi bi-house fs-5 mb-1"></i>
                <span>Home</span>
            </a>
        </div>
    </nav>

    @stack('scripts')
@endsection
