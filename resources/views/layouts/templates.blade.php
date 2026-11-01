@extends('layouts.app')

@section('content')
    <div class="container-fluid px-4">
        <div class="row g-4">
            <div class="col-md-3 col-lg-2">

                {{-- Utilize the template groups passed from the controller --}}
                @php
                    $navGroups = $groupedTemplates ?? ($sidebarGroups ?? []);
                @endphp

                {{-- Dynamic Template Navigation Groups --}}
                @forelse ($navGroups as $category => $items)
                    <nav class="side-nav mb-4">
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
                    <div class="text-muted small px-2 mb-4">No template groups found.</div>
                @endforelse

                <a href="{{ route('home') }}" class="btn btn-link text-muted ps-1 mt-2 text-decoration-none">
                    <i class="bi bi-arrow-left me-1"></i> All services
                </a>
            </div>

            <div class="col-md-9 col-lg-10">
                @yield('template_content')
            </div>
        </div>
    </div>
    @stack('scripts')
@endsection
