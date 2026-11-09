@extends('layouts.templates')

@section('template_content')
    <div class="card shadow-sm border-0 rounded-4 p-3 p-md-4 mb-5 mb-md-0"
        style="background: var(--card); color: var(--ink);">
        <div
            class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-3">
            <div>
                <h3 class="fw-bold mb-0 fs-4 fs-md-3">Template Viewer Dashboard</h3>
                <p class="text-muted small mb-0">Select a template from the sidebar or dropdown to preview it live.</p>
            </div>

            {{-- Mobile Template Dropdown Selector --}}
            <div class="w-100 d-md-none">
                <label class="form-label small fw-semibold text-muted mb-1">Select Template to View:</label>
                <select id="mobileTemplateSelect"
                    class="form-select form-select-sm border-primary text-primary fw-bold bg-body"
                    onchange="if(this.value) window.location.href=this.value;">
                    <option value="">📁 Choose template...</option>
                    @php
                        $navGroupsMobile = $groupedTemplates ?? ($sidebarGroups ?? ($templates ?? []));
                    @endphp
                    @forelse ($navGroupsMobile as $category => $items)
                        <optgroup label="{{ strtoupper($category) }}">
                            @foreach ($items as $name => $viewPathOption)
                                @php
                                    $routeUrl = route('templates.index', [
                                        'view' => $viewPathOption,
                                        'type' => $category,
                                    ]);
                                @endphp
                                <option value="{{ $routeUrl }}"
                                    {{ request()->input('view') === $viewPathOption ? 'selected' : '' }}>
                                    {{ Str::headline($name) }}
                                </option>
                            @endforeach
                        </optgroup>
                    @empty
                        <option value="" disabled>No templates available</option>
                    @endforelse
                </select>
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

                <div class="ratio ratio-16x9 w-100 h-100" style="min-height: 600px;">
                    @php
                        $route = $type === 'pdf' ? 'templates.stream.pdf' : 'templates.render';
                    @endphp

                    <iframe id="template-iframe" src="{{ route($route, ['view' => $viewPath]) }}"
                        class="w-100 h-100 border-0 bg-white {{ $type === 'pdf' ? '' : 'pe-none' }}">
                    </iframe>
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
