@php
    $isEdit = isset($quotation);
    $currency = config('company.currency_symbol');

    $savedItems = $isEdit
        ? $quotation->items
            ->map(
                fn($item) => [
                    'description' => $item->description,
                    'unit_price' => (float) $item->unit_price,
                    'quantity' => (float) $item->quantity,
                ],
            )
            ->all()
        : [['description' => '', 'unit_price' => '', 'quantity' => 1]];

    $oldItems = array_values(old('items', $savedItems));
@endphp

@if ($errors->any())
    <div class="alert alert-danger border-0 shadow-sm">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ $action }}" id="quotationForm">
    @csrf
    @if ($isEdit)
        @method('PUT')
        {{-- Status & Approval Remarks --}}
        <div class="panel mb-4">
            <div class="p-4">
                <h6 class="fw-semibold mb-3">Status & Remarks</h6>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="status" class="form-label">Status</label>
                        <select id="status" name="status" class="form-select">
                            <option value="draft"
                                {{ old('status', $quotation->status ?? 'draft') == 'draft' ? 'selected' : '' }}>Draft
                            </option>
                            <option value="pending"
                                {{ old('status', $quotation->status ?? '') == 'pending' ? 'selected' : '' }}>Pending
                            </option>
                            <option value="emailed"
                                {{ old('status', $quotation->status ?? '') == 'emailed' ? 'selected' : '' }}>Emailed
                            </option>
                            <option value="approved"
                                {{ old('status', $quotation->status ?? '') == 'approved' ? 'selected' : '' }}>Approved
                            </option>
                            <option value="ongoing"
                                {{ old('status', $quotation->status ?? '') == 'ongoing' ? 'selected' : '' }}>Ongoing
                            </option>
                            <option value="completed"
                                {{ old('status', $quotation->status ?? '') == 'completed' ? 'selected' : '' }}>Completed
                            </option>
                            <option value="rejected"
                                {{ old('status', $quotation->status ?? '') == 'rejected' ? 'selected' : '' }}>Rejected
                            </option>
                        </select>
                    </div>
                    <div class="col-md-8">
                        <label for="remarks" class="form-label">Remarks / Approval Notes <span
                                class="text-muted fw-normal">(optional)</span></label>
                        <input id="remarks" type="text" name="remarks"
                            value="{{ old('remarks', $quotation->remarks ?? '') }}" class="form-control"
                            placeholder="Add any remarks or internal notes regarding the status...">
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Customer & Project Details --}}
    <div class="panel mb-4">
        <div class="p-4">
            <h6 class="fw-semibold mb-3">Customer & Project Details</h6>
            <div class="row g-3">
                {{-- Quote To / Client Name --}}
                <div class="col-md-6">
                    <label for="quoted_to" class="form-label">Client Name (To)</label>
                    <input id="quoted_to" type="text" name="quoted_to"
                        value="{{ old('quoted_to', $quotation->quoted_to ?? '') }}" class="form-control" required>
                </div>

                {{-- Company / Business Name --}}
                <div class="col-md-6">
                    <label for="client_company" class="form-label">Company Name <span
                            class="text-muted fw-normal">(optional)</span></label>
                    <input id="client_company" type="text" name="client_company"
                        value="{{ old('client_company', $quotation->client_company ?? '') }}" class="form-control"
                        placeholder="Client's Company Name">
                </div>

                {{-- Client Address --}}
                <div class="col-md-12">
                    <label for="client_address" class="form-label">Address <span
                            class="text-muted fw-normal">(optional)</span></label>
                    <input id="client_address" type="text" name="client_address"
                        value="{{ old('client_address', $quotation->client_address ?? '') }}" class="form-control"
                        placeholder="Street, City, State, Zip">
                </div>

                {{-- Phone Number --}}
                <div class="col-md-6">
                    <label for="client_phone" class="form-label">Phone Number <span
                            class="text-muted fw-normal">(optional)</span></label>
                    <input id="client_phone" type="text" name="client_phone"
                        value="{{ old('client_phone', $quotation->client_phone ?? '') }}" class="form-control"
                        placeholder="(000) 000-0000">
                </div>

                {{-- Contact Email --}}
                <div class="col-md-6">
                    <label for="contact_email" class="form-label">Email Address <span
                            class="text-muted fw-normal">(optional)</span></label>
                    <input id="contact_email" type="email" name="contact_email"
                        value="{{ old('contact_email', $quotation->contact_email ?? '') }}" class="form-control"
                        placeholder="name@example.com">
                </div>

                {{-- Attention / Contact Person --}}
                <div class="col-md-6">
                    <label for="attention" class="form-label">Contact Person (Attention) <span
                            class="text-muted fw-normal">(optional)</span></label>
                    <input id="attention" type="text" name="attention"
                        value="{{ old('attention', $quotation->attention ?? '') }}" class="form-control"
                        placeholder="Person in charge">
                </div>

                {{-- Prepared By --}}
                <div class="col-md-6">
                    <label for="prepared_by" class="form-label">Prepared By <span
                            class="text-muted fw-normal">(optional)</span></label>
                    <input id="prepared_by" type="text" name="prepared_by"
                        value="{{ old('prepared_by', $quotation->prepared_by ?? '') }}" class="form-control"
                        placeholder="Your Name">
                </div>

                {{-- Quotation Date --}}
                <div class="col-md-4">
                    <label for="quotation_date" class="form-label">Issue Date</label>
                    <input id="quotation_date" type="date" name="quotation_date"
                        value="{{ old('quotation_date', $isEdit ? $quotation->quotation_date->toDateString() : now()->toDateString()) }}"
                        class="form-control" required>
                </div>

                {{-- Valid Until --}}
                <div class="col-md-4">
                    <label for="valid_until" class="form-label">Valid Until <span
                            class="text-muted fw-normal">(optional)</span></label>
                    <input id="valid_until" type="date" name="valid_until"
                        value="{{ old('valid_until', $isEdit && $quotation->valid_until ? $quotation->valid_until->toDateString() : '') }}"
                        class="form-control">
                </div>

                {{-- Reference / Project --}}
                <div class="col-md-4">
                    <label for="reference_project" class="form-label">Reference / Project <span
                            class="text-muted fw-normal">(optional)</span></label>
                    <input id="reference_project" type="text" name="reference_project"
                        value="{{ old('reference_project', $quotation->reference_project ?? '') }}"
                        class="form-control" placeholder="Project / Job Title">
                </div>
            </div>
        </div>
    </div>

    {{-- Scope of work --}}
    <div class="panel mb-4">
        <div class="p-4 pb-3 d-flex justify-content-between align-items-center">
            <h6 class="fw-semibold mb-0">Scope of Work</h6>
            <button type="button" id="add-row" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-plus-lg me-1"></i> Add item
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-elegant" id="items-table">
                <thead>
                    <tr>
                        <th style="min-width: 260px;">Service Description</th>
                        <th style="width: 170px;">Unit Price ({{ $currency }})</th>
                        <th style="width: 130px;">Quantity</th>
                        <th style="width: 160px;" class="text-end">Total</th>
                        <th style="width: 60px;"></th>
                    </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-end fw-semibold">Grand Total</td>
                        <td class="text-end fw-bold fs-5 text-brand" id="grand-total">{{ $currency }}0.00</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-brand">
            <i class="bi bi-check2-circle me-1"></i> {{ $isEdit ? 'Update Quotation' : 'Save Quotation' }}
        </button>
        <a href="{{ route('quotations.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>

