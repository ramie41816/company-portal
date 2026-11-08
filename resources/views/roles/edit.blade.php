@extends('layouts.user')

@section('user_content')
    <div class="page-head mb-4">
        <h2 class="mb-1 fw-bold" style="color: var(--ink);"><i class="bi bi-pencil-square"></i> Edit Role</h2>
        <div class="text-muted">Update role details and reconfigure assigned permissions.</div>
    </div>

    <form action="{{ route('roles.update', $role) }}" method="POST" id="editRoleForm">
        @csrf
        @method('PUT')

        {{-- Include Shared Form Partial, passing role data --}}
        @include('roles._form', ['role' => $role, 'rolePermissions' => $rolePermissions])

        {{-- Form Actions --}}
        <div class="d-flex justify-content-end gap-2 mb-4">
            <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
            <button type="submit" class="btn btn-primary px-4">
                <i class="bi bi-check2-circle me-1"></i> Update Role
            </button>
        </div>
    </form>

    @push('scripts')
        <script>
            document.getElementById('editRoleForm').addEventListener('submit', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Update role details?',
                    text: 'Do you want to save these changes to the role and permissions?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#2b3a67',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, update changes!',
                    cancelButtonText: 'Cancel',
                    customClass: {
                        popup: 'rounded-4 shadow'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Processing...',
                            text: 'Please wait while we save your changes.',
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
    @endpush
@endsection
