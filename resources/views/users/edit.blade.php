@extends('layouts.user')

@section('user_content')
    <div class="page-head mb-4">
        <h2 class="mb-1 fw-bold" style="color: var(--ink);">Edit User Profile</h2>
        <div class="text-muted">Update account details, role access, and status configuration.</div>
    </div>

    {{-- Separate Security / Password Reset Card --}}
    <div class="card border-0 shadow-sm mb-4"
        style="background: var(--card); border: 1px solid var(--line); border-radius: 1rem;">
        <div class="card-body p-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div>
                <h5 class="fw-semibold mb-1" style="color: var(--ink);">
                    <i class="bi bi-shield-lock me-2 text-warning"></i> Password Security
                </h5>
                <p class="text-muted small mb-0">Generate a new 10-character temporary password and email it directly to the
                    user.</p>
            </div>

            <form action="{{ route('users.reset-password', $user) }}" method="POST" id="resetPasswordForm">
                @csrf
                @method('PUT')
                {{-- Removed text-dark, keeping it clean with warning outline/solid styling that fits both modes --}}
                <button type="submit" class="btn btn-warning fw-medium px-4 text-nowrap shadow-sm">
                    <i class="bi bi-key me-1"></i> Send Temporary Password
                </button>
            </form>
        </div>
    </div>

    <form action="{{ route('users.update', $user) }}" method="POST" id="editUserForm">
        @csrf
        @method('PUT')

        {{-- Personal Information Panel --}}
        <div class="card border-0 shadow-sm mb-4"
            style="background: var(--card); border: 1px solid var(--line); border-radius: 1rem;">
            <div class="card-body p-4">
                <h5 class="fw-semibold mb-3 pb-2 border-bottom"
                    style="color: var(--ink); border-color: var(--line) !important;">
                    <i class="bi bi-person me-2 text-primary"></i> Personal Information
                </h5>

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
                        <input type="text" name="last_name" value="{{ old('last_name', $user->userInfo?->last_name) }}"
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
                                {{ old('gender', $user->userInfo?->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                            <option value="Other"
                                {{ old('gender', $user->userInfo?->gender) == 'Other' ? 'selected' : '' }}>Other</option>
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
                </div>
            </div>
        </div>

        {{-- Account Credentials & Access Panel --}}
        <div class="card border-0 shadow-sm mb-4"
            style="background: var(--card); border: 1px solid var(--line); border-radius: 1rem;">
            <div class="card-body p-4">
                <h5 class="fw-semibold mb-3 pb-2 border-bottom"
                    style="color: var(--ink); border-color: var(--line) !important;">
                    <i class="bi bi-shield-lock me-2 text-primary"></i> Account Credentials & Access
                </h5>

                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label small fw-semibold text-muted">Email Address *</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}"
                            class="form-control @error('email') is-invalid @enderror" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-semibold text-muted">Access Level (RBAC) *</label>
                        <select name="access_level" class="form-select @error('access_level') is-invalid @enderror"
                            required>
                            <option value="staff"
                                {{ old('access_level', $user->access_level) == 'staff' ? 'selected' : '' }}>Staff</option>
                            <option value="manager"
                                {{ old('access_level', $user->access_level) == 'manager' ? 'selected' : '' }}>Manager
                            </option>
                            <option value="admin"
                                {{ old('access_level', $user->access_level) == 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                        @error('access_level')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-semibold text-muted">Account Status *</label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>Active
                            </option>
                            <option value="inactive" {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>
                                Inactive</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Actions --}}
        <div class="d-flex justify-content-end gap-2 mb-4">
            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
            <button type="submit" class="btn btn-primary px-4">
                <i class="bi bi-check2-circle me-1"></i> Update User
            </button>
        </div>
    </form>

    <script>
        document.getElementById('editUserForm').addEventListener('submit', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Update user profile?',
                text: 'Do you want to save these changes to the user account?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2b3a67',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, update changes!',
                cancelButtonText: 'Cancel',
                customClass: {
                    popup: 'rounded-4 shadow'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Processing...',
                        text: 'Please wait while we save your changes.',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
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

        document.getElementById('resetPasswordForm').addEventListener('submit', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Send new temporary password?',
                text: 'This will instantly invalidate the user\'s current password and email them a fresh temporary one.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, send temporary password!',
                cancelButtonText: 'Cancel',
                customClass: {
                    popup: 'rounded-4 shadow'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Sending Email...',
                        text: 'Please wait while we generate and dispatch the credentials.',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
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
@endsection
