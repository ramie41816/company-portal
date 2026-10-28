@extends('layouts.user')

@section('user_content')
    <div class="page-head mb-4">
        <h2 class="mb-1 fw-bold" style="color: var(--ink);">Create New User</h2>
        <div class="text-muted">Add a new user account, configure role access, and set initial account status.</div>
    </div>

    <form action="{{ route('users.store') }}" method="POST" id="createUserForm">
        @csrf

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
                        <input type="text" name="first_name" value="{{ old('first_name') }}"
                            class="form-control @error('first_name') is-invalid @enderror" required>
                        @error('first_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3 col-sm-6">
                        <label class="form-label small fw-semibold text-muted">Middle Name</label>
                        <input type="text" name="middle_name" value="{{ old('middle_name') }}" class="form-control">
                    </div>

                    <div class="col-md-3 col-sm-6">
                        <label class="form-label small fw-semibold text-muted">Last Name *</label>
                        <input type="text" name="last_name" value="{{ old('last_name') }}"
                            class="form-control @error('last_name') is-invalid @enderror" required>
                        @error('last_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3 col-sm-6">
                        <label class="form-label small fw-semibold text-muted">Suffix</label>
                        <input type="text" name="suffix" value="{{ old('suffix') }}" class="form-control"
                            placeholder="Jr., III">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-semibold text-muted">Gender</label>
                        <select name="gender" class="form-select @error('gender') is-invalid @enderror">
                            <option value="">Select Gender</option>
                            <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                            <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('gender')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-semibold text-muted">Birthday</label>
                        <input type="date" name="birthday" value="{{ old('birthday') }}"
                            class="form-control @error('birthday') is-invalid @enderror">
                        @error('birthday')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Account & Access Panel --}}
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
                        <input type="email" name="email" value="{{ old('email') }}"
                            class="form-control @error('email') is-invalid @enderror" placeholder="name@example.com"
                            required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-semibold text-muted">Access Level (RBAC) *</label>
                        <select name="access_level" class="form-select @error('access_level') is-invalid @enderror"
                            required>
                            <option value="staff" {{ old('access_level') == 'staff' ? 'selected' : '' }}>Staff</option>
                            <option value="manager" {{ old('access_level') == 'manager' ? 'selected' : '' }}>Manager
                            </option>
                            <option value="admin" {{ old('access_level') == 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                        @error('access_level')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-semibold text-muted">Account Status *</label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active
                            </option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Auto-generated temporary password notice --}}
                    <div class="col-12 mt-3">
                        <div class="alert alert-info border-0 shadow-sm mb-0 d-flex align-items-center" role="alert">
                            <i class="bi bi-info-circle-fill fs-5 me-2"></i>
                            <div>
                                A secure temporary password will be <strong>auto-generated by the system</strong> and sent
                                directly to the user's email address upon creation.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Actions --}}
        <div class="d-flex justify-content-end gap-2 mb-4">
            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
            <button type="submit" class="btn btn-primary px-4">
                <i class="bi bi-check2-circle me-1"></i> Create User
            </button>
        </div>
    </form>
    <script>
        document.getElementById('createUserForm').addEventListener('submit', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Create new user account?',
                text: 'This will create the user and send a temporary password via email. Are you sure?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2b3a67',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, create user!',
                cancelButtonText: 'Cancel',
                customClass: {
                    popup: 'rounded-4 shadow'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Processing...',
                        text: 'Please wait while we create the account and send the email.',
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
