@extends('layouts.user')

@section('user_content')
    <div class="page-head mb-4">
        <h2 class="mb-1 fw-bold" style="color: var(--ink);"><i class="bi bi-pencil-square"></i> Edit Permission</h2>
        <div class="text-muted">Update permission attributes and its grouping category.</div>
    </div>

    <form action="{{ route('permissions.update', $permission) }}" method="POST" id="editPermissionForm">
        @csrf
        @method('PUT')

        <div class="card border-0 shadow-sm mb-4"
            style="background: var(--card); border: 1px solid var(--line); border-radius: 1rem;">
            <div class="card-body p-4">
                <h5 class="fw-semibold mb-3 pb-2 border-bottom"
                    style="color: var(--ink); border-color: var(--line) !important;">
                    <i class="bi bi-info-circle me-2 text-primary"></i> Permission Information
                </h5>

                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label small fw-semibold text-muted">Permission Name (Slug) *</label>
                        <input type="text" name="name" value="{{ old('name', $permission->name) }}"
                            class="form-control font-monospace @error('name') is-invalid @enderror" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12">
                        <label class="form-label small fw-semibold text-muted">Display Name *</label>
                        <input type="text" name="display_name"
                            value="{{ old('display_name', $permission->display_name) }}"
                            class="form-control @error('display_name') is-invalid @enderror" required>
                        @error('display_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12">
                        <label class="form-label small fw-semibold text-muted">Group Category *</label>
                        <input type="text" name="group" value="{{ old('group', $permission->group) }}"
                            class="form-control @error('group') is-invalid @enderror" required>
                        @error('group')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mb-4">
            <a href="{{ route('permissions.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
            <button type="submit" class="btn btn-primary px-4">
                <i class="bi bi-check2-circle me-1"></i> Update Permission
            </button>
        </div>
    </form>

    @push('scripts')
        <script>
            document.getElementById('editPermissionForm').addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Update permission details?',
                    text: 'Do you want to save these changes to the permission?',
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
