@extends('layouts.app')

@section('content')
    <div class="container-fluid px-4 pb-5 mb-md-0">
        <div class="row g-4">
            {{-- Desktop Sidebar --}}
            <div class="sticky-sidebar col-md-3 col-lg-2 d-none d-md-block">
                @php
                    $navGroups = $groupedTemplates ?? ($sidebarGroups ?? ($templates ?? []));
                @endphp

                @forelse ($navGroups as $category => $items)
                    <nav class="side-nav {{ !$loop->last ? 'mb-4' : '' }}">
                        <div class="side-title text-uppercase small fw-bold text-muted mb-2 px-2">
                            {{ ucfirst($category) }} Templates
                        </div>

                        @foreach ($items as $name => $viewPath)
                            <a href="{{ route('templates.index', ['view' => $viewPath, 'type' => $category]) }}"
                                class="nav-link {{ request()->input('view') === $viewPath ? 'active' : '' }}">
                                <i class="bi bi-file-earmark-code me-2"></i> {{ Str::headline($name) }}
                            </a>
                        @endforeach
                    </nav>
                @empty
                    <div class="text-muted small px-2 mb-4 p-3 border rounded bg-body-tertiary">
                        <i class="bi bi-exclamation-triangle me-1"></i> No template groups found.
                    </div>
                @endforelse

                <a href="{{ route('home') }}" class="btn btn-link text-muted ps-1 mt-2 text-decoration-none">
                    <i class="bi bi-arrow-left me-1"></i> All services
                </a>
            </div>

            {{-- Main Content Area --}}
            <div class="col-12 col-md-9 col-lg-10">
                @yield('template_content')
            </div>
        </div>
    </div>

    {{-- Mobile Bottom Navbar --}}
    <nav class="navbar fixed-bottom bg-body border-top shadow-sm d-md-none py-2 px-3 z-3">
        <div class="container-fluid justify-content-around p-0">
            <a href="{{ route('home') }}" class="nav-link d-flex flex-column align-items-center text-muted"
                style="font-size: 0.75rem;">
                <i class="bi bi-house fs-5 mb-1"></i>
                <span>Services</span>
            </a>
        </div>
    </nav>

    @stack('scripts')
@endsection
