@extends('layouts.app')

@section('content')
    <div class="container py-4">
        {{-- Fixed alignment: added d-flex justify-content-between align-items-center --}}
        <div class="page-head mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h2 class="mb-1 fw-bold" style="color: var(--ink);">My Profile</h2>
                <div class="text-muted">Manage your personal details and account security settings.</div>
            </div>
            <div class="btn-group">
                <a href="{{ route('home') }}" class="btn btn-outline-primary px-4">
                    <i class="bi bi-arrow-left me-1"></i> Back to Service List
                </a>
                <a href="{{ route('profile.show') }}" class="btn btn-outline-info px-4">
                    <i class="bi bi-eye me-1"></i> Back to View
                </a>
            </div>
        </div>

        {{-- Update Profile Form Card --}}
        <div class="card border-0 shadow-sm mb-4"
            style="background: var(--card); color: var(--ink); border: 1px solid var(--line) !important; border-radius: 1rem;">
            <div class="card-body p-4">
                <h5 class="fw-semibold mb-3 pb-2 border-bottom"
                    style="color: var(--ink); border-color: var(--line) !important;">
                    <i class="bi bi-person-badge me-2 text-primary"></i> Personal Information
                </h5>

                <form action="{{ route('profile.update') }}" method="POST" id="profileForm">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-3 col-sm-6">
                            <label class="form-label small fw-semibold text-muted">First Name *</label>
                            <input type="text" name="first_name"
                                value="{{ old('first_name', $user->userInfo?->first_name) }}"
                                class="form-control @error('first_name') is-invalid @enderror" required>
                            @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <label class="form-label small fw-semibold text-muted">Middle Name</label>
                            <input type="text" name="middle_name"
                                value="{{ old('middle_name', $user->userInfo?->middle_name) }}" class="form-control">
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <label class="form-label small fw-semibold text-muted">Last Name *</label>
                            <input type="text" name="last_name"
                                value="{{ old('last_name', $user->userInfo?->last_name) }}"
                                class="form-control @error('last_name') is-invalid @enderror" required>
                            @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <label class="form-label small fw-semibold text-muted">Suffix</label>
                            <input type="text" name="suffix" value="{{ old('suffix', $user->userInfo?->suffix) }}"
                                class="form-control" placeholder="Jr., III">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Gender</label>
                            <select name="gender" class="form-select @error('gender') is-invalid @enderror">
                                <option value="">Select Gender</option>
                                <option value="Male"
                                    {{ old('gender', $user->userInfo?->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female"
                                    {{ old('gender', $user->userInfo?->gender) == 'Female' ? 'selected' : '' }}>Female
                                </option>
                                <option value="Other"
                                    {{ old('gender', $user->userInfo?->gender) == 'Other' ? 'selected' : '' }}>Other
                                </option>
                            </select>
                            @error('gender')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Birthday</label>
                            <input type="date" name="birthday" value="{{ old('birthday', $user->userInfo?->birthday) }}"
                                class="form-control @error('birthday') is-invalid @enderror">
                            @error('birthday')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12">
                            <label class="form-label small fw-semibold text-muted">Email Address *</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                class="form-control @error('email') is-invalid @enderror" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Update Password Form Card --}}
        <div class="card border-0 shadow-sm mb-4"
            style="background: var(--card); color: var(--ink); border: 1px solid var(--line) !important; border-radius: 1rem;">
            <div class="card-body p-4">
                <h5 class="fw-semibold mb-3 pb-2 border-bottom"
                    style="color: var(--ink); border-color: var(--line) !important;">
                    <i class="bi bi-shield-lock me-2 text-primary"></i> Change Password
                </h5>

                <form action="{{ route('profile.password') }}" method="POST" id="passwordForm">
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

    @push('scripts')
        <script>
            document.getElementById('profileForm').addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Save profile changes?',
                    text: 'Do you want to update your personal information?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#2b3a67',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, update profile!',
                    cancelButtonText: 'Cancel',
                    customClass: {
                        popup: 'rounded-4 shadow'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Processing...',
                            text: 'Please wait while we update your profile.',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                            },
                            customClass: {
                                popup: 'rounded-4 shadow'
                            }
                        });
                        e.target.submit();
                    }
                });
            });

            document.getElementById('passwordForm').addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Change password?',
                    text: 'Are you sure you want to update your account password?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ffc107',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, change password!',
                    cancelButtonText: 'Cancel',
                    customClass: {
                        popup: 'rounded-4 shadow'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Updating...',
                            text: 'Please wait while we secure your account.',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                            },
                            customClass: {
                                popup: 'rounded-4 shadow'
                            }
                        });
                        e.target.submit();
                    }
                });
            });
        </script>
    @endpush
@endsection
