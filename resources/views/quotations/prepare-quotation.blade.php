    @extends('layouts.quotation')

    @section('quotation_content')
        <div
            class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
            <div>
                <h2 class="mb-1 fw-bold fs-4 fs-md-3 text-body">
                    <i class="bi bi-file-earmark-text me-2"></i>Prepare Quotation
                </h2>
                <p class="text-muted small mb-0">
                    Review quotation details, configure terms, upload your eSignature, and preview or email the document.
                </p>
            </div>
            <a href="{{ route('quotations.show', $quotation->id) }}" class="btn btn-outline-secondary btn-sm text-nowrap">
                <i class="bi bi-arrow-left me-1"></i> Back to Quotation
            </a>
        </div>

        <form action="{{ route('quotations.generate.pdf', $quotation->id) }}" method="POST" target="pdf-frame"
            id="quotationForm" enctype="multipart/form-data">
            @csrf

            {{-- Client & Overview Panel --}}
            <div class="panel mb-4">
                <div class="p-4">
                    <div class="row g-4">
                        <div class="col-lg-6">
                            <h6 class="fw-semibold mb-3 text-body">Client Information</h6>
                            <div class="mb-3">
                                <span class="text-muted small d-block">Client / Contact Name</span>
                                <strong class="fs-6 text-body">{{ $quotation->quoted_to }}</strong>
                            </div>
                            <div class="mb-3">
                                <span class="text-muted small d-block">Company</span>
                                <span class="fw-medium text-body">{{ $quotation->client_company ?? '—' }}</span>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <h6 class="fw-semibold mb-3 text-body">Quotation Details</h6>
                            <div class="mb-3">
                                <span class="text-muted small d-block">Quotation Number</span>
                                <strong class="text-primary font-monospace">{{ $quotation->quotation_number }}</strong>
                            </div>
                            <div class="p-3 rounded-3 bg-body-secondary border">
                                <span class="text-muted small d-block">Total Amount</span>
                                <strong class="text-success fs-4">PHP {{ number_format($quotation->total, 2) }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Signatory Settings Panel (Required Signature) --}}
            <div class="panel mb-4">
                <div class="p-4">
                    <h6 class="fw-semibold mb-3 text-body">Signatory Settings</h6>
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label for="signature_file" class="form-label text-body">
                                Upload eSignature Image <span class="text-danger">*</span> <span
                                    class="text-muted fw-normal">(PNG/JPG)</span>
                            </label>
                            <input type="file" id="signature_file" name="signature_file" class="form-control"
                                accept="image/png, image/jpeg" required>
                            <div class="form-text small">An official image of your eSignature is required to generate and
                                email the quotation.</div>
                        </div>
                    </div>
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

        {{-- Bootstrap Modal containing iFrame & Email Option --}}
        <div class="modal fade" id="pdfModal" tabindex="-1" aria-labelledby="pdfModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="pdfModalLabel">
                            <i class="bi bi-file-earmark-pdf text-primary me-2"></i>Quotation Document Preview
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="position-relative ratio ratio-16x9 mb-4 border rounded overflow-hidden shadow-sm"
                            style="min-height: 500px;">
                            <div id="pdfLoadingOverlay"
                                class="position-absolute top-0 start-0 w-100 h-100 d-none flex-column justify-content-center align-items-center bg-body bg-opacity-75 z-3">
                                <div class="spinner-border text-primary mb-2" role="status"
                                    style="width: 3rem; height: 3rem;">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <span class="fw-semibold text-body">Generating Quotation PDF...</span>
                            </div>
                            <iframe name="pdf-frame" id="pdfFrame" class="w-100 h-100" style="border: none;"></iframe>
                        </div>

                        @if (auth()->user()->hasPermission('quotations.send.email'))
                            {{-- Email Dispatch Section --}}
                            <div class="card bg-body-secondary border-0 p-3">
                                <label for="recipientEmail" class="form-label fw-semibold text-body">
                                    <i class="bi bi-envelope me-1"></i> Send Quotation via Email
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
                    if (window.quoteScriptInitialized) return;
                    window.quoteScriptInitialized = true;

                    const pdfModalEl = document.getElementById('pdfModal');
                    const loadingOverlay = document.getElementById('pdfLoadingOverlay');
                    const pdfFrame = document.getElementById('pdfFrame');
                    const quotationForm = document.getElementById('quotationForm');
                    const signatureInput = document.getElementById('signature_file');

                    if (quotationForm) {
                        quotationForm.addEventListener('submit', function(e) {
                            if (signatureInput && signatureInput.files.length === 0) {
                                e.preventDefault();
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Missing eSignature',
                                    text: 'Please upload an eSignature image before proceeding.',
                                    confirmButtonColor: '#2b3a67',
                                    customClass: {
                                        popup: 'rounded-4 shadow'
                                    }
                                }).then(() => {
                                    signatureInput.focus();
                                });
                                return;
                            }

                            quotationForm.action = "{{ route('quotations.generate.pdf', $quotation->id) }}";
                            quotationForm.removeAttribute('target');
                            quotationForm.target = "pdf-frame";

                            if (pdfFrame) pdfFrame.src = 'about:blank';
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
                            if (pdfFrame) pdfFrame.src = 'about:blank';
                        });
                    });

                    const sendEmailBtn = document.getElementById('sendEmailBtn');
                    if (sendEmailBtn) {
                        sendEmailBtn.addEventListener('click', function() {
                            if (signatureInput && signatureInput.files.length === 0) {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Missing eSignature',
                                    text: 'Please upload an eSignature image before sending the email.',
                                    confirmButtonColor: '#2b3a67',
                                    customClass: {
                                        popup: 'rounded-4 shadow'
                                    }
                                }).then(() => {
                                    document.querySelector('[data-bs-dismiss="modal"]').click();
                                    signatureInput.focus();
                                });
                                return;
                            }

                            const emailInput = document.getElementById('recipientEmail');
                            const email = emailInput ? emailInput.value.trim() : '';
                            if (!email) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Email Required',
                                    text: 'Please enter a valid email address.',
                                    confirmButtonColor: '#2b3a67',
                                    customClass: {
                                        popup: 'rounded-4 shadow'
                                    }
                                });
                                return;
                            }

                            // --- Added Confirmation Step ---
                            Swal.fire({
                                title: 'Are you sure?',
                                text: `Do you want to send this quotation to ${email}?`,
                                icon: 'question',
                                showCancelButton: true,
                                confirmButtonColor: '#2b3a67',
                                cancelButtonColor: '#6c757d',
                                confirmButtonText: 'Yes, send it!',
                                cancelButtonText: 'Cancel',
                                customClass: {
                                    popup: 'rounded-4 shadow'
                                }
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // Show loading indicator
                                    Swal.fire({
                                        title: 'Sending Email...',
                                        text: 'Please wait while we generate and dispatch the Quotation.',
                                        allowOutsideClick: false,
                                        allowEscapeKey: false,
                                        showConfirmButton: false,
                                        target: 'body',
                                        didOpen: () => {
                                            Swal.showLoading();
                                        }
                                    });

                                    let emailField = quotationForm.querySelector(
                                        'input[name="recipient_email"]');
                                    if (!emailField) {
                                        emailField = document.createElement('input');
                                        emailField.type = 'hidden';
                                        emailField.name = 'recipient_email';
                                        quotationForm.appendChild(emailField);
                                    }
                                    emailField.value = email;

                                    quotationForm.action = "{{ route('quotations.email', $quotation->id) }}";
                                    quotationForm.removeAttribute('target');
                                    quotationForm.submit();
                                }
                            });
                        });
                    }
                })();
            </script>
        @endpush
    @endsection
