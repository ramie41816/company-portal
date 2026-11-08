@extends('layouts.schedule')

@section('schedule_content')
    <div class="page-head mb-4">
        <h2 class="mb-1">Archived Schedules</h2>
        <div class="sub">View, restore, or permanently delete soft-deleted schedules.</div>
    </div>

    <div class="card border-0 shadow-sm mb-4" style="background: var(--card); border-radius: 0.75rem;">
        <div class="card-body">
            <form action="{{ route('schedules.archives') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-9">
                    <label class="form-label small fw-semibold text-muted">Search archive</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" value="{{ $search ?? '' }}" class="form-control border-start-0"
                            placeholder="Schedule #, client, company...">
                    </div>
                </div>
                <div class="col-md-3 d-grid">
                    <button type="submit" class="btn btn-primary" title="Filter">
                        <i class="bi bi-filter"></i> Filter
                    </button>
                </div>
            </form>
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
                        <th class="text-end">Total</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($schedules as $s)
                        <tr>
                            <td class="fw-semibold text-brand">{{ $s->schedule_number }}</td>
                            <td>{{ $s->schedule_date->format('M d, Y') }}</td>
                            <td class="fw-medium">{{ $s->quoted_to }}</td>
                            <td class="text-end fw-semibold">@money($s->total)</td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    @if (auth()->user()->hasPermission('schedules.update'))
                                        <button type="button" class="btn btn-sm btn-outline-success" title="Restore"
                                            data-bs-toggle="modal" data-bs-target="#restoreModal"
                                            data-action="{{ route('schedules.restore', $s->id) }}"
                                            data-name="{{ $s->schedule_number }} &ndash; {{ $s->quoted_to }}">
                                            <i class="bi bi-arrow-counterclockwise"></i> Restore
                                        </button>
                                    @endif
                                    @if (auth()->user()->hasPermission('schedules.delete'))
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                            title="Delete Permanently" data-bs-toggle="modal"
                                            data-bs-target="#forceDeleteModal"
                                            data-action="{{ route('schedules.force-delete', $s->id) }}"
                                            data-name="{{ $s->schedule_number }} &ndash; {{ $s->quoted_to }}">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="bi bi-archive fs-1 text-muted d-block mb-2"></i>
                                <div class="fw-medium">No archived schedules found</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $schedules->links() }}</div>

    {{-- Restore Modal --}}
    <div class="modal fade" id="restoreModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" id="restoreForm" class="modal-content shadow"
                style="background: var(--card); color: var(--ink); border: 1px solid var(--line); border-radius: 1rem;">
                @csrf
                @method('PATCH')
                <div class="modal-body text-center p-4">
                    <h5 class="fw-semibold mb-2">Restore this schedule?</h5>
                    <div class="fw-medium mb-2" id="restoreName"></div>
                    <div class="d-flex justify-content-center gap-2 mt-3">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success text-white">Yes, Restore</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Force Delete Modal --}}
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
                    <h5 class="fw-semibold mb-2">Permanently delete schedule?</h5>
                    <div class="fw-medium mb-2" id="forceDeleteName"></div>
                    <p class="text-muted small mb-4">This action cannot be undone.</p>
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash me-1"></i> Delete Permanently
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('restoreModal').addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            document.getElementById('restoreForm').action = button.dataset.action;
            document.getElementById('restoreName').textContent = button.dataset.name;
        });
        document.getElementById('forceDeleteModal').addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            document.getElementById('forceDeleteForm').action = button.dataset.action;
            document.getElementById('forceDeleteName').textContent = button.dataset.name;
        });
    </script>
@endsection
