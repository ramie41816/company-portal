@extends('layouts.app')

@section('content')
    <div class="container py-4">
        {{-- Page Header --}}
        <div
            class="page-head mb-4 d-flex flex-column flex-sm-row justify-content-between align-items-stretch align-items-sm-center gap-3">
            <div>
                <h2 class="mb-1 fw-bold fs-3" style="color: var(--ink);">User Profile</h2>
                <div class="text-muted small">Overview of your personal details and account access configuration.</div>
            </div>
            <div class="d-flex flex-column flex-sm-row gap-2">
                <a href="{{ route('home') }}" class="btn btn-outline-secondary px-3 text-nowrap">
                    <i class="bi bi-arrow-left me-1"></i> Go to Services
                </a>
                <a href="{{ route('profile.edit') }}" class="btn btn-primary px-3 text-nowrap">
                    <i class="bi bi-pencil me-1"></i> Edit Profile
                </a>
            </div>
        </div>

        {{-- Row with align-items-stretch to ensure equal container heights --}}
        <div class="row g-4 align-items-stretch">
            {{-- Left Column: User Summary & Status Card --}}
            <div class="col-12 col-lg-4 d-flex">
                <div class="card border-0 shadow-sm w-100 h-100"
                    style="background: var(--card); color: var(--ink); border: 1px solid var(--line) !important; border-radius: 1rem;">
                    <div class="card-body p-4 text-center d-flex flex-column align-items-center justify-content-center">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center mb-3 shadow-sm"
                            style="width: 80px; height: 80px; font-size: 2.25rem;">
                            <i class="bi bi-person-fill"></i>
                        </div>
                        <h4 class="fw-bold mb-1" style="color: var(--ink);">{{ $user->userInfo?->first_name }}
                            {{ $user->userInfo?->last_name }}</h4>
                        <p class="text-muted small mb-3">{{ $user->email }}</p>

                        <div class="d-flex flex-wrap justify-content-center gap-2 mb-3">
                            @if ($user->status === 'active')
                                <span
                                    class="badge bg-success bg-opacity-10 text-success border border-success-subtle px-3 py-1.5 text-uppercase fw-semibold small">Active</span>
                            @else
                                <span
                                    class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle px-3 py-1.5 text-uppercase fw-semibold small">Inactive</span>
                            @endif

                            @foreach ($user->roles as $role)
                                <span
                                    class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle px-3 py-1.5 text-uppercase fw-semibold small">{{ $role->display_name }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Read-Only Form Details --}}
            <div class="col-12 col-lg-8 d-flex">
                <div class="card border-0 shadow-sm w-100 h-100"
                    style="background: var(--card); color: var(--ink); border: 1px solid var(--line) !important; border-radius: 1rem;">
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div>
                            <h5 class="fw-semibold mb-3 pb-2 border-bottom fs-6"
                                style="color: var(--ink); border-color: var(--line) !important;">
                                <i class="bi bi-person-badge me-2 text-primary"></i> Personal Information
                            </h5>

                            <div class="row g-3">
                                <div class="col-12 col-sm-6">
                                    <label class="form-label small fw-semibold text-muted">First Name</label>
                                    <input type="text" class="form-control"
                                        style="background: var(--card); color: var(--ink); border-color: var(--line);"
                                        value="{{ $user->userInfo?->first_name ?? '—' }}" disabled>
                                </div>

                                <div class="col-12 col-sm-6">
                                    <label class="form-label small fw-semibold text-muted">Middle Name</label>
                                    <input type="text" class="form-control"
                                        style="background: var(--card); color: var(--ink); border-color: var(--line);"
                                        value="{{ $user->userInfo?->middle_name ?? '—' }}" disabled>
                                </div>

                                <div class="col-12 col-sm-6">
                                    <label class="form-label small fw-semibold text-muted">Last Name</label>
                                    <input type="text" class="form-control"
                                        style="background: var(--card); color: var(--ink); border-color: var(--line);"
                                        value="{{ $user->userInfo?->last_name ?? '—' }}" disabled>
                                </div>

                                <div class="col-12 col-sm-6">
                                    <label class="form-label small fw-semibold text-muted">Suffix</label>
                                    <input type="text" class="form-control"
                                        style="background: var(--card); color: var(--ink); border-color: var(--line);"
                                        value="{{ $user->userInfo?->suffix ?? '—' }}" disabled>
                                </div>

                                <div class="col-12 col-sm-6">
                                    <label class="form-label small fw-semibold text-muted">Gender</label>
                                    <input type="text" class="form-control"
                                        style="background: var(--card); color: var(--ink); border-color: var(--line);"
                                        value="{{ $user->userInfo?->gender ?? '—' }}" disabled>
                                </div>

                                <div class="col-12 col-sm-6">
                                    <label class="form-label small fw-semibold text-muted">Birthday</label>
                                    <input type="text" class="form-control"
                                        style="background: var(--card); color: var(--ink); border-color: var(--line);"
                                        value="{{ $user->userInfo?->birthday ? \Carbon\Carbon::parse($user->userInfo->birthday)->format('F d, Y') : '—' }}"
                                        disabled>
                                </div>

                                <div class="col-12">
                                    <label class="form-label small fw-semibold text-muted">Email Address</label>
                                    <input type="email" class="form-control"
                                        style="background: var(--card); color: var(--ink); border-color: var(--line);"
                                        value="{{ $user->email }}" disabled>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
