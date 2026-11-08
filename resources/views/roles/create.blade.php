@extends('layouts.user')

@section('user_content')
    <div class="page-head mb-4">
        <h2 class="mb-1 fw-bold" style="color: var(--ink);"><i class="bi bi-shield-plus"></i> Create New Role</h2>
        <div class="text-muted">Define a new role and configure access permission checkboxes.</div>
    </div>

    <form action="{{ route('roles.store') }}" method="POST" id="createRoleForm">
        @csrf

        {{-- Include Shared Form Partial --}}
        @include('roles._form')

        {{-- Form Actions --}}
        <div class="d-flex justify-content-end gap-2 mb-4">
            <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
            <button type="submit" class="btn btn-primary px-4">
                <i class="bi bi-check2-circle me-1"></i> Save Role
            </button>
        </div>
    </form>

    @push('scripts')
        <script>
            document.getElementById('createRoleForm').addEventListener('submit', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Create new role?',
                    text: 'Do you want to save this role and assign the selected permissions?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#2b3a67',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, create role!',
                    cancelButtonText: 'Cancel',
                    customClass: {
                        popup: 'rounded-4 shadow'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Processing...',
                            text: 'Please wait while we save the new role.',
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
