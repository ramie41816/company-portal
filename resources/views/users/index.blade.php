@extends('layouts.user')

@section('user_content')
    <div class="page-head mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h2 class="mb-1"><i class="bi bi-people"></i> User Management</h2>
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
                                <div class="d-flex flex-wrap gap-1">
                                    @forelse($user->roles as $role)
                                        <span class="badge bg-primary text-white px-2 py-1">
                                            {{ $role->display_name }}
                                        </span>
                                    @empty
                                        <span class="badge bg-secondary text-white px-2 py-1">No Role</span>
                                    @endforelse
                                </div>
                            </td>
                            <td>
                                @if ($user->status === 'active')
                                    <span class="badge bg-success text-white px-2 py-1 text-uppercase">Active</span>
                                @else
                                    <span class="badge bg-secondary text-white px-2 py-1 text-uppercase">Inactive</span>
                                @endif
                            </td>
                            <td>{{ $user->userInfo?->gender ?? '—' }}</td>
                            <td>{{ $user->userInfo?->birthday ? \Carbon\Carbon::parse($user->userInfo->birthday)->format('M d, Y') : '—' }}
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-outline-primary"
                                        title="View Profile">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-warning"
                                        title="Edit User">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" title="Archive User"
                                        data-bs-toggle="modal" data-bs-target="#archiveModal"
                                        data-action="{{ route('users.destroy', $user) }}"
                                        data-name="{{ $user->userInfo?->full_name ?? $user->email }}">
                                        <i class="bi bi-archive"></i> Archive
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

    {{-- Archive / Deactivate confirmation modal --}}
    <div class="modal fade" id="archiveModal" tabindex="-1" aria-labelledby="archiveModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" id="archiveForm" class="modal-content shadow"
                style="background: var(--card); color: var(--ink); border: 1px solid var(--line); border-radius: 1rem;">
                @csrf
                @method('DELETE')

                <div class="modal-body text-center p-4">
                    <div class="mx-auto mb-3"
                        style="width: 60px; height: 60px; border-radius: 50%; display: grid; place-items: center;
                                font-size: 1.6rem; background: rgba(255, 193, 7, .15); color: #ffc107;">
                        <i class="bi bi-archive"></i>
                    </div>
                    <h5 class="fw-semibold mb-2" id="archiveModalLabel">Archive this user account?</h5>
                    <div class="fw-medium mb-2 text-muted" id="deleteName"></div>
                    <p class="text-muted small mb-4">
                        This user will be moved to the archives and restricted from accessing the system. You can restore
                        them anytime.
                    </p>
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning text-dark fw-medium">
                            <i class="bi bi-archive me-1"></i> Yes, Archive
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        const archiveModal = document.getElementById('archiveModal');
        if (archiveModal) {
            archiveModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                document.getElementById('archiveForm').action = button.dataset.action;
                document.getElementById('deleteName').textContent = button.dataset.name;
            });
        }
    </script>
@endsection
