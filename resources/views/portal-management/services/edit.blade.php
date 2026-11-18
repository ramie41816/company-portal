@extends('layouts.portal')

@section('portal_content')
    <div class="page-head mb-4">
        <h2 class="mb-1 fw-bold" style="color: var(--ink);"><i class="bi bi-pencil-square"></i> Edit Service Details</h2>
        <div class="text-muted">Update service descriptions, inclusions, display order, and photos.</div>
    </div>

    <form action="{{ route('portal.services.update', $service) }}" method="POST" id="editServiceForm"
        enctype="multipart/form-data">
        @csrf
        @method('PUT')

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
                        <input type="text" name="title" value="{{ old('title', $service->title) }}"
                            class="form-control @error('title') is-invalid @enderror" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label small fw-semibold text-muted">Service Icon *</label>
                        <select name="icon_class" class="form-select @error('icon_class') is-invalid @enderror" required>
                            <option value="bi-tools"
                                {{ old('icon_class', $service->icon_class) == 'bi-tools' ? 'selected' : '' }}>Tools (General
                                Plumbing)</option>
                            <option value="bi-truck"
                                {{ old('icon_class', $service->icon_class) == 'bi-truck' ? 'selected' : '' }}>Truck
                                (Siphoning / Hauling)</option>
                            <option value="bi-droplet-fill"
                                {{ old('icon_class', $service->icon_class) == 'bi-droplet-fill' ? 'selected' : '' }}>Water
                                Drop (Drainage / Flushing)</option>
                            <option value="bi-shield-check"
                                {{ old('icon_class', $service->icon_class) == 'bi-shield-check' ? 'selected' : '' }}>Shield
                                (Sanitation / Safety)</option>
                            <option value="bi-wrench"
                                {{ old('icon_class', $service->icon_class) == 'bi-wrench' ? 'selected' : '' }}>Wrench
                                (Repairs)</option>
                            <option value="bi-gear-fill"
                                {{ old('icon_class', $service->icon_class) == 'bi-gear-fill' ? 'selected' : '' }}>Gear
                                (Maintenance)</option>
                        </select>
                        @error('icon_class')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label small fw-semibold text-muted">Card Summary (Shown on front page) *</label>
                        <textarea name="short_description" class="form-control @error('short_description') is-invalid @enderror" rows="2"
                            required>{{ old('short_description', $service->short_description) }}</textarea>
                        @error('short_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label small fw-semibold text-muted">Detailed Description *</label>
                        <textarea name="full_description" class="form-control @error('full_description') is-invalid @enderror" rows="5"
                            required>{{ old('full_description', $service->full_description) }}</textarea>
                        @error('full_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label small fw-semibold text-muted">Key Inclusions & Features (Put each item on a
                            new line)</label>
                        <textarea name="features" class="form-control" rows="3">{{ old('features', is_array($service->features) ? implode("\n", $service->features) : '') }}</textarea>
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

                @if (!empty($service->images))
                    <div class="mb-4">
                        <label class="form-label small fw-semibold text-muted mb-2">Current Service Photos</label>
                        <div class="row g-3" id="photo-gallery">
                            @foreach ($service->images as $img)
                                <div class="col-6 col-md-3" id="photo-card-{{ $loop->index }}">
                                    <div class="position-relative border rounded-3 overflow-hidden shadow-sm"
                                        style="border-color: var(--line) !important;">
                                        <img src="{{ asset('storage/' . $img) }}" class="w-100 object-fit-cover"
                                            style="height: 120px;" alt="Service Photo">

                                        <button type="button"
                                            class="btn btn-sm btn-danger rounded-circle position-absolute top-0 end-0 m-2 delete-photo-btn"
                                            data-path="{{ $img }}" data-card="photo-card-{{ $loop->index }}"
                                            title="Remove Photo">
                                            <i class="bi bi-x fs-6"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label small fw-semibold text-muted">Upload New Photos</label>
                        <input type="file" name="images[]" class="form-control" multiple accept="image/*">
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-semibold text-muted">Display Order Number *</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', $service->sort_order) }}"
                            class="form-control" required>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-semibold text-muted">Website Status *</label>
                        <select name="is_active" class="form-select" required>
                            <option value="1"
                                {{ old('is_active', $service->is_active ? '1' : '0') == '1' ? 'selected' : '' }}>Published
                                (Visible to Customers)</option>
                            <option value="0"
                                {{ old('is_active', $service->is_active ? '1' : '0') == '0' ? 'selected' : '' }}>Hidden
                                (Draft Mode)</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex flex-column-reverse flex-md-row justify-content-end gap-2 mb-4">
            <a href="{{ route('portal.services.index') }}"
                class="btn btn-outline-secondary px-4 w-100 w-md-auto">Cancel</a>
            <button type="submit" class="btn btn-primary px-4 w-100 w-md-auto">
                <i class="bi bi-check2-circle me-1"></i> Update Changes
            </button>
        </div>
    </form>

    @push('scripts')
        <script>
            document.querySelectorAll('.delete-photo-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const imagePath = this.getAttribute('data-path');
                    const cardId = this.getAttribute('data-card');

                    Swal.fire({
                        title: 'Remove photo?',
                        text: 'This image will be permanently deleted.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, remove photo!',
                        cancelButtonText: 'Cancel',
                        customClass: {
                            popup: 'rounded-4 shadow'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch("{{ route('portal.services.delete-image', $service->id) }}", {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json'
                                    },
                                    body: JSON.stringify({
                                        image_path: imagePath
                                    })
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        document.getElementById(cardId).remove();
                                        Swal.fire({
                                            toast: true,
                                            position: 'bottom-end',
                                            icon: 'success',
                                            title: data.message,
                                            showConfirmButton: false,
                                            timer: 3000
                                        });
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                });
                        }
                    });
                });
            });

            document.getElementById('editServiceForm').addEventListener('submit', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Update service details?',
                    text: 'Are you sure you want to save these updates to the website?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#0d6efd',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, save updates!',
                    cancelButtonText: 'Cancel',
                    customClass: {
                        popup: 'rounded-4 shadow'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Saving...',
                            text: 'Please wait while we process your updates.',
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
