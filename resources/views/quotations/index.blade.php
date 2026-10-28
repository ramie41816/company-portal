@extends('layouts.quotation')

@section('quotation_content')
    <div class="page-head mb-4">
        <h2 class="mb-1">Quotations</h2>
        <div class="sub">View and manage every active quotation in one place.</div>
    </div>

    {{-- Advanced Search & Filter Bar --}}
    <div class="card border-0 shadow-sm mb-4" style="background: var(--card); border-radius: 0.75rem;">
        <div class="card-body">
            <form action="{{ route('quotations.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label small fw-semibold text-muted">Search keyword</label>
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
                    <a href="{{ route('quotations.index') }}" class="small text-decoration-none text-danger">
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
                        <th>Status</th>
                        <th class="text-end">Total</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($quotations as $q)
                        <tr>
                            <td class="fw-semibold text-brand">{{ $q->quotation_number }}</td>
                            <td>{{ $q->quotation_date->format('M d, Y') }}</td>
                            <td>
                                <div class="fw-medium">{{ $q->quoted_to }}</div>
                                @if ($q->client_company)
                                    <div class="text-muted small">{{ $q->client_company }}</div>
                                @endif
                            </td>
                            <td>
                                @php
                                    $badgeColors = [
                                        'draft' => 'bg-secondary text-white',
                                        'pending' => 'bg-warning text-dark',
                                        'approved' => 'bg-success text-white',
                                        'rejected' => 'bg-danger text-white',
                                        'emailed' => 'bg-info text-white',
                                        'ongoing' => 'bg-primary text-white',
                                        'completed' => 'bg-secondary text-white',
                                    ];
                                @endphp
                                <span
                                    class="badge {{ $badgeColors[$q->status] ?? 'bg-secondary text-white' }} text-uppercase px-2 py-1"
                                    style="font-size: 0.7rem;">
                                    {{ $q->status }}
                                </span>
                            </td>
                            <td class="text-end fw-semibold">@money($q->total)</td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('quotations.show', $q) }}" class="btn btn-sm btn-outline-secondary"
                                        title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('quotations.edit', $q) }}" class="btn btn-sm btn-outline-secondary"
                                        title="Update">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-warning" title="Delete"
                                        data-bs-toggle="modal" data-bs-target="#archiveModal"
                                        data-action="{{ route('quotations.destroy', $q) }}"
                                        data-name="{{ $q->quotation_number }} &ndash; {{ $q->quoted_to }}">
                                        <i class="bi bi-archive"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
                                <div class="fw-medium">No quotations found</div>
                                <div class="text-muted small">Try adjusting your search query or filters.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $quotations->links() }}</div>

    {{-- Archive confirmation modal --}}
    <div class="modal fade" id="archiveModal" tabindex="-1" aria-labelledby="archiveModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" id="archiveForm" class="modal-content shadow"
                style="background: var(--card); color: var(--ink); border: 1px solid var(--line); border-radius: 1rem;">
                @csrf
                @method('DELETE')

                <div class="modal-body text-center p-4">
                    {{-- Changed icon container to an archive/amber accent --}}
                    <div class="mx-auto mb-3"
                        style="width: 60px; height: 60px; border-radius: 50%; display: grid; place-items: center; font-size: 1.6rem; background: rgba(255, 193, 7, .15); color: #ffc107;">
                        <i class="bi bi-archive"></i>
                    </div>

                    <h5 class="fw-semibold mb-2" id="archiveModalLabel">Archive Quotation?</h5>
                    <div class="fw-medium mb-2" id="archiveName"></div>

                    <p class="text-muted small mb-4">
                        This quotation will be moved to the archive list and hidden from active records. You can restore it
                        later if needed. Are you sure?
                    </p>

                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        {{-- Changed from btn-danger to warning/amber to indicate archiving instead of permanent deletion --}}
                        <button type="submit" class="btn btn-warning text-dark fw-medium">
                            <i class="bi bi-archive me-1"></i> Yes, Archive
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('archiveModal').addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            document.getElementById('archiveForm').action = button.dataset.action;
            document.getElementById('archiveName').textContent = button.dataset.name;
        });
    </script>
@endsection
