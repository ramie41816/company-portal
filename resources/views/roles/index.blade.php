@extends('layouts.user')

@section('user_content')
    <div class="page-head mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h2 class="mb-1 fw-bold" style="color: var(--ink);"><i class="bi bi-shield-lock"></i> Roles & Permissions</h2>
            <div class="text-muted">Manage system roles and assign granular permission controls.</div>
        </div>
        <a href="{{ route('roles.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Create New Role
        </a>
    </div>

    <div class="panel">
        <div class="table-responsive">
            <table class="table table-elegant align-middle">
                <thead>
                    <tr>
                        <th>Role Name</th>
                        <th>Display Name</th>
                        <th>Description</th>
                        <th>Users Count</th>
                        <th>Permissions</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($roles as $role)
                        <tr>
                            <td class="fw-semibold text-brand font-monospace"><code>{{ $role->name }}</code></td>
                            <td class="fw-semibold" style="color: var(--ink);">{{ $role->display_name }}</td>
                            <td class="text-muted">{{ $role->description ?? '—' }}</td>
                            <td><span class="badge bg-secondary">{{ $role->users_count }} users</span></td>
                            <td><span class="badge bg-info text-dark">{{ $role->permissions_count }} permissions</span></td>
                            <td class="text-end">
                                <div class="gap-2">
                                    <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-sm btn-outline-warning"
                                        title="Edit Role">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @if ($role->users_count === 0 && $role->name !== 'admin')
                                        <form action="{{ route('roles.destroy', $role->id) }}" method="POST"
                                            class="d-inline delete-role-form">
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
                            <td colspan="6" class="text-center py-5">
                                <i class="bi bi-shield-exclamation fs-1 text-muted d-block mb-2"></i>
                                <div class="fw-medium">No roles found</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
        <script>
            document.querySelectorAll('.delete-role-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    Swal.fire({
                        title: 'Are you sure?',
                        text: 'This action cannot be undone. This role will be permanently deleted.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'Cancel',
                        customClass: {
                            popup: 'rounded-4 shadow'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Deleting...',
                                text: 'Please wait while we remove this role.',
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
                            form.submit();
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection
