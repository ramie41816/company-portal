@php
    $isEdit = isset($schedule) && $schedule->exists;
    $currency = config('company.currency_symbol');

    if (!isset($savedItems)) {
        $savedItems = (isset($schedule) && $schedule->relationLoaded('items') ? $schedule->items : collect())
            ->map(
                fn($item) => [
                    'id' => $item->id,
                    'parent_id' => $item->parent_id,
                    'description' => $item->description,
                    'unit_price' => (float) $item->unit_price,
                    'quantity' => (float) $item->quantity,
                ],
            )
            ->all();

        if (empty($savedItems)) {
            $savedItems = [
                ['id' => null, 'parent_id' => null, 'description' => '', 'unit_price' => '', 'quantity' => 1],
            ];
        }
    }

    $oldItems = array_values(old('items', $savedItems));
@endphp
@if ($errors->any())
    <div class="alert alert-danger border-0 shadow-sm mb-4">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ $action }}" id="scheduleForm">
    @csrf
    @if ($isEdit)
        @method('PUT')
        <div class="panel mb-4">
            <div class="p-4">
                <h6 class="fw-semibold mb-3">Status & Remarks</h6>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="status" class="form-label">Status</label>
                        <select id="status" name="status" class="form-select">
                            <option value="scheduled"
                                {{ old('status', $schedule->status ?? '') == 'scheduled' ? 'selected' : '' }}>Scheduled
                            </option>
                            <option value="ongoing"
                                {{ old('status', $schedule->status ?? '') == 'ongoing' ? 'selected' : '' }}>Ongoing
                            </option>
                            <option value="completed"
                                {{ old('status', $schedule->status ?? '') == 'completed' ? 'selected' : '' }}>Completed
                            </option>
                            <option value="cancelled"
                                {{ old('status', $schedule->status ?? '') == 'cancelled' ? 'selected' : '' }}>Cancelled
                            </option>
                        </select>
                    </div>
                    <div class="col-md-8">
                        <label for="remarks" class="form-label">Remarks / Notes</label>
                        <input id="remarks" type="text" name="remarks"
                            value="{{ old('remarks', $schedule->remarks ?? '') }}" class="form-control"
                            placeholder="Internal remarks...">
                    </div>
                </div>
            </div>
        </div>
    @else
        <input type="hidden" name="status" value="scheduled">
    @endif

    @if (isset($schedule->quotation_id) && $schedule->quotation_id)
        <div class="alert alert-info border-0 shadow-sm mb-4 d-flex align-items-center justify-content-between">
            <div>
                <i class="bi bi-link-45deg me-1"></i> Generated from Quotation:
                <strong>{{ optional($schedule->quotation)->quotation_number }}</strong>
            </div>
            <input type="hidden" name="quotation_id" value="{{ $schedule->quotation_id }}">
        </div>
    @endif

    {{-- Customer & Project Details --}}
    <div class="panel mb-4">
        <div class="p-4">
            <h6 class="fw-semibold mb-3">Customer & Project Details</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="quoted_to" class="form-label">Client Name (To)</label>
                    <input id="quoted_to" type="text" name="quoted_to"
                        value="{{ old('quoted_to', $schedule->quoted_to ?? '') }}" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label for="client_company" class="form-label">Company Name <span
                            class="text-muted fw-normal">(optional)</span></label>
                    <input id="client_company" type="text" name="client_company"
                        value="{{ old('client_company', $schedule->client_company ?? '') }}" class="form-control">
                </div>
                <div class="col-md-12">
                    <label for="client_address" class="form-label">Address <span
                            class="text-muted fw-normal">(optional)</span></label>
                    <input id="client_address" type="text" name="client_address"
                        value="{{ old('client_address', $schedule->client_address ?? '') }}" class="form-control">
                </div>
                <div class="col-md-6">
                    <label for="client_phone" class="form-label">Phone Number <span
                            class="text-muted fw-normal">(optional)</span></label>
                    <input id="client_phone" type="text" name="client_phone"
                        value="{{ old('client_phone', $schedule->client_phone ?? '') }}" class="form-control">
                </div>
                <div class="col-md-6">
                    <label for="contact_email" class="form-label">Email Address <span
                            class="text-muted fw-normal">(optional)</span></label>
                    <input id="contact_email" type="email" name="contact_email"
                        value="{{ old('contact_email', $schedule->contact_email ?? '') }}" class="form-control">
                </div>
                <div class="col-md-6">
                    <label for="attention" class="form-label">Contact Person (Attention)</label>
                    <input id="attention" type="text" name="attention"
                        value="{{ old('attention', $schedule->attention ?? '') }}" class="form-control">
                </div>
                <div class="col-md-6">
                    <label for="prepared_by" class="form-label">Prepared By</label>
                    <input id="prepared_by" type="text" name="prepared_by"
                        value="{{ old('prepared_by', $schedule->prepared_by ?? '') }}" class="form-control">
                </div>
                <div class="col-md-4">
                    <label for="schedule_date" class="form-label">Schedule Date & Time</label>
                    <input id="schedule_date" type="datetime-local" name="schedule_date"
                        value="{{ old('schedule_date', $isEdit && $schedule->schedule_date ? $schedule->schedule_date->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}"
                        class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label for="start_date" class="form-label">Start Date & Time <span
                            class="text-muted fw-normal">(optional)</span></label>
                    <input id="start_date" type="datetime-local" name="start_date"
                        value="{{ old('start_date', $isEdit && $schedule->start_date ? $schedule->start_date->format('Y-m-d\TH:i') : '') }}"
                        class="form-control">
                </div>
                <div class="col-md-4">
                    <label for="end_date" class="form-label">End Date & Time <span
                            class="text-muted fw-normal">(optional)</span></label>
                    <input id="end_date" type="datetime-local" name="end_date"
                        value="{{ old('end_date', $isEdit && $schedule->end_date ? $schedule->end_date->format('Y-m-d\TH:i') : '') }}"
                        class="form-control">
                </div>
                <div class="col-md-12">
                    <label for="reference_project" class="form-label">Reference / Project</label>
                    <input id="reference_project" type="text" name="reference_project"
                        value="{{ old('reference_project', $schedule->reference_project ?? '') }}"
                        class="form-control">
                </div>
            </div>
        </div>
    </div>

    {{-- Scope of work & Items --}}
    <div class="panel mb-4">
        <div class="p-4 pb-3 d-flex justify-content-between align-items-center">
            <div>
                <h6 class="fw-semibold mb-1">Scope of Work & Services</h6>
                <p class="text-muted small mb-0">Add services and hierarchical child sub-items.</p>
            </div>
            <button type="button" id="add-row" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-plus-lg me-1"></i> Add Main Item
            </button>
        </div>

        <div class="table-responsive px-4 pb-4">
            <table class="table table-elegant align-middle" id="items-table">
                <thead>
                    <tr>
                        <th style="min-width: 320px;">Service / Item Description</th>
                        <th style="width: 170px;">Unit Price ({{ $currency }})</th>
                        <th style="width: 130px;">Quantity</th>
                        <th style="width: 150px;" class="text-end">Total</th>
                        <th style="width: 110px;" class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-end fw-semibold pt-3">Grand Total</td>
                        <td class="text-end fw-bold fs-5 text-brand pt-3" id="grand-total">{{ $currency }}0.00
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Rich Text Notes --}}
    <div class="panel mb-4">
        <div class="p-4">
            <h6 class="fw-semibold mb-2">Other Information / Notes</h6>
            <div id="editor-container" style="height: 180px;">
                {!! old('other_info', $schedule->other_info ?? '') !!}
            </div>
            <input type="hidden" name="other_info" id="other_info"
                value="{{ old('other_info', $schedule->other_info ?? '') }}">
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mb-4">
        <a href="{{ route('schedules.index') }}" class="btn btn-outline-secondary">Cancel</a>
        <button type="submit" class="btn btn-brand">
            <i class="bi bi-check2-circle me-1"></i> {{ $isEdit ? 'Update Schedule' : 'Save Schedule' }}
        </button>
    </div>
