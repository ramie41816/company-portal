@extends('layouts.app')

@section('content')
    <div class="container-fluid px-4 pb-5 mb-md-0">
        <div class="row g-4">

            <div class="sticky-sidebar col-md-3 col-lg-2 d-none d-md-block">
                <nav class="side-nav">
                    <div class="side-title">Schedules</div>

                    @if (auth()->user()->hasPermission('schedules.view'))
                        <a href="{{ route('schedules.dashboard') }}"
                            class="{{ request()->routeIs('schedules.dashboard') ? 'active' : '' }}">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    @endif

                    @if (auth()->user()->hasPermission('schedules.add'))
                        <a href="{{ route('schedules.create') }}"
                            class="{{ request()->routeIs('schedules.create', 'schedules.from-quote') ? 'active' : '' }}">
                            <i class="bi bi-plus-circle"></i> Create Schedule
                        </a>
                    @endif

                    @if (auth()->user()->hasPermission('schedules.view'))
                        <a href="{{ route('schedules.index') }}"
                            class="{{ request()->routeIs('schedules.index', 'schedules.show', 'schedules.edit') ? 'active' : '' }}">
                            <i class="bi bi-list-ul"></i> Schedule List
                        </a>
                    @endif

                    @if (auth()->user()->hasPermission('schedules.view'))
                        <a href="{{ route('schedules.archives') }}"
                            class="{{ request()->routeIs('schedules.archives') ? 'active' : '' }}">
                            <i class="bi bi-archive"></i> Archived Schedules
                        </a>
                    @endif
                </nav>
                <a href="{{ route('home') }}" class="btn btn-link text-muted ps-1 mt-2 text-decoration-none">
                    <i class="bi bi-arrow-left me-1"></i> All services
                </a>
            </div>

            <div class="col-12 col-md-9 col-lg-10">
                @yield('schedule_content')
            </div>

        </div>
    </div>

    <nav class="navbar fixed-bottom bg-body border-top shadow-sm d-md-none py-2 px-3">
        <div class="container-fluid justify-content-around p-0">
            @if (auth()->user()->hasPermission('schedules.view'))
                <a href="{{ route('schedules.dashboard') }}"
                    class="nav-link d-flex flex-column align-items-center {{ request()->routeIs('schedules.dashboard') ? 'text-primary fw-bold' : 'text-muted' }}"
                    style="font-size: 0.75rem;">
                    <i class="bi bi-speedometer2 fs-5 mb-1"></i>
                    <span>Dashboard</span>
                </a>
            @endif

            @if (auth()->user()->hasPermission('schedules.add'))
                <a href="{{ route('schedules.create') }}"
                    class="nav-link d-flex flex-column align-items-center {{ request()->routeIs('schedules.create') ? 'text-primary fw-bold' : 'text-muted' }}"
                    style="font-size: 0.75rem;">
                    <i class="bi bi-plus-circle fs-5 mb-1"></i>
                    <span>Create</span>
                </a>
            @endif

            @if (auth()->user()->hasPermission('schedules.view'))
                <a href="{{ route('schedules.index') }}"
                    class="nav-link d-flex flex-column align-items-center {{ request()->routeIs('schedules.index', 'schedules.show', 'schedules.edit') ? 'text-primary fw-bold' : 'text-muted' }}"
                    style="font-size: 0.75rem;">
                    <i class="bi bi-list-ul fs-5 mb-1"></i>
                    <span>List</span>
                </a>
            @endif

            @if (auth()->user()->hasPermission('schedules.view'))
                <a href="{{ route('schedules.archives') }}"
                    class="nav-link d-flex flex-column align-items-center {{ request()->routeIs('schedules.archives') ? 'text-primary fw-bold' : 'text-muted' }}"
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
