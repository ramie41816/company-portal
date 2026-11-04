@extends('layouts.schedule')

@section('schedule_content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1"><i class="bi bi-speedometer2 me-1"></i> Schedule Dashboard</h3>
            <p class="text-muted small mb-0">Overview of your operations, schedules, and active statuses.</p>
        </div>
        <div class="d-flex gap-2">
            @if (auth()->user()->hasPermission('schedules.view'))
                <a href="{{ route('schedules.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-list-ul me-1"></i> All Schedules
                </a>
            @endif
            @if (auth()->user()->hasPermission('schedules.add'))
                <a href="{{ route('schedules.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg me-1"></i> New Schedule
                </a>
            @endif
        </div>
    </div>

    {{-- Metrics Cards Row --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="panel p-3 h-100 d-flex flex-column justify-content-between">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-uppercase small text-muted fw-semibold" style="letter-spacing: .08em;">Total
                        Schedules</span>
                    <div class="p-2 rounded-3 bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-calendar-check fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-0" style="color: var(--ink);">{{ number_format($totalSchedules) }}</h3>
            </div>
        </div>

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

        <div class="col-md-3 col-sm-6">
            <div class="panel p-3 h-100 d-flex flex-column justify-content-between">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-uppercase small text-muted fw-semibold" style="letter-spacing: .08em;">Ongoing</span>
                    <div class="p-2 rounded-3 bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-play-circle fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-0" style="color: var(--ink);">{{ number_format($ongoingCount) }}</h3>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="panel p-3 h-100 d-flex flex-column justify-content-between">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-uppercase small text-muted fw-semibold"
                        style="letter-spacing: .08em;">Completed</span>
                    <div class="p-2 rounded-3 bg-success bg-opacity-10 text-success">
                        <i class="bi bi-check-circle fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-0" style="color: var(--ink);">{{ number_format($completedCount) }}</h3>
            </div>
        </div>
    </div>

    {{-- Recent Schedules Panel --}}
    <div class="panel mb-4">
        <div class="p-4 border-bottom d-flex justify-content-between align-items-center"
            style="border-color: var(--line) !important;">
            <h6 class="fw-semibold mb-0" style="color: var(--ink);">Recent Schedules</h6>
            <a href="{{ route('schedules.index') }}" class="small text-decoration-none fw-semibold">View All &rarr;</a>
        </div>
        <div class="table-responsive">
            <table class="table table-elegant align-middle mb-0">
                <thead>
                    <tr>
                        <th>Schedule No.</th>
                        <th>Client</th>
                        <th>Project / Reference</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th class="text-end">Total</th>
                        <th class="text-center" style="width: 100px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentSchedules as $schedule)
                        <tr>
                            <td class="fw-semibold text-brand">{{ $schedule->schedule_number }}</td>
                            <td>
                                <div class="fw-medium" style="color: var(--ink);">{{ $schedule->quoted_to }}</div>
                                @if ($schedule->client_company)
                                    <div class="text-muted small">{{ $schedule->client_company }}</div>
                                @endif
                            </td>
                            <td class="text-muted">{{ $schedule->reference_project ?: '—' }}</td>
                            <td class="text-muted small">{{ $schedule->schedule_date->format('M d, Y') }}</td>
                            <td>
                                @php
                                    $badgeColors = match (strtolower($schedule->status)) {
                                        'completed' => 'bg-success text-white',
                                        'cancelled' => 'bg-danger text-white',
                                        'ongoing' => 'bg-primary text-white',
                                        default => 'bg-secondary text-white',
                                    };
                                @endphp
                                <span class="badge {{ $badgeColors }} px-2 py-1 text-uppercase"
                                    style="font-size: 0.7rem;">
                                    {{ $schedule->status }}
                                </span>
                            </td>
                            <td class="text-end fw-semibold">@money($schedule->total)</td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    @if (auth()->user()->hasPermission('schedules.view'))
                                        <a href="{{ route('schedules.show', $schedule->id) }}"
                                            class="btn btn-outline-secondary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    @endif
                                    @if (auth()->user()->hasPermission('schedules.update'))
                                        <a href="{{ route('schedules.edit', $schedule->id) }}"
                                            class="btn btn-outline-secondary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                No schedules found. Get started by creating your first schedule!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
