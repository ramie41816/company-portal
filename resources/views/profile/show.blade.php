@extends('layouts.app')

@section('content')
    <div class="container py-4" style="max-width: 800px;">
        <div class="page-head mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h2 class="mb-1 fw-bold" style="color: var(--ink);">User Profile Overview</h2>
                <div class="text-muted">Viewing personal details and account access configuration.</div>
            </div>

            @if (auth()->user()->hasPermission('profile.edit'))
                <a href="{{ route('profile.edit') }}" class="btn btn-outline-warning">
                    <i class="bi bi-pencil me-1"></i> Edit Profile
                </a>
            @endif
        </div>

        {{-- Profile Details Card --}}
        <div class="card border-0 shadow-sm mb-4"
            style="background: var(--card); color: var(--ink); border: 1px solid var(--line) !important; border-radius: 1rem;">
            <div class="card-body p-4">
                <h5 class="fw-semibold mb-3 pb-2 border-bottom"
                    style="color: var(--ink); border-color: var(--line) !important;">
                    <i class="bi bi-person-badge me-2 text-primary"></i> Personal Information
                </h5>

                <div class="row g-3">
                    <div class="col-md-3 col-sm-6">
                        <span class="d-block small fw-semibold text-muted">First Name</span>
                        <span class="fs-6 fw-medium"
                            style="color: var(--ink);">{{ $user->userInfo?->first_name ?? '—' }}</span>
                    </div>

                    <div class="col-md-3 col-sm-6">
                        <span class="d-block small fw-semibold text-muted">Middle Name</span>
                        <span class="fs-6 fw-medium"
                            style="color: var(--ink);">{{ $user->userInfo?->middle_name ?? '—' }}</span>
                    </div>

                    <div class="col-md-3 col-sm-6">
                        <span class="d-block small fw-semibold text-muted">Last Name</span>
                        <span class="fs-6 fw-medium"
                            style="color: var(--ink);">{{ $user->userInfo?->last_name ?? '—' }}</span>
                    </div>

                    <div class="col-md-3 col-sm-6">
                        <span class="d-block small fw-semibold text-muted">Suffix</span>
                        <span class="fs-6 fw-medium" style="color: var(--ink);">{{ $user->userInfo?->suffix ?? '—' }}</span>
                    </div>

                    <div class="col-md-6">
                        <span class="d-block small fw-semibold text-muted">Gender</span>
                        <span class="fs-6 fw-medium" style="color: var(--ink);">{{ $user->userInfo?->gender ?? '—' }}</span>
                    </div>

                    <div class="col-md-6">
                        <span class="d-block small fw-semibold text-muted">Birthday</span>
                        <span class="fs-6 fw-medium"
                            style="color: var(--ink);">{{ $user->userInfo?->birthday ? \Carbon\Carbon::parse($user->userInfo->birthday)->format('F d, Y') : '—' }}</span>
                    </div>

                    <div class="col-md-12">
                        <span class="d-block small fw-semibold text-muted">Email Address</span>
                        <span class="fs-6 fw-medium" style="color: var(--ink);">{{ $user->email }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Account Status & Security Card --}}
        <div class="card border-0 shadow-sm mb-4"
            style="background: var(--card); color: var(--ink); border: 1px solid var(--line) !important; border-radius: 1rem;">
            <div class="card-body p-4">
                <h5 class="fw-semibold mb-3 pb-2 border-bottom"
                    style="color: var(--ink); border-color: var(--line) !important;">
                    <i class="bi bi-shield-lock me-2 text-primary"></i> Account Access & Status
                </h5>

                <div class="row g-3">
                    <div class="col-6">
                        <span class="d-block small fw-semibold text-muted mb-2">Account Status</span>
                        @if ($user->status === 'active')
                            <span class="badge bg-success text-white px-3 py-2 text-uppercase fs-6 shadow-sm">Active</span>
                        @else
                            <span class="badge bg-danger text-white px-3 py-2 text-uppercase fs-6 shadow-sm">Inactive</span>
                        @endif
                    </div>

                    <div class="col-6">
                        <span class="d-block small fw-semibold text-muted mb-2">Assigned Role</span>
                        @forelse($user->roles as $role)
                            <span
                                class="badge bg-primary text-white text-uppercase px-3 py-2 fs-6 shadow-sm">{{ $role->display_name }}</span>
                        @empty
                            <span class="text-muted">No role assigned</span>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-start">
            <a href="{{ route('home') }}" class="btn btn-outline-secondary px-4">
                <i class="bi bi-arrow-left me-1"></i> Back to Service List
            </a>
        </div>
    </div>
@endsection
