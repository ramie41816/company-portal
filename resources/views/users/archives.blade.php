@extends('layouts.user')

@section('user_content')
    <div class="page-head mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h2 class="mb-1">Archived Users</h2>
            <div class="sub">View and manage inactive or archived system user accounts.</div>
        </div>
    </div>

    @if (session('status', session('success')))
        <div class="alert alert-success border-0 shadow-sm">{{ session('status', session('success')) }}</div>
    @endif

    {{-- Advanced Search & Filter Bar --}}
    <div class="card border-0 shadow-sm mb-4" style="background: var(--card); border-radius: 0.75rem;">
        <div class="card-body">
            <form action="{{ route('users.archives') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-7">
                    <label class="form-label small fw-semibold text-muted">Search keyword</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" value="{{ $search ?? '' }}" class="form-control border-start-0"
                            placeholder="Name, email...">
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-muted">Access Level</label>
                    <select name="access_level" class="form-select">
                        <option value="">All Access Levels</option>
                        <option value="admin" {{ ($accessLevel ?? '') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="manager" {{ ($accessLevel ?? '') == 'manager' ? 'selected' : '' }}>Manager</option>
                        <option value="staff" {{ ($accessLevel ?? '') == 'staff' ? 'selected' : '' }}>Staff</option>
                    </select>
                </div>
                <div class="col-md-1 d-grid">
                    <button type="submit" class="btn btn-primary" title="Filter">
                        <i class="bi bi-filter"></i> Filter
                    </button>
                </div>
            </form>
            @if (request()->anyFilled(['search', 'access_level']))
                <div class="mt-2">
                    <a href="{{ route('users.archives') }}" class="small text-decoration-none text-danger">
                        <i class="bi bi-x-circle me-1"></i> Clear filters
                    </a>
                </div>
            @endif
        </div>
    </div>

    <div class="panel">
        <div class="table-responsive">
            <table class="table table-elegant align-middle">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Gender</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td class="fw-semibold text-brand">{{ $user->userInfo?->full_name ?? '—' }}</td>
                            <td class="text-muted">{{ $user->email }}</td>
                            <td>
                                <span class="badge bg-secondary">{{ ucfirst($user->access_level) }}</span>
                            </td>
                            <td>
                                <span class="badge bg-secondary">Inactive</span>
                            </td>
                            <td>{{ $user->userInfo?->gender ?? '—' }}</td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-secondary"
                                        title="Restore / Edit">
                                        <i class="bi bi-pencil-square"></i> Edit & Reactivate
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="bi bi-archive fs-1 text-muted d-block mb-2"></i>
                                <div class="fw-medium">No archived users found</div>
                                <div class="text-muted small">Inactive or disabled user accounts will appear here.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $users->links() }}</div>
@endsection
