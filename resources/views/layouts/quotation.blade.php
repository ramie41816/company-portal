@extends('layouts.app')

@section('content')
    <div class="container-fluid px-4 pb-5 mb-md-0">
        <div class="row g-4">

            {{-- Desktop Sidebar --}}
            <div class="sticky-sidebar col-md-3 col-lg-2 d-none d-md-block">
                <nav class="side-nav">
                    <div class="side-title">Quotation</div>

                    @if (auth()->user()->hasPermission('quotations.view'))
                        {{-- Dashboard Link --}}
                        <a href="{{ route('quotations.dashboard') }}"
                            class="{{ request()->routeIs('quotations.dashboard') ? 'active' : '' }}">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    @endif

                    @if (auth()->user()->hasPermission('quotations.add'))
                        {{-- Create Quotation Link --}}
                        <a href="{{ route('quotations.create') }}"
                            class="{{ request()->routeIs('quotations.create') ? 'active' : '' }}">
                            <i class="bi bi-plus-circle"></i> Create Quotation
                        </a>
                    @endif

                    @if (auth()->user()->hasPermission('quotations.view'))
                        {{-- Quotation List Link --}}
                        <a href="{{ route('quotations.index') }}"
                            class="{{ request()->routeIs('quotations.index', 'quotations.show', 'quotations.edit') ? 'active' : '' }}">
                            <i class="bi bi-list-ul"></i> Quotation List
                        </a>
                    @endif

                    @if (auth()->user()->hasPermission('quotations.view'))
                        {{-- Archived Quotations Link --}}
                        <a href="{{ route('quotations.archives') }}"
                            class="{{ request()->routeIs('quotations.archives') ? 'active' : '' }}">
                            <i class="bi bi-archive"></i> Archived Quotations
                        </a>
                    @endif
                </nav>
                <a href="{{ route('home') }}" class="btn btn-link text-muted ps-1 mt-2 text-decoration-none">
                    <i class="bi bi-arrow-left me-1"></i> All services
                </a>
            </div>

            {{-- Page content --}}
            <div class="col-12 col-md-9 col-lg-10">
                @yield('quotation_content')
            </div>

        </div>
    </div>

    {{-- Mobile Bottom Navigation Bar --}}
    <nav class="navbar fixed-bottom bg-body border-top shadow-sm d-md-none py-2 px-3">
        <div class="container-fluid justify-content-around p-0">
            @if (auth()->user()->hasPermission('quotations.view'))
                <a href="{{ route('quotations.dashboard') }}"
                    class="nav-link d-flex flex-column align-items-center {{ request()->routeIs('quotations.dashboard') ? 'text-primary fw-bold' : 'text-muted' }}"
                    style="font-size: 0.75rem;">
                    <i class="bi bi-speedometer2 fs-5 mb-1"></i>
                    <span>Dashboard</span>
                </a>
            @endif

            @if (auth()->user()->hasPermission('quotations.add'))
                <a href="{{ route('quotations.create') }}"
                    class="nav-link d-flex flex-column align-items-center {{ request()->routeIs('quotations.create') ? 'text-primary fw-bold' : 'text-muted' }}"
                    style="font-size: 0.75rem;">
                    <i class="bi bi-plus-circle fs-5 mb-1"></i>
                    <span>Create</span>
                </a>
            @endif

            @if (auth()->user()->hasPermission('quotations.view'))
                <a href="{{ route('quotations.index') }}"
                    class="nav-link d-flex flex-column align-items-center {{ request()->routeIs('quotations.index', 'quotations.show', 'quotations.edit') ? 'text-primary fw-bold' : 'text-muted' }}"
                    style="font-size: 0.75rem;">
                    <i class="bi bi-list-ul fs-5 mb-1"></i>
                    <span>List</span>
                </a>
            @endif

            @if (auth()->user()->hasPermission('quotations.view'))
                <a href="{{ route('quotations.archives') }}"
                    class="nav-link d-flex flex-column align-items-center {{ request()->routeIs('quotations.archives') ? 'text-primary fw-bold' : 'text-muted' }}"
                    style="font-size: 0.75rem;">
                    <i class="bi bi-archive fs-5 mb-1"></i>
                    <span>Archives</span>
                </a>
            @endif

            <a href="{{ route('home') }}" class="nav-link d-flex flex-column align-items-center text-muted"
                style="font-size: 0.75rem;">
                <i class="bi bi-house fs-5 mb-1"></i>
                <span>Services</span>
            </a>
        </div>
    </nav>

    @stack('scripts')
@endsection
