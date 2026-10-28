@extends('layouts.app')

@section('content')
    <div class="container-fluid px-4">
        <div class="row g-4">

            {{-- Sidebar --}}
            <div class="col-md-3 col-lg-2">
                <nav class="side-nav">
                    <div class="side-title">Quotation</div>
                    <a href="{{ route('quotations.create') }}"
                        class="{{ request()->routeIs('quotations.create') ? 'active' : '' }}">
                        <i class="bi bi-plus-circle"></i> Create Quotation
                    </a>
                    <a href="{{ route('quotations.index') }}"
                        class="{{ request()->routeIs('quotations.index', 'quotations.show', 'quotations.edit') ? 'active' : '' }}">
                        <i class="bi bi-list-ul"></i> Quotation List
                    </a>
                    <a href="{{ route('quotations.archives') }}"
                        class="{{ request()->routeIs('quotations.archives') ? 'active' : '' }}">
                        <i class="bi bi-archive"></i> Archived Quotations
                    </a>
                </nav>
                <a href="{{ route('home') }}" class="btn btn-link text-muted ps-1 mt-2 text-decoration-none">
                    <i class="bi bi-arrow-left me-1"></i> All services
                </a>
            </div>

            {{-- Page content --}}
            <div class="col-md-9 col-lg-10">
                @yield('quotation_content')
            </div>

        </div>
    </div>

    @stack('scripts')
@endsection
