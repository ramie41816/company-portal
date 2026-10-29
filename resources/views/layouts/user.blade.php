@extends('layouts.app')

@section('content')
    <div class="container-fluid px-4">
        <div class="row g-4">
            <div class="col-md-3 col-lg-2">
                <!-- Card 1: User Management -->
                <nav class="side-nav mb-4">
                    <div class="side-title text-uppercase small fw-bold text-muted mb-2 px-2">User Management</div>
                    <a href="{{ route('users.create') }}"
                        class="nav-link {{ request()->routeIs('users.create') ? 'active' : '' }}">
                        <i class="bi bi-person-plus me-2"></i> Create User
                    </a>
                    <a href="{{ route('users.index') }}"
                        class="nav-link {{ request()->routeIs('users.index') ? 'active' : '' }}">
                        <i class="bi bi-people me-2"></i> Users List
                    </a>
                    <a href="{{ route('users.archives') }}"
                        class="nav-link {{ request()->routeIs('users.archives') ? 'active' : '' }}">
                        <i class="bi bi-archive me-2"></i> Archived Users
                    </a>
                </nav>

                <!-- Card 2: Access Control -->
                <nav class="side-nav">
                    <div class="side-title text-uppercase small fw-bold text-muted mb-2 px-2">Access Control</div>
                    <a href="{{ route('roles.index') }}"
                        class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                        <i class="bi bi-shield-lock me-2"></i> Roles & Permissions
                    </a>
                </nav>

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
