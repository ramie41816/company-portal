@extends('layouts.portal')

@section('portal_content')
    <div class="page-head mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h2 class="mb-1" style="color: var(--ink);"><i class="bi bi-archive"></i> Archived Inquiries</h2>
            <div class="sub text-muted">View and manage soft-deleted client inquiries.</div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4" style="background: var(--card); border-radius: 0.75rem;">
        <div class="card-body">
            <form action="{{ route('portal.inquiries.archives') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-12 col-md-8">
                    <label class="form-label small fw-semibold text-muted">Search keyword</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" value="{{ $search ?? '' }}" class="form-control border-start-0"
                            placeholder="Name, email, phone...">
                    </div>
                </div>
                <div class="col-12 col-md-4 d-grid">
                    <button type="submit" class="btn btn-primary" title="Filter">
                        <i class="bi bi-filter"></i> Filter
                    </button>
                </div>
            </form>
            @if (request()->anyFilled(['search']))
                <div class="mt-2">
                    <a href="{{ route('portal.inquiries.archives') }}" class="small text-decoration-none text-danger">
                        <i class="bi bi-x-circle me-1"></i> Clear filters
                    </a>
                </div>
            @endif
        </div>
    </div>

    <div class="panel shadow-sm border-0 mb-4"
        style="background: var(--card); border: 1px solid var(--line); border-radius: 1rem;">
        <div class="table-responsive">
            <table class="table table-elegant align-middle mb-0" style="color: var(--ink);">
                <thead>
                    <tr>
                        <th class="px-3">Customer</th>
                        <th>Phone</th>
                        <th class="d-none d-md-table-cell">Service Type</th>
                        <th>Status</th>
                        <th class="d-none d-lg-table-cell">Archived Date</th>
                        <th class="text-end px-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($inquiries as $inquiry)
                        <tr>
                            <td class="fw-semibold px-3" style="color: var(--ink);">{{ $inquiry->name }}</td>
                            <td class="text-muted small">{{ $inquiry->phone }}</td>
                            <td class="d-none d-md-table-cell">
                                <span class="badge bg-secondary text-white px-2 py-1" style="font-size: 0.7rem;">
                                    {{ $inquiry->service_type }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-danger text-white px-2 py-1 text-uppercase"
                                    style="font-size: 0.7rem;">
                                    Archived
                                </span>
                            </td>
                            <td class="d-none d-lg-table-cell text-muted small">{{ $inquiry->deleted_at->format('M d, Y') }}
                            </td>
                            <td class="text-end px-3">
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-sm btn-outline-success" title="Restore Inquiry"
                                        data-bs-toggle="modal" data-bs-target="#restoreModal"
                                        data-action="{{ route('portal.inquiries.restore', $inquiry->id) }}"
                                        data-name="{{ $inquiry->name }}">
                                        <i class="bi bi-arrow-counterclockwise"></i> Restore
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger" title="Delete Permanently"
                                        data-bs-toggle="modal" data-bs-target="#forceDeleteModal"
                                        data-action="{{ route('portal.inquiries.force-delete', $inquiry->id) }}"
                                        data-name="{{ $inquiry->name }}">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="bi bi-archive fs-1 text-muted d-block mb-2"></i>
                                <div class="fw-medium">No archived inquiries found</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $inquiries->links() }}</div>

    <div class="modal fade" id="restoreModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" id="restoreForm" class="modal-content shadow"
                style="background: var(--card); color: var(--ink); border: 1px solid var(--line); border-radius: 1rem;">
                @csrf
                @method('PUT')
                <div class="modal-body text-center p-4">
                    <div class="mx-auto mb-3 bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center"
                        style="width: 48px; height: 48px;">
                        <i class="bi bi-arrow-counterclockwise fs-4"></i>
                    </div>
                    <h5 class="fw-semibold mb-2">Restore inquiry?</h5>
                    <div class="fw-medium mb-2 text-muted" id="restoreName"></div>
                    <p class="text-muted small mb-4">This inquiry will be restored to active list.</p>
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-outline-secondary px-4"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success px-4">Yes, Restore</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="forceDeleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" id="forceDeleteForm" class="modal-content shadow"
                style="background: var(--card); color: var(--ink); border: 1px solid var(--line); border-radius: 1rem;">
                @csrf
                @method('DELETE')
                <div class="modal-body text-center p-4">
                    <div class="mx-auto mb-3 bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center"
                        style="width: 48px; height: 48px;">
                        <i class="bi bi-trash fs-4"></i>
                    </div>
                    <h5 class="fw-semibold mb-2">Permanently delete inquiry?</h5>
                    <div class="fw-medium mb-2 text-muted" id="forceDeleteName"></div>
                    <p class="text-muted small mb-4">This action is irreversible and will completely remove the inquiry
                        record.</p>
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-outline-secondary px-4"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger px-4">
                            <i class="bi bi-trash me-1"></i> Delete Permanently
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        const restoreModal = document.getElementById('restoreModal');
        if (restoreModal) {
            restoreModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                document.getElementById('restoreForm').action = button.getAttribute('data-action');
                document.getElementById('restoreName').textContent = button.getAttribute('data-name');
            });
        }

        const forceDeleteModal = document.getElementById('forceDeleteModal');
        if (forceDeleteModal) {
            forceDeleteModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                document.getElementById('forceDeleteForm').action = button.getAttribute('data-action');
                document.getElementById('forceDeleteName').textContent = button.getAttribute('data-name');
            });
        }
    </script>
@endsection
