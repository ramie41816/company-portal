@extends('layouts.quotation')

@section('quotation_content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="mb-1 fw-bold fs-4 fs-md-3 text-body">
                <i class="bi bi-file-earmark-check me-2"></i>Prepare Certificate of Completion / Treatment
            </h2>
            <p class="text-muted small mb-0">
                Review quotation summary, select executed services, add item remarks, configure execution settings, and
                generate CoC.
            </p>
        </div>
        <a href="{{ route('quotations.show', $quotation->id) }}" class="btn btn-outline-secondary btn-sm text-nowrap">
            <i class="bi bi-arrow-left me-1"></i> Back to Quotation
        </a>
    </div>

    {{-- Main Form for PDF Preview --}}
    <form action="{{ route('quotations.coc-generate.pdf', $quotation->id) }}" method="POST" target="pdf-frame" id="cocForm"
        enctype="multipart/form-data">
        @csrf

        {{-- Client & Quotation Overview Panel --}}
        <div class="panel mb-4">
            <div class="p-4">
                <div class="row g-4 gy-lg-0 align-items-stretch">
                    {{-- Client Information Column --}}
                    <div class="col-lg-6 pe-lg-4 d-flex flex-column justify-content-between border-end-lg-0">
                        <div>
                            <h6 class="fw-semibold mb-3 text-body">Client Information (Quote To)</h6>
                            <div class="mb-3">
                                <span class="text-muted small d-block">Client / Contact Name</span>
                                <strong class="fs-6 text-body">{{ $quotation->quoted_to }}</strong>
                            </div>
                            <div class="mb-3">
                                <span class="text-muted small d-block">Company Name</span>
                                <span class="fw-medium text-body">{{ $quotation->client_company ?? '—' }}</span>
                            </div>
                            <div class="mb-3">
                                <span class="text-muted small d-block">Address</span>
                                <span class="small text-body">{{ $quotation->client_address ?? '—' }}</span>
                            </div>
                        </div>
                        <div class="row g-2 mt-2">
                            <div class="col-sm-6">
                                <span class="text-muted small d-block">Phone</span>
                                <span class="small text-body">{{ $quotation->client_phone ?? '—' }}</span>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted small d-block">Email</span>
                                <span class="small text-body text-break">{{ $quotation->contact_email ?? '—' }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Quotation Overview Column --}}
                    <div class="col-lg-6 ps-lg-4 mt-4 mt-lg-0 d-flex flex-column justify-content-between">
                        <div>
                            <h6 class="fw-semibold mb-3 text-body">Quotation Overview</h6>
                            <div class="row g-3 mb-3">
                                <div class="col-sm-6">
                                    <span class="text-muted small d-block">Quotation Number</span>
                                    <strong class="text-primary font-monospace">{{ $quotation->quotation_number }}</strong>
                                </div>
                                <div class="col-sm-6">
                                    <span class="text-muted small d-block">Issue Date</span>
                                    <span class="text-body">{{ $quotation->quotation_date->format('d/m/Y') }}</span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <span class="text-muted small d-block">Project / Reference</span>
                                <strong class="text-body">{{ $quotation->reference_project ?? '—' }}</strong>
                            </div>
                        </div>
                        <div class="p-3 rounded-3 bg-body-secondary border mt-3 mt-lg-0">
                            <span class="text-muted small d-block">Total Quotation Amount</span>
                            <strong class="text-success fs-4">PHP {{ number_format($quotation->total, 2) }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Scope of Work & Items Checkbox Selection Panel --}}
        <div class="panel mb-4">
            <div class="p-4">
                <div
                    class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-3">
                    <div>
                        <h6 class="fw-semibold mb-1 text-body">Executed Services & Item Remarks</h6>
                        <p class="text-muted small mb-0">Check items completed during the project execution.</p>
                    </div>
                    <div class="btn-group btn-group-sm shadow-sm">
                        <button type="button" id="selectAll" class="btn btn-outline-primary px-3">Select All</button>
                        <button type="button" id="deselectAll" class="btn btn-outline-secondary px-3">Deselect All</button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-elegant align-middle mb-0" id="coc-items-table">
                        <thead>
                            <tr>
                                <th style="width: 8%;" class="text-center">Include</th>
                                <th style="width: 42%;">Description</th>
                                <th style="width: 10%;" class="text-center">Qty</th>
                                <th style="width: 40%;">Item Remarks (Optional notes / reason)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $mainItems = $quotation->items->whereNull('parent_id');
                                $x = 1;
                            @endphp
                            @foreach ($mainItems as $item)
                                @php
                                    $subItems = $quotation->items->where('parent_id', $item->id);
                                    $y = 1;
                                @endphp
                                {{-- Main Item Row --}}
                                <tr>
                                    <td class="text-center align-middle">
                                        <input type="checkbox" name="items[{{ $item->id }}][included]" value="1"
                                            class="form-check-input item-checkbox shadow-none" checked
                                            style="width: 1.25rem; height: 1.25rem; cursor: pointer;">
                                    </td>
                                    <td class="align-middle">
                                        <span class="fw-semibold text-body me-2">{{ $x }}.</span>
                                        <span class="fw-semibold text-body">{{ $item->description }}</span>
                                    </td>
                                    <td class="text-center text-muted align-middle">
                                        {{ $item->unit_price > 0 && $item->quantity > 0 ? rtrim(rtrim(number_format($item->quantity, 2), '0'), '.') : '-' }}
                                    </td>
                                    <td class="align-middle">
                                        <input type="text" name="items[{{ $item->id }}][remarks]"
                                            class="form-control form-control-sm" placeholder="Add remarks or notes...">
                                    </td>
                                </tr>

                                {{-- Sub-items Rows --}}
                                @foreach ($subItems as $subItem)
                                    @php
                                        $numbering = $x . '.' . $y;
                                    @endphp
                                    <tr class="sub-item-row">
                                        <td class="text-center align-middle">
                                            <input type="checkbox" name="items[{{ $subItem->id }}][included]"
                                                value="1" class="form-check-input item-checkbox shadow-none" checked
                                                style="width: 1.25rem; height: 1.25rem; cursor: pointer;">
                                        </td>
                                        <td class="align-middle" style="padding-left: 2rem !important;">
                                            <span style="font-weight: bold; margin-right: 5px;">{{ $numbering }}</span>
                                            <span class="text-muted">{{ $subItem->description }}</span>
                                        </td>
                                        <td class="text-center text-muted align-middle">
                                            {{ $subItem->unit_price > 0 || $subItem->quantity > 0 ? rtrim(rtrim(number_format($subItem->quantity, 2), '0'), '.') : '—' }}
                                        </td>
                                        <td class="align-middle">
                                            <input type="text" name="items[{{ $subItem->id }}][remarks]"
                                                class="form-control form-control-sm" placeholder="Add remarks or notes...">
                                        </td>
                                    </tr>
                                    @php $y++; @endphp
                                @endforeach

                                @php $x++; @endphp
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Execution & Signatory Settings Panel --}}
        <div class="panel mb-4">
            <div class="p-4">
                <h6 class="fw-semibold mb-3 text-body">Execution & Signatory Settings</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="executed_by" class="form-label text-body">Prepared / Executed By <span
                                class="text-muted fw-normal">(Optional)</span></label>
                        <input type="text" id="executed_by" name="executed_by" class="form-control"
                            value="{{ old('executed_by', $quotation->prepared_by) }}"
                            placeholder="Defaults to quotation preparer if left blank">
                    </div>

                    <div class="col-md-6">
                        <label for="signature_file" class="form-label text-body">Upload eSignature Image <span
                                class="text-danger">*</span> <span class="text-muted fw-normal">(PNG/JPG)</span></label>
                        <input type="file" id="signature_file" name="signature_file" class="form-control"
                            accept="image/png, image/jpeg" required>
                        <div class="form-text small">An official image of your eSignature is required for the CoC document.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- General Remarks / Notes Panel --}}
        <div class="panel mb-4">
            <div class="p-4">
                <h6 class="fw-semibold mb-2 text-body">General Completion Remarks / Notes <span
                        class="text-muted fw-normal">(optional)</span></h6>
                <textarea name="general_remarks" rows="4" class="form-control"
                    placeholder="Enter general project observations, warranty notes, or completion comments..."></textarea>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mb-4">
            <a href="{{ route('quotations.show', $quotation->id) }}"
                class="btn btn-outline-secondary px-4 py-2">Cancel</a>
            <button id="generatePDF" type="submit" class="btn btn-primary px-4 py-2 shadow-sm">
                <i class="bi bi-file-earmark-pdf me-1"></i> Generate PDF Preview
            </button>
        </div>
    </form>

    {{-- Bootstrap Modal containing the iFrame Preview with Loading Overlay and Email Option --}}
    <div class="modal fade" id="pdfModal" tabindex="-1" aria-labelledby="pdfModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pdfModalLabel">
                        <i class="bi bi-file-earmark-pdf text-primary me-2"></i>Certificate of Completion Preview
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- iFrame Preview Container with Loading Overlay --}}
                    <div class="position-relative ratio ratio-16x9 mb-4 border rounded overflow-hidden shadow-sm"
                        style="min-height: 500px;">

                        {{-- Loading Spinner Overlay --}}
                        <div id="pdfLoadingOverlay"
                            class="position-absolute top-0 start-0 w-100 h-100 d-none flex-column justify-content-center align-items-center bg-body bg-opacity-75 z-3">
                            <div class="spinner-border text-primary mb-2" role="status"
                                style="width: 3rem; height: 3rem;">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <span class="fw-semibold text-body">Generating Certificate of Completion PDF...</span>
                        </div>

                        <iframe name="pdf-frame" id="pdfFrame" class="w-100 h-100" style="border: none;"></iframe>
                    </div>

                    @if (auth()->user()->hasPermission('quotations.coc.send.email'))
                        {{-- Email Dispatch Section --}}
                        <div class="card bg-body-secondary border-0 p-3">
                            <label for="recipientEmail" class="form-label fw-semibold text-body">
                                <i class="bi bi-envelope me-1"></i> Send CoC via Email
                            </label>
                            <div class="row g-2">
                                <div class="col-md-9">
                                    <input type="email" id="recipientEmail" class="form-control"
                                        value="{{ $quotation->contact_email }}"
                                        placeholder="Enter recipient email address...">
                                </div>
                                <div class="col-md-3">
                                    <button type="button" id="sendEmailBtn" class="btn btn-success w-100 shadow-sm">
                                        <i class="bi bi-send me-1"></i> Send Email
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            (function() {
                if (window.cocScriptInitialized) return;
                window.cocScriptInitialized = true;

                document.getElementById('selectAll').addEventListener('click', function() {
                    document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = true);
                });
                document.getElementById('deselectAll').addEventListener('click', function() {
                    document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = false);
                });

                const pdfModalEl = document.getElementById('pdfModal');
                const loadingOverlay = document.getElementById('pdfLoadingOverlay');
                const pdfFrame = document.getElementById('pdfFrame');
                const cocForm = document.getElementById('cocForm');
                const signatureInput = document.getElementById('signature_file');

                if (cocForm) {
                    cocForm.addEventListener('submit', function(e) {
                        // Check if signature file is provided
                        if (signatureInput && signatureInput.files.length === 0) {
                            e.preventDefault();
                            alert('Please upload an eSignature image before proceeding.');
                            signatureInput.focus();
                            return;
                        }

                        // Ensure form targets the iframe for preview
                        cocForm.action = "{{ route('quotations.coc-generate.pdf', $quotation->id) }}";
                        cocForm.removeAttribute('target');
                        cocForm.target = "pdf-frame";

                        if (pdfFrame) {
                            pdfFrame.src = 'about:blank';
                        }

                        if (loadingOverlay) {
                            loadingOverlay.classList.remove('d-none');
                            loadingOverlay.classList.add('d-flex');
                        }

                        if (pdfModalEl) {
                            pdfModalEl.classList.add('show');
                            pdfModalEl.style.display = 'block';
                            pdfModalEl.setAttribute('aria-hidden', 'false');
                            pdfModalEl.setAttribute('aria-modal', 'true');
                        }
                        document.body.classList.add('modal-open');

                        if (!document.querySelector('.modal-backdrop')) {
                            const backdrop = document.createElement('div');
                            backdrop.className = 'modal-backdrop fade show';
                            document.body.appendChild(backdrop);
                        }

                        setTimeout(function() {
                            if (loadingOverlay) {
                                loadingOverlay.classList.remove('d-flex');
                                loadingOverlay.classList.add('d-none');
                            }
                        }, 1500);
                    });
                }

                document.querySelectorAll('[data-bs-dismiss="modal"]').forEach(button => {
                    button.addEventListener('click', function() {
                        if (pdfModalEl) {
                            pdfModalEl.classList.remove('show');
                            pdfModalEl.style.display = 'none';
                            pdfModalEl.setAttribute('aria-hidden', 'true');
                            pdfModalEl.removeAttribute('aria-modal');
                        }
                        document.body.classList.remove('modal-open');
                        const backdrop = document.querySelector('.modal-backdrop');
                        if (backdrop) backdrop.remove();

                        if (loadingOverlay) {
                            loadingOverlay.classList.remove('d-flex');
                            loadingOverlay.classList.add('d-none');
                        }
                        if (pdfFrame) {
                            pdfFrame.src = 'about:blank';
                        }
                    });
                });

                // Standard Form Submission for Email with SweetAlert2 Loading State
                const sendEmailBtn = document.getElementById('sendEmailBtn');
                if (sendEmailBtn) {
                    sendEmailBtn.addEventListener('click', function() {
                        // Check signature validation first
                        if (signatureInput && signatureInput.files.length === 0) {
                            alert('Please upload an eSignature image before sending the email.');
                            document.querySelector('[data-bs-dismiss="modal"]').click();
                            signatureInput.focus();
                            return;
                        }

                        const emailInput = document.getElementById('recipientEmail');
                        const email = emailInput ? emailInput.value : '';
                        if (!email) {
                            alert('Please enter a valid email address.');
                            return;
                        }

                        // Trigger SweetAlert2 Loading Dialog
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: 'Sending Email...',
                                text: 'Please wait while we generate and dispatch the Certificate of Completion.',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                showConfirmButton: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                        }

                        // Dynamically append or update recipient email field inside the main form
                        let emailField = cocForm.querySelector('input[name="recipient_email"]');
                        if (!emailField) {
                            emailField = document.createElement('input');
                            emailField.type = 'hidden';
                            emailField.name = 'recipient_email';
                            cocForm.appendChild(emailField);
                        }
                        emailField.value = email;

                        // Switch form action to the email route and remove target
                        cocForm.action = "{{ route('quotations.coc-email', $quotation->id) }}";
                        cocForm.removeAttribute('target');

                        // Submit form normally
                        cocForm.submit();
                    });
                }
            })();
        </script>
    @endpush
@endsection
