@extends('layouts.portal')

@section('portal_content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center gap-2 mb-4">
        <a href="{{ route('portal.services.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to services
        </a>
        @if (auth()->user()->hasPermission('portal.update'))
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('portal.services.edit', $service->id) }}" class="btn btn-warning btn-sm w-100 w-md-auto">
                    <i class="bi bi-pencil-square me-1"></i> Edit Service
                </a>
            </div>
        @endif
    </div>

    <div class="panel border-0 shadow-sm mb-4"
        style="background: var(--card); color: var(--ink); border: 1px solid var(--line); border-radius: 1rem;">
        <div class="p-4 p-md-5">
            <div class="d-flex align-items-center flex-wrap gap-4 pb-4 mb-4 border-bottom"
                style="border-color: var(--line) !important;">
                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white bg-primary shadow-sm"
                    style="width: 70px; height: 70px; font-size: 1.8rem;">
                    <i class="bi {{ $service->icon_class ?? 'bi-tools' }}"></i>
                </div>
                <div>
                    <h3 class="mb-1 fw-bold" style="color: var(--ink);">{{ $service->title }}</h3>
                    <div class="d-flex flex-wrap align-items-center gap-2 mt-2">
                        <span class="badge bg-{{ $service->is_active ? 'success' : 'secondary' }} px-3 py-2">
                            Status: {{ $service->is_active ? 'Published' : 'Hidden' }}
                        </span>
                        <span class="badge bg-info text-dark px-3 py-2">Display Position: #{{ $service->sort_order }}</span>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-12 col-md-6">
                    <div class="card h-100 border shadow-none"
                        style="background: var(--card); border-color: var(--line) !important; border-radius: 0.75rem;">
                        <div class="card-body p-4">
                            <h6 class="text-uppercase small text-muted fw-semibold mb-3" style="letter-spacing: .08em;">
                                <i class="bi bi-file-text me-1 text-primary"></i> Service Overview
                            </h6>
                            <p class="small text-muted mb-3"><strong style="color: var(--ink);">Short
                                    Summary:</strong><br>{{ $service->short_description }}</p>
                            <p class="small text-muted mb-0"><strong style="color: var(--ink);">Full
                                    Details:</strong><br>{{ $service->full_description }}</p>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="card h-100 border shadow-none"
                        style="background: var(--card); border-color: var(--line) !important; border-radius: 0.75rem;">
                        <div class="card-body p-4">
                            <h6 class="text-uppercase small text-muted fw-semibold mb-3" style="letter-spacing: .08em;">
                                <i class="bi bi-list-check me-1 text-primary"></i> What's Included
                            </h6>
                            @if (!empty($service->features))
                                <ul class="small text-muted mb-0 ps-3">
                                    @foreach ($service->features as $feature)
                                        <li class="mb-1">{{ $feature }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <span class="text-muted small">No specific inclusions listed.</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @if (!empty($service->images))
                <h6 class="text-uppercase small text-muted fw-semibold mb-3" style="letter-spacing: .08em;">
                    <i class="bi bi-images me-1 text-primary"></i> Service Photos
                </h6>
                <div class="row g-3">
                    @foreach ($service->images as $img)
                        <div class="col-6 col-md-3">
                            <img src="{{ asset('storage/' . $img) }}"
                                class="w-100 rounded-3 border object-fit-cover shadow-sm"
                                style="height: 150px; border-color: var(--line) !important;" alt="Service Photo">
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
