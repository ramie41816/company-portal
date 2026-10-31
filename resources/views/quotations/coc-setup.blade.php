@extends('layouts.user')

@section('user_content')
    <div class="page-head mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h2 class="mb-1 fw-bold" style="color: var(--ink);"><i class="bi bi-file-earmark-check"></i> Setup Certificate of
                Completion</h2>
            <div class="text-muted">Review quotation summary, check/uncheck executed items, provide optional item remarks,
                configure execution signatures, and generate CoC.</div>
        </div>
        <a href="{{ route('quotations.show', $quotation->id) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Quotation
        </a>
    </div>

    <form action="{{ route('quotations.coc.pdf', $quotation->id) }}" method="POST" target="_blank" id="cocForm"
        enctype="multipart/form-data">
        @csrf

        {{-- Comprehensive Client & Quotation Overview Panel --}}
        <div class="panel mb-4 p-4"
            style="background: var(--card, #fff); border: 1px solid var(--line, #dee2e6); border-radius: 1rem;">
            <div class="row g-4">
                <div class="col-md-6 border-end">
                    <h6 class="fw-bold text-uppercase text-primary small mb-3" style="letter-spacing: .05em;">Client
                        Information (Quote To)</h6>
                    <div class="mb-2">
                        <span class="text-muted small d-block">Client / Contact Name:</span>
                        <strong style="color: var(--ink);">{{ $quotation->quoted_to }}</strong>
                    </div>
                    <div class="mb-2">
                        <span class="text-muted small d-block">Company Name:</span>
                        <span style="color: var(--ink);">{{ $quotation->client_company ?? '—' }}</span>
                    </div>
                    <div class="mb-2">
                        <span class="text-muted small d-block">Address:</span>
                        <span style="color: var(--ink);">{{ $quotation->client_address ?? '—' }}</span>
                    </div>
                    <div class="row">
                        <div class="col-sm-6 mb-2">
                            <span class="text-muted small d-block">Phone:</span>
                            <span style="color: var(--ink);">{{ $quotation->client_phone ?? '—' }}</span>
                        </div>
                        <div class="col-sm-6 mb-2">
                            <span class="text-muted small d-block">Email:</span>
                            <span style="color: var(--ink);">{{ $quotation->contact_email ?? '—' }}</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 ps-md-4">
                    <h6 class="fw-bold text-uppercase text-primary small mb-3" style="letter-spacing: .05em;">Quotation
                        Overview</h6>
                    <div class="row">
                        <div class="col-sm-6 mb-2">
                            <span class="text-muted small d-block">Quotation Number:</span>
                            <strong class="text-primary font-monospace">{{ $quotation->quotation_number }}</strong>
                        </div>
                        <div class="col-sm-6 mb-2">
                            <span class="text-muted small d-block">Issue Date:</span>
                            <span style="color: var(--ink);">{{ $quotation->quotation_date->format('d/m/Y') }}</span>
                        </div>
                    </div>
                    <div class="mb-2">
                        <span class="text-muted small d-block">Project / Reference:</span>
                        <strong style="color: var(--ink);">{{ $quotation->reference_project ?? '—' }}</strong>
                    </div>
                    <div class="mb-2">
                        <span class="text-muted small d-block">Total Amount:</span>
                        <strong class="text-success fs-5">PHP {{ number_format($quotation->total, 2) }}</strong>
                    </div>
                </div>
            </div>
        </div>

        {{-- Scope of Work & Items Checkbox Selection Panel --}}
        <div class="panel mb-4 p-4"
            style="background: var(--card, #fff); border: 1px solid var(--line, #dee2e6); border-radius: 1rem;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-semibold mb-0" style="color: var(--ink);">Select Executed Services & Item Remarks</h5>
                <div>
                    <button type="button" id="selectAll" class="btn btn-sm btn-outline-primary me-1">Select All</button>
                    <button type="button" id="deselectAll" class="btn btn-sm btn-outline-secondary">Deselect All</button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-elegant align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width: 8%;" class="text-center">Include</th>
                            <th style="width: 42%;">Description</th>
                            <th style="width: 10%;" class="text-center">Qty</th>
                            <th style="width: 40%;">Item Remarks (Reason if unselected / notes)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($quotation->items as $item)
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="items[{{ $item->id }}][included]" value="1"
                                        class="form-check-input item-checkbox" checked style="transform: scale(1.2);">
                                </td>
                                <td>
                                    <span class="{{ $item->parent_id ? 'ms-3 text-muted' : 'fw-semibold' }}"
                                        style="color: var(--ink);">
                                        {{ $item->description }}
                                    </span>
                                </td>
                                <td class="text-center" style="color: var(--ink);">
                                    {{ rtrim(rtrim(number_format($item->quantity, 2), '0'), '.') }}</td>
                                <td>
                                    <input type="text" name="items[{{ $item->id }}][remarks]"
                                        class="form-control form-control-sm"
                                        placeholder="Optional remarks for this item...">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Execution & Signatory Settings Panel --}}
        <div class="panel mb-4 p-4"
            style="background: var(--card, #fff); border: 1px solid var(--line, #dee2e6); border-radius: 1rem;">
            <h5 class="fw-semibold mb-3" style="color: var(--ink);">Execution & Signatory Settings</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="executed_by" class="form-label small fw-semibold text-muted">Prepared / Executed By <span
                            class="fw-normal">(Optional)</span></label>
                    <input type="text" id="executed_by" name="executed_by" class="form-control"
                        value="{{ old('executed_by', $quotation->prepared_by) }}"
                        placeholder="Defaults to quotation preparer if left blank">
                </div>

                <div class="col-md-6">
                    <label for="signature_file" class="form-label small fw-semibold text-muted">Upload eSignature Image
                        <span class="fw-normal">(PNG/JPG)</span></label>
                    <input type="file" id="signature_file" name="signature_file" class="form-control"
                        accept="image/png, image/jpeg">
                    <div class="form-text small">Upload an image of your signature to embed it onto the CoC PDF.</div>
                </div>
            </div>
        </div>

        {{-- General Remarks / Notes Panel --}}
        <div class="panel mb-4 p-4"
            style="background: var(--card, #fff); border: 1px solid var(--line, #dee2e6); border-radius: 1rem;">
            <h5 class="fw-semibold mb-3" style="color: var(--ink);">General Completion Remarks / Notes</h5>
            <div class="mb-3">
                <textarea name="general_remarks" rows="4" class="form-control"
                    placeholder="Enter general project observations, warranty notes, or completion comments..."></textarea>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mb-4">
            <button type="submit" class="btn btn-primary px-4 py-2">
                <i class="bi bi-file-earmark-pdf me-1"></i> Generate Certificate of Completion PDF
            </button>
        </div>
    </form>

    @push('scripts')
        <script>
            document.getElementById('selectAll').addEventListener('click', function() {
                document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = true);
            });
            document.getElementById('deselectAll').addEventListener('click', function() {
                document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = false);
            });

            document.getElementById('cocForm').addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Generate Certificate of Completion?',
                    text: 'This will compile the selected service statuses and generate the official CoC PDF document.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#2b3a67',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, generate PDF!',
                    cancelButtonText: 'Cancel',
                    customClass: {
                        popup: 'rounded-4 shadow'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Generating PDF...',
                            text: 'Please wait while your document is being prepared.',
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
                        setTimeout(() => {
                            Swal.close();
                        }, 2500);
                    }
                });
            });
        </script>
    @endpush
@endsection