<script>
    const oldItems = @json($oldItems);
    const currency = @json($currency);
    const tbody = document.querySelector('#items-table tbody');
    let index = 0;

    // Intercept form submission to show SweetAlert2 confirmation
    document.getElementById('quotationForm').addEventListener('submit', function(e) {
        e.preventDefault(); // Stop the form from submitting instantly

        const isEdit = @json($isEdit);
        const actionText = isEdit ? 'update this quotation' : 'save this new quotation';

        Swal.fire({
            title: 'Are you sure?',
            text: `Do you want to ${actionText}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#2b3a67', // Matches your brand button theme
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, proceed!',
            cancelButtonText: 'Cancel',
            customClass: {
                popup: 'rounded-4 shadow'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading state while it submits
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

                // Submit the form programmatically
                e.target.submit();
            }
        });
    });

    function formatMoney(amount) {
        return currency + amount.toLocaleString('en-PH', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });
    }

    function addRow(item = {}) {
        const i = index++;
        const tr = document.createElement('tr');
        tr.innerHTML =
            `
            <td class="p-2"><input type="text" name="items[${i}][description]" class="form-control" required></td>
            <td class="p-2"><input type="number" step="0.01" min="0" name="items[${i}][unit_price]" class="form-control price" required></td>
            <td class="p-2"><input type="number" step="0.01" min="0.01" name="items[${i}][quantity]" class="form-control qty" required></td>
            <td class="text-end fw-medium line-total">${formatMoney(0)}</td>
            <td class="p-2 text-end"><button type="button" class="btn btn-sm btn-outline-danger remove" aria-label="Remove item"><i class="bi bi-x-lg"></i></button></td>`;
        tr.querySelector('[name$="[description]"]').value = item.description ?? '';
        tr.querySelector('.price').value = item.unit_price ?? '';
        tr.querySelector('.qty').value = item.quantity ?? 1;
        tbody.appendChild(tr);
        recalc();
    }

    function recalc() {
        let grand = 0;
        tbody.querySelectorAll('tr').forEach(tr => {
            const total = (parseFloat(tr.querySelector('.price').value) || 0) *
                (parseFloat(tr.querySelector('.qty').value) || 0);
            tr.querySelector('.line-total').textContent = formatMoney(total);
            grand += total;
        });
        document.getElementById('grand-total').textContent = formatMoney(grand);
    }

    tbody.addEventListener('input', recalc);
    tbody.addEventListener('click', e => {
        if (e.target.closest('.remove') && tbody.rows.length > 1) {
            e.target.closest('tr').remove();
            recalc();
        }
    });
    document.getElementById('add-row').addEventListener('click', () => addRow());

    oldItems.forEach(addRow);
</script>
