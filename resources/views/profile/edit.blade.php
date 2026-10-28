@extends('layouts.app')

@section('content')
    <div class="container py-4" style="max-width: 800px;">
        <div class="page-head mb-4">
            <h2 class="mb-1 fw-bold" style="color: var(--ink);">My Profile</h2>
            <div class="text-muted">Manage your personal details and account security settings.</div>
        </div>

        @if (session('success'))
            <div class="alert alert-success border-0 shadow-sm mb-4">{{ session('success') }}</div>
        @endif

        {{-- Update Profile Form --}}
        <div class="card border-0 shadow-sm mb-4"
            style="background: var(--card); border: 1px solid var(--line); border-radius: 1rem;">
            <div class="card-body p-4">
                <h5 class="fw-semibold mb-3 pb-2 border-bottom"
                    style="color: var(--ink); border-color: var(--line) !important;">
                    <i class="bi bi-person-badge me-2 text-primary"></i> Personal Information
                </h5>

                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-muted">First Name *</label>
                            <input type="text" name="first_name"
                                value="{{ old('first_name', $user->userInfo?->first_name) }}"
                                class="form-control @error('first_name') is-invalid @enderror" required>
                            @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-muted">Middle Name</label>
                            <input type="text" name="middle_name"
                                value="{{ old('middle_name', $user->userInfo?->middle_name) }}" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-muted">Last Name *</label>
                            <input type="text" name="last_name"
                                value="{{ old('last_name', $user->userInfo?->last_name) }}"
                                class="form-control @error('last_name') is-invalid @enderror" required>
                            @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Email Address *</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                class="form-control @error('email') is-invalid @enderror" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Birthday</label>
                            <input type="date" name="birthday" value="{{ old('birthday', $user->userInfo?->birthday) }}"
                                class="form-control">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Update Password Form --}}
        <div class="card border-0 shadow-sm mb-4"
            style="background: var(--card); border: 1px solid var(--line); border-radius: 1rem;">
            <div class="card-body p-4">
                <h5 class="fw-semibold mb-3 pb-2 border-bottom"
                    style="color: var(--ink); border-color: var(--line) !important;">
                    <i class="bi bi-shield-lock me-2 text-primary"></i> Change Password
                </h5>

                <form action="{{ route('profile.password') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-muted">Current Password *</label>
                            <input type="password" name="current_password"
                                class="form-control @error('current_password') is-invalid @enderror" required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">New Password *</label>
                            <input type="password" name="password"
                                class="form-control @error('password') is-invalid @enderror" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Confirm New Password *</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-warning text-dark px-4 fw-medium">Update Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
