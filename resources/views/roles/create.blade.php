@extends('layouts.user')

@section('user_content')
    <div class="page-head mb-4">
        <h2 class="mb-1 fw-bold" style="color: var(--ink);"><i class="bi bi-shield-plus"></i> Create New Role</h2>
        <div class="text-muted">Define a new role and configure access permission checkboxes.</div>
    </div>

    <form action="{{ route('roles.store') }}" method="POST" class="panel p-4"
        style="background: var(--card); border: 1px solid var(--line); border-radius: 0.75rem;">
        @csrf

        <div class="mb-3">
            <label for="display_name" class="form-label fw-semibold">Role Name</label>
            <input type="text" class="form-control @error('display_name') is-invalid @enderror" id="display_name"
                name="display_name" value="{{ old('display_name') }}" required placeholder="e.g. Content Manager">
            @error('display_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4">
            <label for="description" class="form-label fw-semibold">Description</label>
            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                rows="2" placeholder="Brief details about what this role can do">{{ old('description') }}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">Assign Permissions</h5>
            <span class="text-muted small">Scroll inside modules to see all permissions</span>
        </div>

        <div class="row g-3 mb-4">
            @foreach ($permissions as $group => $perms)
                @php $groupSlug = Str::slug($group); @endphp
                <div class="col-md-6">
                    <div class="card p-3 h-100 shadow-none"
                        style="background: var(--surface); border: 1px solid var(--line); border-radius: 0.75rem;">
                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom"
                            style="border-color: var(--line) !important;">
                            <h6 class="fw-bold text-primary mb-0">{{ ucfirst($group) }}</h6>
                            <button type="button" class="btn btn-link btn-sm text-decoration-none p-0 toggle-group"
                                data-group="{{ $groupSlug }}" style="font-size: 0.85rem;">Select All</button>
                        </div>
                        <div class="d-flex flex-column gap-2 overflow-y-auto pe-2" style="max-height: 200px;">
                            @foreach ($perms as $permission)
                                <div class="form-check">
                                    <input class="form-check-input permission-checkbox group-{{ $groupSlug }}"
                                        type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                        id="perm_{{ $permission->id }}"
                                        {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="perm_{{ $permission->id }}">
                                        {{ $permission->display_name }} <span class="text-muted font-monospace"
                                            style="font-size: 0.7rem;">({{ $permission->name }})</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Save Role</button>
        </div>
    </form>

    @push('scripts')
        <script>
            document.querySelectorAll('.toggle-group').forEach(button => {
                button.addEventListener('click', function() {
                    const groupSlug = this.dataset.group;
                    const checkboxes = document.querySelectorAll(`.group-${groupSlug}`);
                    const allChecked = Array.from(checkboxes).every(cb => cb.checked);

                    checkboxes.forEach(cb => cb.checked = !allChecked);
                    this.textContent = allChecked ? 'Select All' : 'Deselect All';
                });
            });
        </script>
    @endpush
@endsection
