@extends('layouts.schedule')

@section('schedule_content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="mb-1 fw-bold fs-4 fs-md-3 text-body">
                <i class="bi bi-file-earmark-bar-graph me-2"></i>Prepare Schedule Execution & Audit Report
            </h2>
            <p class="text-muted small mb-0">
                Review schedule summary, financial audits, staff payouts, operational expenses, and generate the final
                report PDF.
            </p>
        </div>
        <a href="{{ route('schedules.show', $schedule->id) }}" class="btn btn-outline-secondary btn-sm text-nowrap">
            <i class="bi bi-arrow-left me-1"></i> Back to Schedule
        </a>
    </div>

    <form action="{{ route('schedules.report-generate.pdf', $schedule->id) }}" method="POST" target="pdf-frame"
        id="reportForm" enctype="multipart/form-data">
        @csrf

        <div class="panel mb-4">
            <div class="p-4">
                <div class="row g-4 gy-lg-0 align-items-stretch">
                    <div class="col-12 col-lg-6 pe-lg-4 d-flex flex-column justify-content-between border-end-lg-0">
                        <div>
                            <h6 class="fw-semibold mb-3 text-body">Client Information</h6>
                            <div class="mb-3">
                                <span class="text-muted small d-block">Client / Contact Name</span>
                                <strong class="fs-6 text-body">{{ $schedule->quoted_to }}</strong>
                            </div>
                            <div class="mb-3">
                                <span class="text-muted small d-block">Company Name</span>
                                <span class="fw-medium text-body">{{ $schedule->client_company ?? '—' }}</span>
                            </div>
                            <div class="mb-3">
                                <span class="text-muted small d-block">Address</span>
                                <span class="small text-body">{{ $schedule->client_address ?? '—' }}</span>
                            </div>
                        </div>
                        <div class="row g-2 mt-2">
                            <div class="col-sm-6">
                                <span class="text-muted small d-block">Phone</span>
                                <span class="small text-body">{{ $schedule->client_phone ?? '—' }}</span>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted small d-block">Email</span>
                                <span class="small text-body text-break">{{ $schedule->contact_email ?? '—' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-6 ps-lg-4 mt-4 mt-lg-0 d-flex flex-column justify-content-between">
                        <div>
                            <h6 class="fw-semibold mb-3 text-body">Execution Overview</h6>
                            <div class="row g-3 mb-3">
                                <div class="col-sm-6">
                                    <span class="text-muted small d-block">Schedule Number</span>
                                    <strong class="text-primary font-monospace">{{ $schedule->schedule_number }}</strong>
                                </div>
                                <div class="col-sm-6">
                                    <span class="text-muted small d-block">Schedule Date</span>
                                    <span
                                        class="text-body">{{ $schedule->schedule_date ? $schedule->schedule_date->format('d/m/Y h:i A') : '—' }}</span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <span class="text-muted small d-block">Project / Reference</span>
                                <strong class="text-body">{{ $schedule->reference_project ?? '—' }}</strong>
                            </div>
                        </div>
                        <div class="p-3 rounded-3 bg-body-secondary border mt-3 mt-lg-0">
                            <span class="text-muted small d-block">Total Schedule Revenue</span>
                            <strong class="text-success fs-4">PHP {{ number_format($schedule->total, 2) }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @php
            $totalRevenue = $schedule->total;
            $totalPaidPersonnel = $schedule->personnel->where('is_paid', true)->sum('payment');
            $totalExpenses = $schedule->expenses->sum('amount');
            $netJobProfit = $totalRevenue - $totalPaidPersonnel - $totalExpenses;
        @endphp
        <div class="panel mb-4">
            <div class="p-4">
                <h6 class="fw-semibold mb-3 text-body">Financial Audit Preview (Paid Payouts & Expenses)</h6>
                <div class="row g-3">
                    <div class="col-6 col-md-3">
                        <div class="p-3 border rounded-3 bg-body-secondary">
                            <span class="text-muted small d-block">Total Revenue</span>
                            <strong class="fs-5 text-primary">PHP {{ number_format($totalRevenue, 2) }}</strong>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 border rounded-3 bg-body-secondary">
                            <span class="text-muted small d-block">Paid Staff Payouts (-)</span>
                            <strong class="fs-5 text-danger">PHP {{ number_format($totalPaidPersonnel, 2) }}</strong>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 border rounded-3 bg-body-secondary">
                            <span class="text-muted small d-block">Operational Expenses (-)</span>
                            <strong class="fs-5 text-danger">PHP {{ number_format($totalExpenses, 2) }}</strong>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 border rounded-3 bg-body-secondary">
                            <span class="text-muted small d-block">Net Job Profit</span>
                            <strong class="fs-5 {{ $netJobProfit >= 0 ? 'text-success' : 'text-danger' }}">
                                PHP {{ number_format($netJobProfit, 2) }}
                            </strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel mb-4">
            <div class="p-4">
                <h6 class="fw-semibold mb-3 text-body">Report Signatory Settings</h6>
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label for="executed_by" class="form-label text-body">Prepared / Audited By <span
                                class="text-muted fw-normal">(Optional)</span></label>
                        <input type="text" id="executed_by" name="executed_by" class="form-control"
                            value="{{ old('executed_by', $schedule->prepared_by) }}"
                            placeholder="Defaults to schedule preparer if left blank">
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="signature_file" class="form-label text-body">Upload eSignature Image <span
                                class="text-danger">*</span> <span class="text-muted fw-normal">(PNG/JPG)</span></label>
                        <input type="file" id="signature_file" name="signature_file" class="form-control"
                            accept="image/png, image/jpeg" required>
                        <div class="form-text small">An official image of your eSignature is required for the report
                            document.</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex flex-column-reverse flex-md-row justify-content-end gap-2 mb-4">
            <a href="{{ route('schedules.show', $schedule->id) }}"
                class="btn btn-outline-secondary px-4 py-2 w-100 w-md-auto">Cancel</a>
            <button id="generateReportPDF" type="submit" class="btn btn-primary px-4 py-2 shadow-sm w-100 w-md-auto">
                <i class="bi bi-file-earmark-pdf me-1"></i> Generate Report PDF Preview
            </button>
        </div>
    </form>

    <div class="modal fade" id="pdfModal" tabindex="-1" aria-labelledby="pdfModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center justify-content-between">
                    <h5 class="modal-title m-0" id="pdfModalLabel">
                        <i class="bi bi-file-earmark-pdf text-primary me-2"></i>Schedule Report Preview
                    </h5>
                    <button type="button" id="printPdfBtn" class="btn btn-sm btn-danger px-4" style="display: none;">
                        <i class="bi bi-printer me-1"></i> Print PDF
                    </button>
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
                            <span class="fw-semibold text-body">Generating Schedule Execution Report...</span>
                        </div>
                        <iframe name="pdf-frame" id="pdfFrame" class="w-100 h-100" style="border: none;"></iframe>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-outline-secondary px-4 ms-auto"
                        data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            (function() {
                if (window.reportScriptInitialized) return;
                window.reportScriptInitialized = true;

                const pdfModalEl = document.getElementById('pdfModal');
                const loadingOverlay = document.getElementById('pdfLoadingOverlay');
                const pdfFrame = document.getElementById('pdfFrame');
                const reportForm = document.getElementById('reportForm');
                const signatureInput = document.getElementById('signature_file');
                const printPdfBtn = document.getElementById('printPdfBtn');

                if (reportForm) {
                    reportForm.addEventListener('submit', function(e) {
                        e.preventDefault();

                        if (signatureInput && signatureInput.files.length === 0) {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    toast: true,
                                    position: 'bottom-end',
                                    icon: 'warning',
                                    title: 'Please upload an eSignature image before proceeding.',
                                    showConfirmButton: false,
                                    timer: 4000,
                                    timerProgressBar: true
                                });
                            } else {
                                alert('Please upload an eSignature image before proceeding.');
                            }
                            signatureInput.focus();
                            return;
                        }

                        if (pdfFrame) pdfFrame.src = 'about:blank';
                        if (printPdfBtn) printPdfBtn.style.display = 'none';

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

                        const formData = new FormData(reportForm);
                        const actionUrl = "{{ route('schedules.report-generate.pdf', $schedule->id) }}";

                        fetch(actionUrl, {
                                method: 'POST',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                },
                                body: formData
                            })
                            .then(response => {
                                if (!response.ok) {
                                    return response.json().then(errData => {
                                        throw errData;
                                    });
                                }
                                return response.blob();
                            })
                            .then(blob => {
                                const blobUrl = window.URL.createObjectURL(blob);
                                if (pdfFrame) {
                                    pdfFrame.src = blobUrl + '#toolbar=0&navpanes=0&scrollbar=0';
                                }
                                if (printPdfBtn) {
                                    printPdfBtn.style.display = 'inline-block';
                                    printPdfBtn.onclick = function() {
                                        if (pdfFrame && pdfFrame.contentWindow) {
                                            pdfFrame.contentWindow.print();
                                        }
                                    };
                                }
                            })
                            .catch(error => {
                                if (pdfModalEl) {
                                    pdfModalEl.classList.remove('show');
                                    pdfModalEl.style.display = 'none';
                                    pdfModalEl.setAttribute('aria-hidden', 'true');
                                    pdfModalEl.removeAttribute('aria-modal');
                                }
                                document.body.classList.remove('modal-open');
                                const backdrop = document.querySelector('.modal-backdrop');
                                if (backdrop) backdrop.remove();

                                alert('Failed to generate report PDF. Please check your file upload.');
                            })
                            .finally(() => {
                                if (loadingOverlay) {
                                    loadingOverlay.classList.remove('d-flex');
                                    loadingOverlay.classList.add('d-none');
                                }
                            });
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
                        if (pdfFrame) pdfFrame.src = 'about:blank';
                    });
                });
            })();
        </script>
    @endpush
@endsection
