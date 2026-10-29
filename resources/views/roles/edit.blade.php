@extends('layouts.user')

@section('user_content')
    <div class="page-head mb-4">
        <h2 class="mb-1 fw-bold" style="color: var(--ink);"><i class="bi bi-pencil-square"></i> Edit Role</h2>
        <div class="text-muted">Update role details and reconfigure assigned permissions.</div>
    </div>

    <form action="{{ route('roles.update', $role) }}" method="POST" id="editRoleForm">
        @csrf
        @method('PUT')

        {{-- Role Details Panel --}}
        <div class="card border-0 shadow-sm mb-4"
            style="background: var(--card); border: 1px solid var(--line); border-radius: 1rem;">
            <div class="card-body p-4">
                <h5 class="fw-semibold mb-3 pb-2 border-bottom"
                    style="color: var(--ink); border-color: var(--line) !important;">
                    <i class="bi bi-info-circle me-2 text-primary"></i> Role Information
                </h5>

                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label small fw-semibold text-muted">Role Name (Display Name) *</label>
                        <input type="text" name="display_name" value="{{ old('display_name', $role->display_name) }}"
                            class="form-control @error('display_name') is-invalid @enderror" required>
                        @error('display_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12">
                        <label class="form-label small fw-semibold text-muted">Description</label>
                        <textarea name="description" rows="2" class="form-control @error('description') is-invalid @enderror">{{ old('description', $role->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Permissions Assignment Panel --}}
        <div class="card border-0 shadow-sm mb-4"
            style="background: var(--card); border: 1px solid var(--line); border-radius: 1rem;">
            <div class="card-body p-4">
                <h5 class="fw-semibold mb-3 pb-2 border-bottom"
                    style="color: var(--ink); border-color: var(--line) !important;">
                    <i class="bi bi-check2-square me-2 text-primary"></i> Assign Permissions
                </h5>

                <div class="row g-3">
                    @foreach ($permissions as $group => $perms)
                        <div class="col-md-6">
                            <div class="p-3 h-100 rounded-3"
                                style="background: var(--surface, #f8f9fa); border: 1px solid var(--line);">

                                {{-- Group Header with Select All Checkbox --}}
                                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom"
                                    style="border-color: var(--line) !important;">
                                    <h6 class="fw-bold text-primary mb-0">{{ ucfirst($group) }}</h6>
                                    <div class="form-check mb-0">
                                        <input class="form-check-input select-all-group" type="checkbox"
                                            id="select_all_{{ Str::slug($group) }}">
                                        <label class="form-check-label small fw-semibold text-muted"
                                            for="select_all_{{ Str::slug($group) }}">
                                            Select All
                                        </label>
                                    </div>
                                </div>

                                {{-- Auto-scroll permission container --}}
                                <div class="d-flex flex-column gap-2 pe-2 permission-group-container"
                                    style="max-height: 180px; overflow-y: auto;">
                                    @foreach ($perms as $permission)
                                        <div class="form-check">
                                            <input class="form-check-input permission-checkbox" type="checkbox"
                                                name="permissions[]" value="{{ $permission->id }}"
                                                id="perm_{{ $permission->id }}"
                                                {{ in_array($permission->id, old('permissions', $rolePermissions)) ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="perm_{{ $permission->id }}">
                                                {{ $permission->display_name }} <span
                                                    class="text-muted font-monospace">({{ $permission->name }})</span>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

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
            // Function to check if all checkboxes in a group are checked and update the "Select All" toggle state
            document.querySelectorAll('.rounded-3').forEach(cardBody => {
                const selectAllCb = cardBody.querySelector('.select-all-group');
                const checkboxes = cardBody.querySelectorAll('.permission-checkbox');

                if (selectAllCb && checkboxes.length > 0) {
                    // Initial check on load
                    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                    selectAllCb.checked = allChecked;

                    // On change of individual checkboxes
                    checkboxes.forEach(cb => {
                        cb.addEventListener('change', () => {
                            selectAllCb.checked = Array.from(checkboxes).every(item => item.checked);
                        });
                    });

                    // On change of Select All toggle
                    selectAllCb.addEventListener('change', function() {
                        checkboxes.forEach(cb => {
                            cb.checked = this.checked;
                        });
                    });
                }
            });

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
