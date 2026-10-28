@extends('layouts.app')

@section('content')
    <div class="container-fluid px-4">
        <div class="row g-4">
            <div class="col-md-3 col-lg-2">
                <nav class="side-nav">
                    <div class="side-title">User Management</div>
                    <a href="{{ route('users.create') }}" class="{{ request()->routeIs('users.create') ? 'active' : '' }}">
                        <i class="bi bi-person-plus"></i> Create User
                    </a>
                    <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.index') ? 'active' : '' }}">
                        <i class="bi bi-people"></i> Users List
                    </a>
                    <a href="{{ route('users.archives') }}"
                        class="{{ request()->routeIs('users.archives') ? 'active' : '' }}">
                        <i class="bi bi-archive"></i> Archived Users
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
