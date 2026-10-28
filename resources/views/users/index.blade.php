@extends('layouts.user')

@section('user_content')
    <div class="page-head mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h2 class="mb-1">User Management</h2>
            <div class="sub">View and manage system users, profiles, and access levels in one place.</div>
        </div>
    </div>

    {{-- Advanced Search & Filter Bar --}}
    <div class="card border-0 shadow-sm mb-4" style="background: var(--card); border-radius: 0.75rem;">
        <div class="card-body">
            <form action="{{ route('users.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-muted">Search keyword</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" value="{{ $search ?? '' }}" class="form-control border-start-0"
                            placeholder="Name, email...">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-muted">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="active" {{ ($status ?? '') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ ($status ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
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
            @if (request()->anyFilled(['search', 'status', 'access_level']))
                <div class="mt-2">
                    <a href="{{ route('users.index') }}" class="small text-decoration-none text-danger">
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
                        <th>Birthday</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td class="fw-semibold text-brand">{{ $user->userInfo?->full_name ?? '—' }}</td>
                            <td class="text-muted">{{ $user->email }}</td>
                            <td>
                                <span
                                    class="badge bg-{{ $user->access_level === 'admin' ? 'danger' : ($user->access_level === 'manager' ? 'warning text-dark' : 'secondary') }}">
                                    {{ ucfirst($user->access_level) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $user->status === 'active' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($user->status) }}
                                </span>
                            </td>
                            <td>{{ $user->userInfo?->gender ?? '—' }}</td>
                            <td>{{ $user->userInfo?->birthday ? \Carbon\Carbon::parse($user->userInfo->birthday)->format('M d, Y') : '—' }}
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-outline-secondary"
                                        title="View Profile">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-secondary"
                                        title="Edit User">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" title="Delete User"
                                        data-bs-toggle="modal" data-bs-target="#deleteModal"
                                        data-action="{{ route('users.destroy', $user) }}"
                                        data-name="{{ $user->userInfo?->full_name ?? $user->email }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="bi bi-people fs-1 text-muted d-block mb-2"></i>
                                <div class="fw-medium">No users found</div>
                                <div class="text-muted small">Try adjusting your search query or filters.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $users->links() }}</div>

    {{-- Delete confirmation modal --}}
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" id="deleteForm" class="modal-content shadow"
                style="background: var(--card); color: var(--ink); border: 1px solid var(--line); border-radius: 1rem;">
                @csrf
                @method('DELETE')

                <div class="modal-body text-center p-4">
                    <div class="mx-auto mb-3"
                        style="width: 60px; height: 60px; border-radius: 50%; display: grid; place-items: center;
                                font-size: 1.6rem; background: rgba(220, 53, 69, .12); color: #dc3545;">
                        <i class="bi bi-trash"></i>
                    </div>
                    <h5 class="fw-semibold mb-2" id="deleteModalLabel">Delete this user account?</h5>
                    <div class="fw-medium mb-2" id="deleteName"></div>
                    <p class="text-muted small mb-4">
                        This user will be permanently removed from the system along with their associated information
                        profile.
                    </p>
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash me-1"></i> Delete
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('deleteModal').addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            document.getElementById('deleteForm').action = button.dataset.action;
            document.getElementById('deleteName').textContent = button.dataset.name;
        });
    </script>
@endsection
