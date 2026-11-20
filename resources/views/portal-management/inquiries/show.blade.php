@extends('layouts.portal')

@section('portal_content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center gap-2 mb-4">
        <a href="{{ route('portal.inquiries.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to inquiries
        </a>
    </div>

    <div class="panel border-0 shadow-sm mb-4"
        style="background: var(--card); color: var(--ink); border: 1px solid var(--line); border-radius: 1rem;">
        <div class="p-4 p-md-5">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 pb-4 mb-4 border-bottom"
                style="border-color: var(--line) !important;">
                <div>
                    <div class="d-flex align-items-center flex-wrap gap-2 mb-1">
                        <h3 class="mb-0 fw-bold" style="color: var(--ink);">
                            <i class="bi bi-person-lines-fill text-primary me-2"></i> Inquiry Details
                        </h3>
                        <span class="badge bg-light text-dark border font-monospace px-2 py-1" style="font-size: 0.8rem;">
                            {{ $inquiry->reference_id ?? 'N/A' }}
                        </span>
                    </div>
                    <div class="text-muted small">
                        Submitted on {{ $inquiry->created_at->format('F d, Y \a\t g:i A') }}
                    </div>
                </div>
                <div>
                    @if ($inquiry->status === 'pending')
                        <span class="badge bg-warning text-dark px-3 py-2 text-uppercase"
                            style="font-size: 0.8rem;">Pending</span>
                    @elseif ($inquiry->status === 'facilitated')
                        <span class="badge bg-success text-white px-3 py-2 text-uppercase"
                            style="font-size: 0.8rem;">Facilitated</span>
                    @else
                        <span class="badge bg-secondary text-white px-3 py-2 text-uppercase"
                            style="font-size: 0.8rem;">{{ ucfirst($inquiry->status) }}</span>
                    @endif
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-12 col-md-6">
                    <div class="card h-100 border shadow-none"
                        style="background: var(--card); border-color: var(--line) !important; border-radius: 0.75rem;">
                        <div class="card-body p-4">
                            <h6 class="text-uppercase small text-muted fw-semibold mb-3" style="letter-spacing: .08em;">
                                <i class="bi bi-person me-1 text-primary"></i> Customer Information
                            </h6>
                            <div class="mb-2"><strong style="color: var(--ink);">Reference ID:</strong>
                                <span class="font-monospace">{{ $inquiry->reference_id ?? 'N/A' }}</span>
                            </div>
                            <div class="mb-2"><strong style="color: var(--ink);">Name:</strong> {{ $inquiry->name }}</div>
                            <div class="mb-2"><strong style="color: var(--ink);">Phone:</strong> <a
                                    href="tel:{{ $inquiry->phone }}" class="text-decoration-none">{{ $inquiry->phone }}</a>
                            </div>
                            <div class="mb-2"><strong style="color: var(--ink);">Email:</strong>
                                {{ $inquiry->email ?? 'Not provided' }}</div>
                            <div class="mb-2"><strong style="color: var(--ink);">Address:</strong> {{ $inquiry->address }}
                            </div>
                            <div class="mb-0"><strong style="color: var(--ink);">IP Address:</strong>
                                <span class="font-monospace text-muted">{{ $inquiry->ip_address ?? 'Not recorded' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="card h-100 border shadow-none"
                        style="background: var(--card); border-color: var(--line) !important; border-radius: 0.75rem;">
                        <div class="card-body p-4 d-flex flex-column justify-content-between">
                            <div>
                                <h6 class="text-uppercase small text-muted fw-semibold mb-3" style="letter-spacing: .08em;">
                                    <i class="bi bi-tools me-1 text-primary"></i> Topic / Service
                                </h6>
                                <div class="mb-3">
                                    <span class="badge bg-primary text-white px-3 py-2" style="font-size: 0.85rem;">
                                        {{ $inquiry->service_type }}
                                    </span>
                                </div>
                            </div>

                            @if (auth()->user()->hasPermission('portal.update'))
                                <form id="updateStatusForm"
                                    action="{{ route('portal.inquiries.update-status', $inquiry) }}" method="POST"
                                    class="mt-3">
                                    @csrf
                                    @method('PUT')
                                    <label class="form-label small fw-semibold text-muted">Update Status</label>
                                    <div class="input-group">
                                        <select id="statusSelect" name="status" class="form-select"
                                            data-current-status="{{ $inquiry->status }}">
                                            <option value="pending" {{ $inquiry->status === 'pending' ? 'selected' : '' }}>
                                                Pending</option>
                                            <option value="facilitated"
                                                {{ $inquiry->status === 'facilitated' ? 'selected' : '' }}>Facilitated
                                            </option>
                                        </select>
                                        <button type="button" id="saveStatusBtn" class="btn btn-primary">Save
                                            Status</button>
                                    </div>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border shadow-none"
                style="background: var(--card); border-color: var(--line) !important; border-radius: 0.75rem;">
                <div class="card-body p-4">
                    <h6 class="text-uppercase small text-muted fw-semibold mb-2" style="letter-spacing: .08em;">
                        <i class="bi bi-chat-left-text me-1 text-primary"></i> Additional Notes / Instructions
                    </h6>
                    <p class="mb-0 text-muted" style="color: var(--ink) !important;">
                        {{ $inquiry->notes ?? 'No additional notes provided by the customer.' }}
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const saveBtn = document.getElementById('saveStatusBtn');
            const select = document.getElementById('statusSelect');
            const form = document.getElementById('updateStatusForm');

            if (saveBtn && select && form) {
                saveBtn.addEventListener('click', function() {
                    const currentStatus = select.dataset.currentStatus;
                    const selectedStatus = select.value;

                    if (currentStatus === selectedStatus) {
                        Swal.fire({
                            icon: 'info',
                            title: 'No change detected',
                            text: `Status is already set to ${selectedStatus.toUpperCase()}.`,
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#0d6efd',
                            customClass: {
                                popup: 'rounded-4 shadow p-4'
                            }
                        });
                        return;
                    }

                    Swal.fire({
                        title: 'Update Status?',
                        text: `Change status to ${selectedStatus.toUpperCase()}?`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, update',
                        cancelButtonText: 'Cancel',
                        confirmButtonColor: '#0d6efd',
                        customClass: {
                            popup: 'rounded-4 shadow p-4'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            }
        });
    </script>
@endpush
