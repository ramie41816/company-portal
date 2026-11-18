@extends('layouts.portal')

@section('portal_content')
    <div class="page-head mb-4">
        <h2 class="mb-1 fw-bold" style="color: var(--ink);"><i class="bi bi-plus-circle"></i> Add New Service</h2>
        <div class="text-muted">Create a new service offering to feature on the website.</div>
    </div>

    <form action="{{ route('portal.services.store') }}" method="POST" id="createServiceForm" enctype="multipart/form-data">
        @csrf

        <div class="card border-0 shadow-sm mb-4"
            style="background: var(--card); border: 1px solid var(--line); border-radius: 1rem;">
            <div class="card-body p-4">
                <h5 class="fw-semibold mb-3 pb-2 border-bottom"
                    style="color: var(--ink); border-color: var(--line) !important;">
                    <i class="bi bi-info-circle me-2 text-primary"></i> Service Information
                </h5>

                <div class="row g-3">
                    <div class="col-12 col-md-8">
                        <label class="form-label small fw-semibold text-muted">Service Name *</label>
                        <input type="text" name="title" value="{{ old('title') }}"
                            class="form-control @error('title') is-invalid @enderror"
                            placeholder="e.g. Septic Tank Cleaning & Siphoning" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label small fw-semibold text-muted">Service Icon *</label>
                        <select name="icon_class" class="form-select @error('icon_class') is-invalid @enderror" required>
                            <option value="bi-tools" {{ old('icon_class') == 'bi-tools' ? 'selected' : '' }}>Tools (General
                                Plumbing)</option>
                            <option value="bi-truck" {{ old('icon_class') == 'bi-truck' ? 'selected' : '' }}>Truck
                                (Siphoning / Hauling)</option>
                            <option value="bi-droplet-fill" {{ old('icon_class') == 'bi-droplet-fill' ? 'selected' : '' }}>
                                Water Drop (Drainage / Flushing)</option>
                            <option value="bi-shield-check" {{ old('icon_class') == 'bi-shield-check' ? 'selected' : '' }}>
                                Shield (Sanitation / Safety)</option>
                            <option value="bi-wrench" {{ old('icon_class') == 'bi-wrench' ? 'selected' : '' }}>Wrench
                                (Repairs)</option>
                            <option value="bi-gear-fill" {{ old('icon_class') == 'bi-gear-fill' ? 'selected' : '' }}>Gear
                                (Maintenance)</option>
                        </select>
                        @error('icon_class')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label small fw-semibold text-muted">Card Summary (Shown on front page) *</label>
                        <textarea name="short_description" class="form-control @error('short_description') is-invalid @enderror" rows="2"
                            required placeholder="Brief 1-2 sentence overview for the service card...">{{ old('short_description') }}</textarea>
                        @error('short_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label small fw-semibold text-muted">Detailed Description *</label>
                        <textarea name="full_description" class="form-control @error('full_description') is-invalid @enderror" rows="5"
                            required placeholder="Write complete details about scope of work, procedure, and service promise...">{{ old('full_description') }}</textarea>
                        @error('full_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label small fw-semibold text-muted">Key Inclusions & Features (Put each item on a
                            new line)</label>
                        <textarea name="features" class="form-control" rows="3"
                            placeholder="Complete septic tank inspection&#10;Safe waste hauling & disposal&#10;24/7 emergency response">{{ old('features') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4"
            style="background: var(--card); border: 1px solid var(--line); border-radius: 1rem;">
            <div class="card-body p-4">
                <h5 class="fw-semibold mb-3 pb-2 border-bottom"
                    style="color: var(--ink); border-color: var(--line) !important;">
                    <i class="bi bi-images me-2 text-primary"></i> Photos & Website Settings
                </h5>

                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label small fw-semibold text-muted">Upload Photos</label>
                        <input type="file" name="images[]" class="form-control" multiple accept="image/*">
                        <small class="text-muted">You can select multiple photos to show work equipment or sample completed
                            jobs.</small>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-semibold text-muted">Display Order Number *</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', 1) }}" class="form-control"
                            required placeholder="e.g. 1 for first position">
                        <small class="text-muted">Determines the order this service appears on the website (1, 2,
                            3...).</small>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-semibold text-muted">Website Status *</label>
                        <select name="is_active" class="form-select" required>
                            <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Published (Visible
                                to Customers)</option>
                            <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Hidden (Draft Mode)
                            </option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex flex-column-reverse flex-md-row justify-content-end gap-2 mb-4">
            <a href="{{ route('portal.services.index') }}"
                class="btn btn-outline-secondary px-4 w-100 w-md-auto">Cancel</a>
            <button type="submit" class="btn btn-primary px-4 w-100 w-md-auto">
                <i class="bi bi-check2-circle me-1"></i> Save Service
            </button>
        </div>
    </form>

    <script>
        document.getElementById('createServiceForm').addEventListener('submit', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Save new service?',
                text: 'This service will be saved and added to your website options.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0d6efd',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, save service!',
                cancelButtonText: 'Cancel',
                customClass: {
                    popup: 'rounded-4 shadow'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Saving...',
                        text: 'Please wait while we save your service information.',
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
@endsection
