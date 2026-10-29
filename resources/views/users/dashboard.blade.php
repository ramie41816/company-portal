@extends('layouts.user')

@section('user_content')
    {{-- Page Header & Actions --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1" style="color: var(--ink);"><i class="bi bi-speedometer2 me-1"></i> User Management
                Dashboard</h3>
            <p class="text-muted small mb-0">Overview of system users, active staff accounts, roles, and recent
                registrations.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-list-ul me-1"></i> All Users
            </a>
            <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-person-plus me-1"></i> New User
            </a>
        </div>
    </div>

    {{-- Metrics Cards Row --}}
    <div class="row g-3 mb-4">
        {{-- Total Users --}}
        <div class="col-md-3 col-sm-6">
            <div class="panel p-3 h-100 d-flex flex-column justify-content-between"
                style="background: var(--card); border: 1px solid var(--line); border-radius: 1rem;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-uppercase small text-muted fw-semibold" style="letter-spacing: .08em;">Total
                        Users</span>
                    <div class="p-2 rounded-3 bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-people fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-0" style="color: var(--ink);">{{ number_format($totalUsers) }}</h3>
            </div>
        </div>

        {{-- Active Users --}}
        <div class="col-md-3 col-sm-6">
            <div class="panel p-3 h-100 d-flex flex-column justify-content-between"
                style="background: var(--card); border: 1px solid var(--line); border-radius: 1rem;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-uppercase small text-muted fw-semibold" style="letter-spacing: .08em;">Active
                        Accounts</span>
                    <div class="p-2 rounded-3 bg-success bg-opacity-10 text-success">
                        <i class="bi bi-person-check fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-0 text-success">{{ number_format($activeUsers) }}</h3>
            </div>
        </div>

        {{-- Inactive Users --}}
        <div class="col-md-3 col-sm-6">
            <div class="panel p-3 h-100 d-flex flex-column justify-content-between"
                style="background: var(--card); border: 1px solid var(--line); border-radius: 1rem;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-uppercase small text-muted fw-semibold" style="letter-spacing: .08em;">Inactive /
                        Archived</span>
                    <div class="p-2 rounded-3 bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-person-dash fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-0" style="color: var(--ink);">{{ number_format($inactiveUsers) }}</h3>
            </div>
        </div>

        {{-- System Roles --}}
        <div class="col-md-3 col-sm-6">
            <div class="panel p-3 h-100 d-flex flex-column justify-content-between"
                style="background: var(--card); border: 1px solid var(--line); border-radius: 1rem;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-uppercase small text-muted fw-semibold" style="letter-spacing: .08em;">Total
                        Roles</span>
                    <div class="p-2 rounded-3 bg-info bg-opacity-10 text-info">
                        <i class="bi bi-shield-lock fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-0" style="color: var(--ink);">{{ number_format($totalRoles) }}</h3>
            </div>
        </div>
    </div>

    {{-- Recent Users Panel --}}
    <div class="panel mb-4" style="background: var(--card); border: 1px solid var(--line); border-radius: 1rem;">
        <div class="p-4 border-bottom d-flex justify-content-between align-items-center"
            style="border-color: var(--line) !important;">
            <h6 class="fw-semibold mb-0" style="color: var(--ink);">Recently Registered Users</h6>
            <a href="{{ route('users.index') }}" class="small text-decoration-none fw-semibold">View All &rarr;</a>
        </div>
        <div class="table-responsive">
            <table class="table table-elegant align-middle mb-0" style="color: var(--ink);">
                <thead>
                    <tr>
                        <th class="px-3">Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Joined Date</th>
                        <th class="text-end px-3" style="width: 100px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentUsers as $user)
                        <tr>
                            <td class="fw-semibold px-3" style="color: var(--ink);">{{ $user->userInfo?->full_name ?? '—' }}
                            </td>
                            <td class="text-muted">{{ $user->email }}</td>
                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                    @forelse($user->roles as $role)
                                        <span class="badge px-2 py-1 shadow-sm"
                                            style="background-color: #0d6efd; color: #ffffff;">
                                            {{ $role->display_name }}
                                        </span>
                                    @empty
                                        <span class="badge bg-secondary text-white px-2 py-1">No Role</span>
                                    @endforelse
                                </div>
                            </td>
                            <td>
                                @if ($user->status === 'active')
                                    <span class="badge px-2 py-1 shadow-sm text-uppercase"
                                        style="background-color: #198754; color: #ffffff;">Active</span>
                                @else
                                    <span class="badge px-2 py-1 shadow-sm text-uppercase"
                                        style="background-color: #6c757d; color: #ffffff;">Inactive</span>
                                @endif
                            </td>
                            <td class="text-muted small">{{ $user->created_at->format('M d, Y') }}</td>
                            <td class="text-end px-3">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('users.show', $user->id) }}" class="btn btn-outline-primary"
                                        title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-outline-warning"
                                        title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                No users found. Get started by creating your first staff account!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
