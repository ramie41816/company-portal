@extends('layouts.quotation')

@section('quotation_content')
    <div class="page-head mb-4">
        <h2 class="mb-1">Archived Quotations</h2>
        <div class="sub">View, restore, or permanently delete soft-deleted quotations.</div>
    </div>

    {{-- Advanced Search & Filter Bar --}}
    <div class="card border-0 shadow-sm mb-4" style="background: var(--card); border-radius: 0.75rem;">
        <div class="card-body">
            <form action="{{ route('quotations.archives') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label small fw-semibold text-muted">Search archive</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" value="{{ $search ?? '' }}" class="form-control border-start-0"
                            placeholder="Quote #, client, company, project...">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-muted">From Date</label>
                    <input type="date" name="date_from" value="{{ $dateFrom ?? '' }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-muted">To Date</label>
                    <input type="date" name="date_to" value="{{ $dateTo ?? '' }}" class="form-control">
                </div>
                <div class="col-md-1 d-grid">
                    <button type="submit" class="btn btn-primary" title="Filter">
                        <i class="bi bi-filter"></i> Filter
                    </button>
                </div>
            </form>
            @if (request()->anyFilled(['search', 'date_from', 'date_to']))
                <div class="mt-2">
                    <a href="{{ route('quotations.archives') }}" class="small text-decoration-none text-danger">
                        <i class="bi bi-x-circle me-1"></i> Clear filters
                    </a>
                </div>
            @endif
        </div>
    </div>

    <div class="panel">
        <div class="table-responsive">
            <table class="table table-elegant align-middle">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Date</th>
                        <th>To</th>
                        <th>Attention</th>
                        <th class="text-end">Total</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($quotations as $q)
                        <tr>
                            <td class="fw-semibold text-brand">{{ $q->quotation_number }}</td>
                            <td>{{ $q->quotation_date->format('M d, Y') }}</td>
                            <td class="fw-medium">{{ $q->quoted_to }}</td>
                            <td class="text-muted">{{ $q->attention }}</td>
                            <td class="text-end fw-semibold">@money($q->total)</td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-sm btn-outline-success" title="Restore Quotation"
                                        data-bs-toggle="modal" data-bs-target="#restoreModal"
                                        data-action="{{ route('quotations.restore', $q->id) }}"
                                        data-name="{{ $q->quotation_number }} &ndash; {{ $q->quoted_to }}">
                                        <i class="bi bi-arrow-counterclockwise"></i> Restore
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger" title="Delete Permanently"
                                        data-bs-toggle="modal" data-bs-target="#forceDeleteModal"
                                        data-action="{{ route('quotations.force-delete', $q->id) }}"
                                        data-name="{{ $q->quotation_number }} &ndash; {{ $q->quoted_to }}">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="bi bi-archive fs-1 text-muted d-block mb-2"></i>
                                <div class="fw-medium">No archived quotations found</div>
                                <div class="text-muted small">Try adjusting your keyword or date range.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $quotations->links() }}</div>

    {{-- Restore Confirmation Modal --}}
    <div class="modal fade" id="restoreModal" tabindex="-1" aria-labelledby="restoreModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" id="restoreForm" class="modal-content shadow"
                style="background: var(--card); color: var(--ink); border: 1px solid var(--line); border-radius: 1rem;">
                @csrf
                @method('PATCH')

                <div class="modal-body text-center p-4">
                    <div class="mx-auto mb-3"
                        style="width: 60px; height: 60px; border-radius: 50%; display: grid; place-items: center;
                                font-size: 1.6rem; background: rgba(25, 135, 84, .12); color: #198754;">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </div>
                    <h5 class="fw-semibold mb-2" id="restoreModalLabel">Restore this quotation?</h5>
                    <div class="fw-medium mb-2" id="restoreName"></div>
                    <p class="text-muted small mb-4">
                        This quotation will be moved back to the active quotations list.
                    </p>
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success text-white">
                            <i class="bi bi-check-lg me-1"></i> Yes, Restore
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Permanent Delete Confirmation Modal --}}
    <div class="modal fade" id="forceDeleteModal" tabindex="-1" aria-labelledby="forceDeleteModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" id="forceDeleteForm" class="modal-content shadow"
                style="background: var(--card); color: var(--ink); border: 1px solid var(--line); border-radius: 1rem;">
                @csrf
                @method('DELETE')

                <div class="modal-body text-center p-4">
                    <div class="mx-auto mb-3"
                        style="width: 60px; height: 60px; border-radius: 50%; display: grid; place-items: center;
                                font-size: 1.6rem; background: rgba(220, 53, 69, .12); color: #dc3545;">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                    <h5 class="fw-semibold mb-2" id="forceDeleteModalLabel">Permanently delete quotation?</h5>
                    <div class="fw-medium mb-2" id="forceDeleteName"></div>
                    <p class="text-muted small mb-4">
                        This action cannot be undone. The quotation record and its items will be permanently erased from the
                        database.
                    </p>
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash-fill me-1"></i> Delete Permanently
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Populate Restore Modal
        document.getElementById('restoreModal').addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            document.getElementById('restoreForm').action = button.dataset.action;
            document.getElementById('restoreName').textContent = button.dataset.name;
        });

        // Populate Permanent Delete Modal
        document.getElementById('forceDeleteModal').addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            document.getElementById('forceDeleteForm').action = button.dataset.action;
            document.getElementById('forceDeleteName').textContent = button.dataset.name;
        });
    </script>
@endsection
