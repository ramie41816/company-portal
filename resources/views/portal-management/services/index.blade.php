@extends('layouts.portal')

@section('portal_content')
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-stretch align-items-lg-center gap-3 mb-4">
        <div>
            <h2 class="mb-1" style="color: var(--ink);"><i class="bi bi-tools"></i> Services Management</h2>
            <div class="sub text-muted">View and manage offered services displayed on the public landing page.</div>
        </div>
        <div class="d-flex flex-column flex-sm-row align-items-center gap-2">
            @if (auth()->user()->hasPermission('portal.add'))
                <a href="{{ route('portal.services.create') }}" class="btn btn-primary btn-sm w-100 w-sm-auto text-nowrap">
                    <i class="bi bi-plus-circle me-1"></i> New Service
                </a>
            @endif
        </div>
    </div>

    <div class="panel shadow-sm border-0 mb-4"
        style="background: var(--card); border: 1px solid var(--line); border-radius: 1rem;">
        <div class="table-responsive">
            <table class="table table-elegant align-middle mb-0" style="color: var(--ink);">
                <thead>
                    <tr>
                        <th class="px-3">Icon</th>
                        <th>Title</th>
                        <th>Short Description</th>
                        <th class="d-none d-md-table-cell">Photos</th>
                        <th class="text-end px-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($services as $service)
                        <tr>
                            <td class="px-3">
                                <div class="p-2 rounded-3 bg-primary bg-opacity-10 text-primary d-inline-flex align-items-center justify-content-center"
                                    style="width: 36px; height: 36px;">
                                    <i class="bi {{ $service->icon_class ?? 'bi-tools' }}"></i>
                                </div>
                            </td>
                            <td class="fw-semibold" style="color: var(--ink);">{{ $service->title }}</td>
                            <td class="text-muted small text-truncate" style="max-width: 250px;">
                                {{ $service->short_description }}</td>
                            <td class="d-none d-md-table-cell">
                                @php $imgCount = is_array($service->images) ? count($service->images) : 0; @endphp
                                <span class="badge bg-secondary text-white px-2 py-1" style="font-size: 0.7rem;">
                                    {{ $imgCount }} {{ $imgCount === 1 ? 'Photo' : 'Photos' }}
                                </span>
                            </td>
                            <td class="text-end px-3">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('portal.services.show', $service) }}"
                                        class="btn btn-outline-secondary" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if (auth()->user()->hasPermission('portal.update'))
                                        <a href="{{ route('portal.services.edit', $service) }}"
                                            class="btn btn-outline-primary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-danger" title="Archive Service"
                                            data-bs-toggle="modal" data-bs-target="#archiveModal"
                                            data-action="{{ route('portal.services.destroy', $service) }}"
                                            data-name="{{ $service->title }}">
                                            <i class="bi bi-archive"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-tools fs-1 text-muted d-block mb-2"></i>
                                <div class="fw-medium">No services found</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $services->links() }}</div>

    <div class="modal fade" id="archiveModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" id="archiveForm" class="modal-content shadow"
                style="background: var(--card); color: var(--ink); border: 1px solid var(--line); border-radius: 1rem;">
                @csrf
                @method('DELETE')
                <div class="modal-body text-center p-4">
                    <div class="mx-auto mb-3 bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center"
                        style="width: 48px; height: 48px;">
                        <i class="bi bi-archive fs-4"></i>
                    </div>
                    <h5 class="fw-semibold mb-2">Archive this service?</h5>
                    <div class="fw-medium mb-2 text-muted" id="deleteName"></div>
                    <p class="text-muted small mb-4">This service will be hidden from the public portal. You can restore it
                        anytime.</p>
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-outline-secondary px-4"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger px-4"><i class="bi bi-archive me-1"></i> Yes,
                            Archive</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        const archiveModal = document.getElementById('archiveModal');
        if (archiveModal) {
            archiveModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                document.getElementById('archiveForm').action = button.dataset.action;
                document.getElementById('deleteName').textContent = button.dataset.name;
            });
        }
    </script>
@endsection
