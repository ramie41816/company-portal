@extends('layouts.user')

@section('user_content')
    <div class="page-head mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h2 class="mb-1 fw-bold" style="color: var(--ink);"><i class="bi bi-shield-lock"></i> Roles & Permissions</h2>
            <div class="text-muted">Manage system roles and assign granular security permissions.</div>
        </div>
        <a href="{{ route('roles.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Create New Role
        </a>
    </div>

    {{-- Search Bar Space (Matches User List Layout Structure) --}}
    <div class="card border-0 shadow-sm mb-4" style="background: var(--card); border-radius: 0.75rem;">
        <div class="card-body py-3">
            <div class="d-flex align-items-center justify-content-between">
                <span class="text-muted small fw-semibold">Total Roles Configured: <span
                        class="text-dark fw-bold">{{ $roles->count() }}</span></span>
                <span class="badge bg-secondary">Access Control Center</span>
            </div>
        </div>
    </div>

    <div class="panel">
        <div class="table-responsive">
            <table class="table table-elegant align-middle">
                <thead>
                    <tr>
                        <th>Role Name</th>
                        <th>Description</th>
                        <th>Users Count</th>
                        <th>Permissions</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($roles as $role)
                        <tr>
                            <td class="fw-semibold text-brand">
                                {{ $role->display_name }} <br>
                                <small class="text-muted font-monospace">slug: {{ $role->name }}</small>
                            </td>
                            <td class="text-muted">{{ $role->description ?? '—' }}</td>
                            <td><span class="badge bg-secondary">{{ $role->users_count }} users</span></td>
                            <td><span class="badge bg-info text-dark">{{ $role->permissions_count }} permissions</span></td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-sm btn-outline-warning"
                                        title="Edit Role">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @if ($role->users_count === 0)
                                        <form action="{{ route('roles.destroy', $role->id) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('Delete this role?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                title="Delete Role">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="bi bi-shield-exclamation fs-1 text-muted d-block mb-2"></i>
                                <div class="fw-medium">No roles found</div>
                                <div class="text-muted small">Create roles to start managing permissions.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
