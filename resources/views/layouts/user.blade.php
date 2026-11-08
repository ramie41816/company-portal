@extends('layouts.app')

@section('content')
    <div class="container-fluid px-4 pb-5 mb-md-0">
        <div class="row g-4">
            <div class="sticky-sidebar col-md-3 col-lg-2 d-none d-md-block">

                @if (auth()->user()->hasPermission('users.create') ||
                        auth()->user()->hasPermission('users.index') ||
                        auth()->user()->hasPermission('users.archives') ||
                        auth()->user()->hasPermission('users.dashboard'))
                    <nav class="side-nav mb-4">
                        <div class="side-title text-uppercase small fw-bold text-muted mb-2 px-2">User Management</div>

                        @if (auth()->user()->hasPermission('users.dashboard'))
                            <a href="{{ route('users.dashboard') }}"
                                class="nav-link {{ request()->routeIs('users.dashboard') ? 'active' : '' }}">
                                <i class="bi bi-speedometer2 me-2"></i> Dashboard
                            </a>
                        @endif

                        @if (auth()->user()->hasPermission('users.create'))
                            <a href="{{ route('users.create') }}"
                                class="nav-link {{ request()->routeIs('users.create') ? 'active' : '' }}">
                                <i class="bi bi-person-plus me-2"></i> Create User
                            </a>
                        @endif

                        @if (auth()->user()->hasPermission('users.index'))
                            <a href="{{ route('users.index') }}"
                                class="nav-link {{ request()->routeIs('users.index') ? 'active' : '' }}">
                                <i class="bi bi-people me-2"></i> Users List
                            </a>
                        @endif

                        @if (auth()->user()->hasPermission('users.archives'))
                            <a href="{{ route('users.archives') }}"
                                class="nav-link {{ request()->routeIs('users.archives') ? 'active' : '' }}">
                                <i class="bi bi-archive me-2"></i> Archived Users
                            </a>
                        @endif
                    </nav>
                @endif

                @if (auth()->user()->hasPermission('roles.index') || auth()->user()->hasPermission('permissions.manage'))
                    <nav class="side-nav">
                        <div class="side-title text-uppercase small fw-bold text-muted mb-2 px-2">Access Control</div>

                        @if (auth()->user()->hasPermission('roles.index'))
                            <a href="{{ route('roles.index') }}"
                                class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                                <i class="bi bi-shield-lock me-2"></i> Roles Management
                            </a>
                        @endif

                        @if (auth()->user()->hasPermission('permissions.manage'))
                            <a href="{{ route('permissions.index') }}"
                                class="nav-link {{ request()->routeIs('permissions.*') ? 'active' : '' }}">
                                <i class="bi bi-shield-check me-2"></i> Permissions Registry
                            </a>
                        @endif
                    </nav>
                @endif

                <a href="{{ route('home') }}" class="btn btn-link text-muted ps-1 mt-2 text-decoration-none">
                    <i class="bi bi-arrow-left me-1"></i> All services
                </a>
            </div>

            <div class="col-12 col-md-9 col-lg-10">
                @yield('user_content')
            </div>
        </div>
    </div>

    <nav class="navbar fixed-bottom bg-body border-top shadow-sm d-md-none py-2 px-3">
        <div class="container-fluid justify-content-around p-0">
            @if (auth()->user()->hasPermission('users.dashboard'))
                <a href="{{ route('users.dashboard') }}"
                    class="nav-link d-flex flex-column align-items-center {{ request()->routeIs('users.dashboard') ? 'text-primary fw-bold' : 'text-muted' }}"
                    style="font-size: 0.75rem;">
                    <i class="bi bi-speedometer2 fs-5 mb-1"></i>
                    <span>Dashboard</span>
                </a>
            @endif

            @if (auth()->user()->hasPermission('users.create'))
                <a href="{{ route('users.create') }}"
                    class="nav-link d-flex flex-column align-items-center {{ request()->routeIs('users.create') ? 'text-primary fw-bold' : 'text-muted' }}"
                    style="font-size: 0.75rem;">
                    <i class="bi bi-person-plus fs-5 mb-1"></i>
                    <span>Create</span>
                </a>
            @endif

            @if (auth()->user()->hasPermission('users.index'))
                <a href="{{ route('users.index') }}"
                    class="nav-link d-flex flex-column align-items-center {{ request()->routeIs('users.index', 'users.show', 'users.edit') ? 'text-primary fw-bold' : 'text-muted' }}"
                    style="font-size: 0.75rem;">
                    <i class="bi bi-people fs-5 mb-1"></i>
                    <span>Users</span>
                </a>
            @endif

            @if (auth()->user()->hasPermission('users.archives'))
                <a href="{{ route('users.archives') }}"
                    class="nav-link d-flex flex-column align-items-center {{ request()->routeIs('users.archives') ? 'text-primary fw-bold' : 'text-muted' }}"
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
