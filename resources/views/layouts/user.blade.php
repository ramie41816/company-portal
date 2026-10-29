@extends('layouts.app')

@section('content')
    <div class="container-fluid px-4">
        <div class="row g-4">
            <div class="col-md-3 col-lg-2">

                {{-- Card 1: User Management --}}
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

                {{-- Card 2: Access Control --}}
                @if (auth()->user()->hasPermission('roles.index'))
                    <nav class="side-nav">
                        <div class="side-title text-uppercase small fw-bold text-muted mb-2 px-2">Access Control</div>
                        <a href="{{ route('roles.index') }}"
                            class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                            <i class="bi bi-shield-lock me-2"></i> Roles & Permissions
                        </a>
                    </nav>
                @endif

                <a href="{{ route('home') }}" class="btn btn-link text-muted ps-1 mt-2 text-decoration-none">
                    <i class="bi bi-arrow-left me-1"></i> All services
                </a>
            </div>

            <div class="col-md-9 col-lg-10">
                @yield('user_content')
            </div>
        </div>
    </div>
    @stack('scripts')
@endsection
