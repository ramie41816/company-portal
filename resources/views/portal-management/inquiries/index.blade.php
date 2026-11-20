@extends('layouts.portal')

@section('portal_content')
    <div
        class="page-head mb-4 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
        <div>
            <h2 class="mb-1" style="color: var(--ink);"><i class="bi bi-envelope-paper"></i> Client Inquiries</h2>
            <div class="sub text-muted">View and manage customer service requests submitted online.</div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4" style="background: var(--card); border-radius: 0.75rem;">
        <div class="card-body">
            <form action="{{ route('portal.inquiries.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-12 col-md-4">
                    <label for="searchInput" class="form-label small fw-semibold text-muted">Search keyword</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-search"></i></span>
                        <input type="text" id="searchInput" name="search" value="{{ $search ?? '' }}"
                            class="form-control border-start-0" placeholder="Name, email, phone, address...">
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-md-3">
                    <label for="statusSelect" class="form-label small fw-semibold text-muted">Status</label>
                    <select id="statusSelect" name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="pending"
                            {{ ($status ?? request()->query('status')) === 'pending' ? 'selected' : '' }}>
                            Pending
                        </option>
                        <option value="facilitated"
                            {{ ($status ?? request()->query('status')) === 'facilitated' ? 'selected' : '' }}>
                            Facilitated
                        </option>
                        <option value="completed"
                            {{ ($status ?? request()->query('status')) === 'completed' ? 'selected' : '' }}>
                            Completed
                        </option>
                        <option value="cancelled"
                            {{ ($status ?? request()->query('status')) === 'cancelled' ? 'selected' : '' }}>
                            Cancelled
                        </option>
                    </select>
                </div>

                <div class="col-12 col-sm-6 col-md-3">
                    <label for="serviceTypeSelect" class="form-label small fw-semibold text-muted">Service Type</label>
                    <select id="serviceTypeSelect" name="service_type" class="form-select">
                        <option value="">All Services</option>
                        @foreach ($serviceTypes ?? [] as $type)
                            <option value="{{ $type }}"
                                {{ ($serviceType ?? request()->query('service_type')) === $type ? 'selected' : '' }}>
                                {{ $type }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-2 d-grid">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-filter"></i> Filter
                    </button>
                </div>
            </form>

            @if (request()->anyFilled(['search', 'status', 'service_type']))
                <div class="mt-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div class="d-flex flex-wrap gap-2 small">
                        @if (!empty($search))
                            <span class="badge bg-light text-dark border">Search: "{{ $search }}"</span>
                        @endif
                        @if (!empty($status))
                            <span class="badge bg-light text-dark border">Status: {{ ucfirst($status) }}</span>
                        @endif
                        @if (!empty($serviceType))
                            <span class="badge bg-light text-dark border">Service: {{ $serviceType }}</span>
                        @endif
                    </div>
                    <a href="{{ route('portal.inquiries.index') }}" class="small text-decoration-none text-danger">
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
                        <th class="d-none d-lg-table-cell">Date</th>
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
                                @if ($inquiry->status === 'pending')
                                    <span class="badge bg-warning text-dark px-2 py-1 text-uppercase"
                                        style="font-size: 0.7rem;">Pending</span>
                                @elseif ($inquiry->status === 'facilitated')
                                    <span class="badge bg-success text-white px-2 py-1 text-uppercase"
                                        style="font-size: 0.7rem;">Facilitated</span>
                                @else
                                    <span class="badge bg-secondary text-white px-2 py-1 text-uppercase"
                                        style="font-size: 0.7rem;">{{ ucfirst($inquiry->status) }}</span>
                                @endif
                            </td>
                            <td class="d-none d-lg-table-cell text-muted small">
                                {{ $inquiry->created_at->format('M d, Y') }}
                            </td>
                            <td class="text-end px-3">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('portal.inquiries.show', $inquiry) }}"
                                        class="btn btn-outline-secondary" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if (auth()->user()->hasPermission('portal.update'))
                                        <button type="button" class="btn btn-outline-danger" title="Archive Inquiry"
                                            data-bs-toggle="modal" data-bs-target="#archiveModal"
                                            data-action="{{ route('portal.inquiries.destroy', $inquiry) }}"
                                            data-name="{{ $inquiry->name }}">
                                            <i class="bi bi-archive"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
                                <div class="fw-medium">No inquiries found</div>
                                @if (request()->anyFilled(['search', 'status', 'service_type']))
                                    <a href="{{ route('portal.inquiries.index') }}"
                                        class="btn btn-sm btn-outline-secondary mt-2">Reset Filters</a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $inquiries->links() }}</div>

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
                    <h5 class="fw-semibold mb-2">Archive this inquiry?</h5>
                    <div class="fw-medium mb-2 text-muted" id="deleteName"></div>
                    <p class="text-muted small mb-4">This inquiry will be moved to archives.</p>
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-outline-secondary px-4"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger px-4">Yes, Archive</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
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
    @endpush
@endsection
