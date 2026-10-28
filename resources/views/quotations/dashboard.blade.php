@extends('layouts.quotation')

@section('quotation_content')
    {{-- Page Header & Actions --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold  mb-1"><i class="bi bi-speedometer2 me-1"></i> Quotation Dashboard</h3>
            <p class="text-muted small mb-0">Overview of your business pipelines, quotations, and active statuses.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('quotations.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-list-ul me-1"></i> All Quotations
            </a>
            <a href="{{ route('quotations.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i> New Quotation
            </a>
        </div>
    </div>

    {{-- Metrics Cards Row --}}
    <div class="row g-3 mb-4">
        {{-- Total Quotations --}}
        <div class="col-md-3 col-sm-6">
            <div class="panel p-3 h-100 d-flex flex-column justify-content-between">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-uppercase small text-muted fw-semibold" style="letter-spacing: .08em;">Total
                        Quotes</span>
                    <div class="p-2 rounded-3 bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-file-earmark-text fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-0" style="color: var(--ink);">{{ number_format($totalQuotations) }}</h3>
            </div>
        </div>

        {{-- Total Pipeline Value --}}
        <div class="col-md-3 col-sm-6">
            <div class="panel p-3 h-100 d-flex flex-column justify-content-between">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-uppercase small text-muted fw-semibold" style="letter-spacing: .08em;">Total
                        Value</span>
                    <div class="p-2 rounded-3 bg-success bg-opacity-10 text-success">
                        {{ config('company.currency_symbol') }}
                    </div>
                </div>
                <h3 class="fw-bold mb-0 text-success">@money($totalValue)</h3>
            </div>
        </div>

        {{-- Approved Quotes --}}
        <div class="col-md-3 col-sm-6">
            <div class="panel p-3 h-100 d-flex flex-column justify-content-between">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-uppercase small text-muted fw-semibold" style="letter-spacing: .08em;">Approved</span>
                    <div class="p-2 rounded-3 bg-success bg-opacity-10 text-success">
                        <i class="bi bi-check-circle fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-0" style="color: var(--ink);">{{ number_format($approvedCount) }}</h3>
            </div>
        </div>

        {{-- Pending / In-Review --}}
        <div class="col-md-3 col-sm-6">
            <div class="panel p-3 h-100 d-flex flex-column justify-content-between">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-uppercase small text-muted fw-semibold" style="letter-spacing: .08em;">Pending /
                        Action</span>
                    <div class="p-2 rounded-3 bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-clock-history fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-0" style="color: var(--ink);">{{ number_format($pendingCount) }}</h3>
            </div>
        </div>
    </div>

    {{-- Recent Quotations Panel --}}
    <div class="panel mb-4">
        <div class="p-4 border-bottom d-flex justify-content-between align-items-center"
            style="border-color: var(--line) !important;">
            <h6 class="fw-semibold mb-0" style="color: var(--ink);">Recent Quotations</h6>
            <a href="{{ route('quotations.index') }}" class="small text-decoration-none fw-semibold">View All &rarr;</a>
        </div>
        <div class="table-responsive">
            <table class="table table-elegant align-middle mb-0">
                <thead>
                    <tr>
                        <th>Quote No.</th>
                        <th>Client</th>
                        <th>Project / Reference</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th class="text-end">Total</th>
                        <th class="text-center" style="width: 100px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentQuotations as $quotation)
                        <tr>
                            <td class="fw-semibold text-brand">{{ $quotation->quotation_number }}</td>
                            <td>
                                <div class="fw-medium" style="color: var(--ink);">{{ $quotation->quoted_to }}</div>
                                @if ($quotation->client_company)
                                    <div class="text-muted small">{{ $quotation->client_company }}</div>
                                @endif
                            </td>
                            <td class="text-muted">{{ $quotation->reference_project ?: '—' }}</td>
                            <td class="text-muted small">{{ $quotation->quotation_date->format('M d, Y') }}</td>
                            <td>
                                @php
                                    $badgeColors = match (strtolower($quotation->status)) {
                                        'approved' => 'bg-success text-white',
                                        'rejected' => 'bg-danger text-white',
                                        'pending', 'revision' => 'bg-warning text-dark',
                                        'emailed' => 'bg-info text-white',
                                        'ongoing' => 'bg-primary text-white',
                                        'completed' => 'bg-secondary text-white',
                                        default => 'bg-secondary text-white',
                                    };
                                @endphp
                                <span class="badge {{ $badgeColors }} px-2 py-1 text-uppercase"
                                    style="font-size: 0.7rem;">
                                    {{ $quotation->status }}
                                </span>
                            </td>
                            <td class="text-end fw-semibold">@money($quotation->total)</td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('quotations.show', $quotation->id) }}"
                                        class="btn btn-outline-secondary" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('quotations.edit', $quotation->id) }}"
                                        class="btn btn-outline-secondary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                No quotations found. Get started by creating your first quote!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
