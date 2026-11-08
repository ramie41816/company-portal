@php
    $role = $role ?? null;
    $rolePermissions = $rolePermissions ?? [];
@endphp

{{-- Role Details Panel --}}
<div class="card border-0 shadow-sm mb-4"
    style="background: var(--card); border: 1px solid var(--line); border-radius: 1rem;">
    <div class="card-body p-4">
        <h5 class="fw-semibold mb-3 pb-2 border-bottom" style="color: var(--ink); border-color: var(--line) !important;">
            <i class="bi bi-info-circle me-2 text-primary"></i> Role Information
        </h5>

        <div class="row g-3">
            <div class="col-md-12">
                <label class="form-label small fw-semibold text-muted">Role Name (Display Name) *</label>
                <input type="text" name="display_name" value="{{ old('display_name', $role->display_name ?? '') }}"
                    class="form-control @error('display_name') is-invalid @enderror" placeholder="e.g. Content Manager"
                    required>
                @error('display_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-12">
                <label class="form-label small fw-semibold text-muted">Description</label>
                <textarea name="description" rows="2" class="form-control @error('description') is-invalid @enderror"
                    placeholder="Brief details about what this role can do">{{ old('description', $role->description ?? '') }}</textarea>
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
                @php
                    // Ensure the permission ending with '.view' or containing 'view' is sorted to the front
                    $sortedPerms = collect($perms)->sort(function ($a, $b) {
                        $aIsView = str_ends_with($a->name, '.view') || str_contains($a->name, 'view');
                        $bIsView = str_ends_with($b->name, '.view') || str_contains($b->name, 'view');
                        return $bIsView <=> $aIsView;
                    });

                    // Identify the view permission for dependency locking
                    $viewPermission = $sortedPerms->first(
                        fn($p) => str_ends_with($p->name, '.view') || str_contains($p->name, 'view'),
                    );
                    $viewPermissionId = $viewPermission ? $viewPermission->id : null;
                @endphp

                <div class="col-md-6">
                    <div class="p-3 h-100 rounded-3 permission-card-group"
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
                            @foreach ($sortedPerms as $permission)
                                @php
                                    $isViewPerm = $permission->id === $viewPermissionId;
                                    $isChecked = in_array($permission->id, old('permissions', $rolePermissions));
                                @endphp
                                <div class="form-check">
                                    <input
                                        class="form-check-input permission-checkbox {{ $isViewPerm ? 'view-permission-input' : '' }}"
                                        type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                        id="perm_{{ $permission->id }}"
                                        data-is-view="{{ $isViewPerm ? 'true' : 'false' }}"
                                        {{ $isChecked ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="perm_{{ $permission->id }}">
                                        {{ $permission->display_name }} <span
                                            class="text-muted font-monospace">({{ $permission->name }})</span>
                                        @if ($isViewPerm)
                                            <span class="badge bg-light text-dark border ms-1"
                                                style="font-size: 0.65rem;">Required First</span>
                                        @endif
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

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.permission-card-group').forEach(groupCard => {
                const viewCheckbox = groupCard.querySelector('.view-permission-input');
                const allCheckboxes = groupCard.querySelectorAll('.permission-checkbox');
                const selectAllCb = groupCard.querySelector('.select-all-group');

                // Function to handle enabling/disabling dependent action checkboxes based on view state
                function updateDependentPermissions() {
                    if (!viewCheckbox) return;

                    const isViewEnabled = viewCheckbox.checked;

                    allCheckboxes.forEach(cb => {
                        if (cb !== viewCheckbox) {
                            if (!isViewEnabled) {
                                cb.checked = false; // Uncheck dependent rights if view is disabled
                                cb.disabled = true; // Disable them completely
                            } else {
                                cb.disabled = false; // Enable them when view is checked
                            }
                        }
                    });

                    // Update Select All state tracking (Keep Select All enabled always)
                    if (selectAllCb) {
                        const enabledCheckboxes = Array.from(allCheckboxes).filter(cb => !cb.disabled);
                        selectAllCb.checked = enabledCheckboxes.length > 0 && enabledCheckboxes.every(cb =>
                            cb.checked);
                        selectAllCb.disabled = false; // Ensure Select All is never locked out
                    }
                }

                // Run immediately on DOM load to enforce initial view state
                updateDependentPermissions();

                // Bind listener to view checkbox change
                if (viewCheckbox) {
                    viewCheckbox.addEventListener('change', updateDependentPermissions);
                }

                // Select All group handler
                if (selectAllCb) {
                    selectAllCb.addEventListener('change', function() {
                        const shouldCheck = this.checked;

                        // If checking Select All, ensure view is checked first so dependents can be enabled
                        if (shouldCheck && viewCheckbox && !viewCheckbox.checked) {
                            viewCheckbox.checked = true;
                        }

                        // Enable/disable and check/uncheck all checkboxes based on Select All state
                        allCheckboxes.forEach(cb => {
                            cb.disabled =
                                false; // Temporarily enable all to allow checking via Select All
                            cb.checked = shouldCheck;
                        });

                        // If unchecking Select All, re-run updateDependentPermissions so .view state takes effect
                        if (!shouldCheck) {
                            if (viewCheckbox) viewCheckbox.checked = false;
                            updateDependentPermissions();
                        }
                    });
                }

                // Individual checkboxes sync state back to select-all toggle
                allCheckboxes.forEach(cb => {
                    cb.addEventListener('change', () => {
                        // If view checkbox was manually unchecked, enforce lock down
                        if (cb === viewCheckbox && !cb.checked) {
                            updateDependentPermissions();
                        }

                        if (selectAllCb && viewCheckbox && viewCheckbox.checked) {
                            const enabledCheckboxes = Array.from(allCheckboxes).filter(
                                item => !item.disabled);
                            selectAllCb.checked = enabledCheckboxes.length > 0 &&
                                enabledCheckboxes.every(item => item.checked);
                        }
                    });
                });
            });
        });
    </script>
@endpush
