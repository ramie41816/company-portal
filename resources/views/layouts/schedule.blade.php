@extends('layouts.app')

@section('content')
    <div class="container-fluid px-4">
        <div class="row g-4">

            {{-- Sidebar --}}
            <div class="col-md-3 col-lg-2">
                <nav class="side-nav">
                    <div class="side-title">Schedules</div>

                    <a href="{{ route('schedules.dashboard') }}"
                        class="{{ request()->routeIs('schedules.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>

                    <a href="{{ route('schedules.create') }}"
                        class="{{ request()->routeIs('schedules.create', 'schedules.from-quote') ? 'active' : '' }}">
                        <i class="bi bi-plus-circle"></i> Create Schedule
                    </a>

                    <a href="{{ route('schedules.index') }}"
                        class="{{ request()->routeIs('schedules.index', 'schedules.show', 'schedules.edit') ? 'active' : '' }}">
                        <i class="bi bi-list-ul"></i> Schedule List
                    </a>

                    <a href="{{ route('schedules.archives') }}"
                        class="{{ request()->routeIs('schedules.archives') ? 'active' : '' }}">
                        <i class="bi bi-archive"></i> Archived Schedules
                    </a>
                </nav>
                <a href="{{ route('home') }}" class="btn btn-link text-muted ps-1 mt-2 text-decoration-none">
                    <i class="bi bi-arrow-left me-1"></i> All services
                </a>
            </div>

            {{-- Page content --}}
            <div class="col-md-9 col-lg-10">
                @yield('schedule_content')
            </div>

        </div>
    </div>

    @stack('scripts')
@endsection
