@extends('layouts.user')

@section('user_content')
    <div class="page-head mb-4">
        <h2 class="mb-1">Edit User</h2>
        <div class="sub">Update account details, role access, and status configuration.</div>
    </div>

    <div class="card border-0 shadow-sm" style="background: var(--card); border-radius: 0.75rem;">
        <div class="card-body p-4">
            <form action="{{ route('users.update', $user) }}" method="POST">
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
                        <input type="text" name="last_name" value="{{ old('last_name', $user->userInfo?->last_name) }}"
                            class="form-control @error('last_name') is-invalid @enderror" required>
                        @error('last_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-semibold text-muted">Suffix</label>
                        <input type="text" name="suffix" value="{{ old('suffix', $user->userInfo?->suffix) }}"
                            class="form-control">
                    </div>
                    <div class="col-md-4">
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
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold text-muted">Birthday</label>
                        <input type="date" name="birthday" value="{{ old('birthday', $user->userInfo?->birthday) }}"
                            class="form-control @error('birthday') is-invalid @enderror">
                        @error('birthday')
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

                    <div class="col-md-6"></div>

                    <div class="col-md-6">
                        <label class="form-label small fw-semibold text-muted">New Password <span
                                class="text-muted fw-normal">(Leave blank to keep current)</span></label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold text-muted">Confirm New Password</label>
                        <input type="password" name="password_confirmation" class="form-control">
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update User</button>
                </div>
            </form>
        </div>
    </div>
@endsection