</form>

<style>
    .sub-item-row td {
        background-color: var(--bs-tertiary-bg, rgba(0, 0, 0, 0.02));
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const editorEl = document.querySelector('#editor-container');
        let quill = null;
        if (typeof Quill !== 'undefined' && editorEl && !editorEl.classList.contains('ql-container')) {
            quill = new Quill('#editor-container', {
                theme: 'snow',
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline'],
                        [{
                            'list': 'ordered'
                        }, {
                            'list': 'bullet'
                        }],
                        ['clean']
                    ]
                }
            });
            const hiddenInput = document.querySelector('#other_info');
            quill.on('text-change', function() {
                const htmlContent = quill.root.innerHTML;
                hiddenInput.value = (htmlContent === '<p><br></p>' || htmlContent.trim() === '') ? '' :
                    htmlContent;
            });
        }

        const oldItems = @json($oldItems);
        const currency = @json($currency);
        const tbody = document.querySelector('#items-table tbody');
        let globalIndex = 0;

        // SweetAlert confirmation logic on form submit (matching quotations)
        document.getElementById('scheduleForm').addEventListener('submit', function(e) {
            e.preventDefault();

            if (quill) {
                const hiddenInput = document.querySelector('#other_info');
                const htmlContent = quill.root.innerHTML;
                hiddenInput.value = (htmlContent === '<p><br></p>' || htmlContent.trim() === '') ? '' :
                    htmlContent;
            }

            const isEdit = @json($isEdit);
            const actionText = isEdit ? 'update this schedule' : 'save this new schedule';

            Swal.fire({
                title: 'Are you sure?',
                text: `Do you want to ${actionText}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2b3a67',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, proceed!',
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

        function formatMoney(amount) {
            return currency + amount.toLocaleString('en-PH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function renderRow(item = {}, isSubItem = false, insertAfterRow = null) {
            const i = globalIndex++;
            const tr = document.createElement('tr');
            tr.className = isSubItem ? 'sub-item-row main-item-sub' : 'main-item-row';
            tr.dataset.index = i;
            if (isSubItem) tr.dataset.isSub = "1";

            const paddingLeft = isSubItem ? 'ps-4' : '';
            const badgeIcon = isSubItem ?
                '<span class="text-muted small me-2"><i class="bi bi-arrow-return-right"></i> Sub:</span>' :
                '<span class="badge bg-secondary me-2">Main</span>';
            const safeDescription = (item.description ?? '').replace(/"/g, '&quot;').replace(/'/g, '&#39;');

            tr.innerHTML = `
                <td class="p-2 ${paddingLeft}">
                    <div class="d-flex align-items-center">
                        ${badgeIcon}
                        <input type="text" name="items[${i}][description]" class="form-control form-control-sm flex-grow-1" placeholder="Description..." value="${safeDescription}" required>
                        <input type="hidden" name="items[${i}][is_sub]" value="${isSubItem ? '1' : '0'}">
                    </div>
                </td>
                <td class="p-2 align-middle">
                    <input type="number" step="0.01" min="0" name="items[${i}][unit_price]" class="form-control form-control-sm price" value="${item.unit_price ?? ''}" required>
                </td>
                <td class="p-2 align-middle">
                    <input type="number" step="0.01" min="0.01" name="items[${i}][quantity]" class="form-control form-control-sm qty" value="${item.quantity ?? 1}" required>
                </td>
                <td class="text-end fw-medium line-total align-middle">${formatMoney(0)}</td>
                <td class="p-2 text-end align-middle">
                    <div class="btn-group" role="group">
                        ${!isSubItem ? `<button type="button" class="btn btn-sm btn-outline-primary add-sub" title="Add Sub-item"><i class="bi bi-plus-lg"></i></button>` : ''}
                        <button type="button" class="btn btn-sm btn-outline-danger remove" title="Remove"><i class="bi bi-x-lg"></i></button>
                    </div>
                </td>
            `;

            if (insertAfterRow && insertAfterRow.parentNode) {
                insertAfterRow.parentNode.insertBefore(tr, insertAfterRow.nextSibling);
            } else {
                tbody.appendChild(tr);
            }
            recalc();
        }

        function recalc() {
            let grand = 0;
            tbody.querySelectorAll('tr').forEach(tr => {
                const price = parseFloat(tr.querySelector('.price').value) || 0;
                const qty = parseFloat(tr.querySelector('.qty').value) || 0;
                const total = price * qty;
                tr.querySelector('.line-total').textContent = formatMoney(total);
                grand += total;
            });
            document.getElementById('grand-total').textContent = formatMoney(grand);
        }

        tbody.addEventListener('input', recalc);

        tbody.addEventListener('click', e => {
            if (e.target.closest('.remove')) {
                const tr = e.target.closest('tr');
                if (!tr.classList.contains('main-item-sub')) {
                    let next = tr.nextElementSibling;
                    while (next && next.classList.contains('main-item-sub')) {
                        let toRemove = next;
                        next = next.nextElementSibling;
                        toRemove.remove();
                    }
                }
                tr.remove();
                recalc();
            }
            if (e.target.closest('.add-sub')) {
                const mainTr = e.target.closest('tr');
                let insertTarget = mainTr;
                let nextRow = mainTr.nextElementSibling;
                while (nextRow && nextRow.classList.contains('main-item-sub')) {
                    insertTarget = nextRow;
                    nextRow = nextRow.nextElementSibling;
                }
                renderRow({}, true, insertTarget);
            }
        });

        document.getElementById('add-row').addEventListener('click', () => renderRow());

        if (oldItems.length > 0) {
            oldItems.forEach(item => {
                const isSub = item.parent_id !== null && item.parent_id !== undefined && item
                    .parent_id !== "";
                renderRow(item, isSub, tbody.lastElementChild);
            });
        } else {
            renderRow();
        }
    });
</script>
