@extends('layouts.portal')

@section('portal_content')
    <div class="page-head mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h2 class="mb-1" style="color: var(--ink);"><i class="bi bi-archive"></i> Archived Services</h2>
            <div class="sub">View and manage archived services.</div>
        </div>
    </div>

    <div class="panel shadow-sm border-0 mb-4"
        style="background: var(--card); border: 1px solid var(--line); border-radius: 1rem;">
        <div class="table-responsive">
            <table class="table table-elegant align-middle mb-0" style="color: var(--ink);">
                <thead>
                    <tr>
                        <th class="px-3">Title</th>
                        <th>Archived Date</th>
                        <th class="text-end px-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($services as $service)
                        <tr>
                            <td class="fw-semibold px-3" style="color: var(--ink);">{{ $service->title }}</td>
                            <td class="text-muted small">{{ $service->deleted_at->format('M d, Y g:i A') }}</td>
                            <td class="text-end px-3">
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-sm btn-outline-success" title="Restore Service"
                                        data-bs-toggle="modal" data-bs-target="#restoreModal"
                                        data-action="{{ route('portal.services.restore', $service->id) }}"
                                        data-name="{{ $service->title }}">
                                        <i class="bi bi-arrow-counterclockwise"></i> Restore
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger" title="Delete Permanently"
                                        data-bs-toggle="modal" data-bs-target="#forceDeleteModal"
                                        data-action="{{ route('portal.services.force-delete', $service->id) }}"
                                        data-name="{{ $service->title }}">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-5 text-muted">
                                <i class="bi bi-archive fs-1 d-block mb-2"></i>
                                <div class="fw-medium">No archived services found</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $services->links() }}</div>

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
                    <h5 class="fw-semibold mb-2">Restore service?</h5>
                    <div class="fw-medium mb-2 text-muted" id="restoreName"></div>
                    <p class="text-muted small mb-4">This service will be re-enabled and restored to public view.</p>
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
                    <h5 class="fw-semibold mb-2">Permanently delete service?</h5>
                    <div class="fw-medium mb-2 text-muted" id="forceDeleteName"></div>
                    <p class="text-muted small mb-4">This action is irreversible and will delete the service profile
                        completely.</p>
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-outline-secondary px-4"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger px-4"><i class="bi bi-trash me-1"></i> Delete
                            Permanently</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
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
    @endpush
@endsection
