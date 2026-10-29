@extends('layouts.user')

@section('user_content')
    <div class="page-head mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h2 class="mb-1"><i class="bi bi-archive"></i> Archived Users</h2>
            <div class="sub">View and manage inactive or archived system user accounts.</div>
        </div>
    </div>

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
                        <<tr>
                            <td class="fw-semibold text-brand">{{ $user->userInfo?->full_name ?? '—' }}</td>
                            <td class="text-muted">{{ $user->email }}</td>
                            <td>
                                <span class="badge bg-secondary">{{ ucfirst($user->access_level) }}</span>
                            </td>
                            <td>
                                <span class="badge bg-danger">Archived</span>
                            </td>
                            <td>{{ $user->userInfo?->gender ?? '—' }}</td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-sm btn-outline-success" title="Restore User"
                                        data-bs-toggle="modal" data-bs-target="#restoreModal"
                                        data-action="{{ route('users.restore', $user->id) }}"
                                        data-name="{{ $user->userInfo?->full_name ?? $user->email }}">
                                        <i class="bi bi-arrow-counterclockwise"></i> Restore
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger" title="Delete Permanently"
                                        data-bs-toggle="modal" data-bs-target="#forceDeleteModal"
                                        data-action="{{ route('users.force-delete', $user->id) }}"
                                        data-name="{{ $user->userInfo?->full_name ?? $user->email }}">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
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

    <!-- Restore Modal -->
    <div class="modal fade" id="restoreModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" id="restoreForm" class="modal-content shadow"
                style="background: var(--card); color: var(--ink); border: 1px solid var(--line); border-radius: 1rem;">
                @csrf
                @method('PUT')
                <div class="modal-body text-center p-4">
                    <div class="mx-auto mb-3"
                        style="width: 60px; height: 60px; border-radius: 50%; display: grid; place-items: center; font-size: 1.6rem; background: rgba(25, 135, 84, .15); color: #198754;">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </div>
                    <h5 class="fw-semibold mb-2">Restore user account?</h5>
                    <div class="fw-medium mb-2 text-muted" id="restoreName"></div>
                    <p class="text-muted small mb-4">This user will be restored to active status and regain portal access.
                    </p>
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Yes, Restore</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Force Delete Modal -->
    <div class="modal fade" id="forceDeleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" id="forceDeleteForm" class="modal-content shadow"
                style="background: var(--card); color: var(--ink); border: 1px solid var(--line); border-radius: 1rem;">
                @csrf
                @method('DELETE')
                <div class="modal-body text-center p-4">
                    <div class="mx-auto mb-3"
                        style="width: 60px; height: 60px; border-radius: 50%; display: grid; place-items: center; font-size: 1.6rem; background: rgba(220, 53, 69, .15); color: #dc3545;">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                    <h5 class="fw-semibold mb-2">Permanently delete user?</h5>
                    <div class="fw-medium mb-2 text-muted" id="forceDeleteName"></div>
                    <p class="text-muted small mb-4">This action is irreversible and will completely delete the user
                        profile and credentials from the database.</p>
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Yes, Delete Permanently</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Script to bind data to modals dynamically
        const restoreModal = document.getElementById('restoreModal');
        if (restoreModal) {
            restoreModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                document.getElementById('restoreForm').action = button.getAttribute('data-action');
                document.getElementById('restoreName').textContent = button.getAttribute('data-name');
            });
        }

        const forceDeleteModal = document.getElementById('forceDeleteModal');
        if (forceDeleteModal) {
            forceDeleteModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                document.getElementById('forceDeleteForm').action = button.getAttribute('data-action');
                document.getElementById('forceDeleteName').textContent = button.getAttribute('data-name');
            });
        }
    </script>

    <div class="mt-3">{{ $users->links() }}</div>
@endsection
