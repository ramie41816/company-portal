@extends('layouts.portal')

@section('portal_content')
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-stretch align-items-lg-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1" style="color: var(--ink);">
                <i class="bi bi-speedometer2 me-1"></i> Portal Management Dashboard
            </h3>
            <p class="text-muted small mb-0">Overview of public services, active offerings, and customer inquiries.</p>
        </div>
        <div class="d-flex flex-column flex-sm-row align-items-center gap-2">
            <a href="{{ route('portal.services.index') }}"
                class="btn btn-outline-secondary btn-sm w-100 w-sm-auto text-nowrap">
                <i class="bi bi-tools me-1"></i> All Services
            </a>
            <a href="{{ route('portal.inquiries.index') }}" class="btn btn-primary btn-sm w-100 w-sm-auto text-nowrap">
                <i class="bi bi-envelope-paper me-1"></i> Inquiries
            </a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6 col-12">
            <div class="panel p-3 h-100 d-flex flex-column justify-content-between shadow-sm border-0"
                style="background: var(--card); border: 1px solid var(--line); border-radius: 1rem;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-uppercase small text-muted fw-semibold" style="letter-spacing: .08em;">Total
                        Services</span>
                    <div class="p-2 rounded-3 bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-grid fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-0" style="color: var(--ink);">{{ number_format($totalServices) }}</h3>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-12">
            <div class="panel p-3 h-100 d-flex flex-column justify-content-between shadow-sm border-0"
                style="background: var(--card); border: 1px solid var(--line); border-radius: 1rem;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-uppercase small text-muted fw-semibold" style="letter-spacing: .08em;">Active
                        Services</span>
                    <div class="p-2 rounded-3 bg-success bg-opacity-10 text-success">
                        <i class="bi bi-check-circle fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-0 text-success">{{ number_format($activeServices) }}</h3>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-12">
            <div class="panel p-3 h-100 d-flex flex-column justify-content-between shadow-sm border-0"
                style="background: var(--card); border: 1px solid var(--line); border-radius: 1rem;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-uppercase small text-muted fw-semibold" style="letter-spacing: .08em;">Pending
                        Inquiries</span>
                    <div class="p-2 rounded-3 bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-clock-history fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-0" style="color: var(--ink);">{{ number_format($pendingInquiries) }}</h3>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-12">
            <div class="panel p-3 h-100 d-flex flex-column justify-content-between shadow-sm border-0"
                style="background: var(--card); border: 1px solid var(--line); border-radius: 1rem;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-uppercase small text-muted fw-semibold" style="letter-spacing: .08em;">Total
                        Inquiries</span>
                    <div class="p-2 rounded-3 bg-info bg-opacity-10 text-info">
                        <i class="bi bi-envelope-check fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-0" style="color: var(--ink);">{{ number_format($totalInquiries) }}</h3>
            </div>
        </div>
    </div>

    <div class="panel mb-4 shadow-sm border-0"
        style="background: var(--card); border: 1px solid var(--line); border-radius: 1rem;">
        <div class="p-4 border-bottom d-flex justify-content-between align-items-center"
            style="border-color: var(--line) !important;">
            <h6 class="fw-semibold mb-0" style="color: var(--ink);">Recently Received Inquiries</h6>
            <a href="{{ route('portal.inquiries.index') }}" class="small text-decoration-none fw-semibold">View All
                &rarr;</a>
        </div>
        <div class="table-responsive">
            <table class="table table-elegant align-middle mb-0" style="color: var(--ink);">
                <thead>
                    <tr>
                        <th class="px-3">Customer Name</th>
                        <th>Phone</th>
                        <th class="d-none d-md-table-cell">Service Type</th>
                        <th>Status</th>
                        <th class="d-none d-md-table-cell">Date</th>
                        <th class="text-end px-3" style="width: 100px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentInquiries as $inquiry)
                        <tr>
                            <td class="fw-semibold px-3" style="color: var(--ink);">{{ $inquiry->name }}</td>
                            <td class="text-muted text-truncate" style="max-width: 150px;">{{ $inquiry->phone }}</td>
                            <td class="d-none d-md-table-cell">
                                <span class="badge bg-secondary text-white px-2 py-1" style="font-size: 0.7rem;">
                                    {{ $inquiry->service_type }}
                                </span>
                            </td>
                            <td>
                                @if ($inquiry->status === 'pending')
                                    <span class="badge px-2 py-1 shadow-sm text-uppercase"
                                        style="background-color: #ffc107; color: #000000; font-size: 0.7rem;">Pending</span>
                                @elseif ($inquiry->status === 'completed')
                                    <span class="badge px-2 py-1 shadow-sm text-uppercase"
                                        style="background-color: #198754; color: #ffffff; font-size: 0.7rem;">Completed</span>
                                @else
                                    <span class="badge px-2 py-1 shadow-sm text-uppercase"
                                        style="background-color: #0d6efd; color: #ffffff; font-size: 0.7rem;">{{ ucfirst($inquiry->status) }}</span>
                                @endif
                            </td>
                            <td class="text-muted small d-none d-md-table-cell">
                                {{ $inquiry->created_at->format('M d, Y') }}</td>
                            <td class="text-end px-3">
                                <a href="{{ route('portal.inquiries.show', $inquiry) }}"
                                    class="btn btn-outline-secondary btn-sm" title="View Details">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                No inquiries received yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
