@extends('layouts.templates')

@section('template_content')
    <div class="card shadow-sm border-0 rounded-4 p-4" style="background: var(--card); color: var(--ink);">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h3 class="fw-bold mb-0">Template Viewer Dashboard</h3>
                <p class="text-muted small mb-0">Select a template from the left sidebar to preview it live.</p>
            </div>
        </div>

        @if (!empty($viewPath))
            <div class="position-relative border rounded overflow-hidden mt-3" style="min-height: 600px;">

                {{-- Loading Spinner Overlay --}}
                <div id="iframe-loader"
                    class="position-absolute top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center bg-white"
                    style="z-index: 10;">
                    <div class="text-center">
                        <div class="spinner-border text-primary mb-2" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <div class="text-muted small">Loading template preview...</div>
                    </div>
                </div>

                <div class="ratio ratio-16x9 w-100 h-100">
                    @php
                        $route = $type === 'pdf' ? 'templates.stream.pdf' : 'templates.render';
                    @endphp
                    <iframe id="template-iframe" src="{{ route($route, ['view' => $viewPath]) }}"
                        class="w-100 h-100 border-0 bg-white"></iframe>
                </div>
            </div>
        @else
            <div class="alert alert-info mt-3">
                <i class="bi bi-info-circle me-1"></i> Please pick a template from the navigation menu on the left to start
                inspecting.
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const iframe = document.getElementById('template-iframe');
                const loader = document.getElementById('iframe-loader');

                if (iframe && loader) {
                    // Hide loader once the iframe finishes loading
                    iframe.addEventListener('load', function() {
                        loader.style.opacity = '0';
                        setTimeout(() => loader.remove(), 300); // Smooth fade out
                    });

                    setTimeout(function() {
                        if (loader && loader.parentNode) {
                            loader.style.opacity = '0';
                            setTimeout(() => loader.remove(), 300);
                        }
                    }, 4000);
                }
            });
        </script>
    @endpush
@endsection
