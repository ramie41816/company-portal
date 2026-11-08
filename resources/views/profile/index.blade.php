@extends('layouts.user')

@section('user_content')
    <div class="page-head mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h2 class="mb-1 fw-bold" style="color: var(--ink);"><i class="bi bi-shield-check"></i> Permissions Registry</h2>
            <div class="text-muted">Manage granular system permissions grouped by module category.</div>
        </div>
        <div class="d-flex gap-2">
            {{-- Sync / Update Permissions Button --}}
            <form action="{{ route('permissions.sync-routes') }}" method="POST" id="syncPermissionsForm">
                @csrf
                <button type="submit" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-repeat me-1"></i> Sync / Update Registry
                </button>
            </form>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @forelse ($permissions as $group => $groupPermissions)
        <div class="panel mb-4"
            style="background: var(--card, #fff); border: 1px solid var(--line, #dee2e6); border-radius: 1rem;">
            <div class="p-3 border-bottom fw-bold text-uppercase text-secondary small"
                style="letter-spacing: .08em; border-color: var(--line, #dee2e6) !important;">
                {{ $group ?: 'General' }}
            </div>

            {{-- Scrollable container for each permission group --}}
            <div class="permission-group-container p-3" style="max-height: 280px; overflow-y: auto;">
                <div class="table-responsive">
                    <table class="table table-elegant align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width: 35%;">Display Name</th>
                                <th style="width: 45%;">Permission Slug (Name)</th>
                                <th class="text-end" style="width: 20%;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($groupPermissions as $permission)
                                <tr>
                                    <td class="fw-semibold" style="color: var(--ink);">{{ $permission->display_name }}</td>
                                    <td class="font-monospace text-muted small"><code>{{ $permission->name }}</code></td>
                                    <td class="text-end">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ route('permissions.edit', $permission->id) }}"
                                                class="btn btn-sm btn-outline-warning" title="Edit Permission">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('permissions.destroy', $permission->id) }}"
                                                method="POST" class="d-inline delete-permission-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    title="Delete Permission">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @empty
        <div class="panel text-center py-5"
            style="background: var(--card, #fff); border: 1px solid var(--line, #dee2e6); border-radius: 1rem;">
            <i class="bi bi-shield-exclamation fs-1 text-muted d-block mb-2"></i>
            <div class="fw-medium">No permissions found</div>
        </div>
    @endforelse

    @push('scripts')
        <script>
            // Sync button confirmation & loading spinner via SweetAlert2
            document.getElementById('syncPermissionsForm').addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Sync Permissions Registry?',
                    text: 'This will scan all web routes and register any missing permissions automatically.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#2b3a67',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, sync registry!',
                    cancelButtonText: 'Cancel',
                    customClass: {
                        popup: 'rounded-4 shadow'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Scanning Routes...',
                            text: 'Please wait while we update the permission registry.',
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

            document.querySelectorAll('.delete-permission-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Are you sure?',
                        text: 'This action cannot be undone. This permission will be permanently deleted.',
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
                                text: 'Please wait while we remove this permission.',
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
