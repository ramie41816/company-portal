@extends('layouts.schedule')

@section('schedule_content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1"><i class="bi bi-speedometer2 me-1"></i> Schedule Dashboard</h3>
            <p class="text-muted small mb-0">Overview of your operations, schedules, and active statuses.</p>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2">
            {{-- Dashboard Submenu / Quick Jump Navigation --}}
            <div class="btn-group btn-group-sm shadow-sm" role="group" aria-label="Dashboard Submenu">
                <a href="#calendar-section" class="btn btn-outline-secondary px-3">
                    <i class="bi bi-calendar3 me-1"></i> Calendar
                </a>
                <a href="#recent-schedules-section" class="btn btn-outline-secondary px-3">
                    <i class="bi bi-list-task me-1"></i> Recent List
                </a>
            </div>

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
            <div class="panel p-3 h-100 d-flex flex-column justify-content-between shadow-sm border-0">
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
            <div class="panel p-3 h-100 d-flex flex-column justify-content-between shadow-sm border-0">
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
            <div class="panel p-3 h-100 d-flex flex-column justify-content-between shadow-sm border-0">
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
            <div class="panel p-3 h-100 d-flex flex-column justify-content-between shadow-sm border-0">
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

    {{-- Interactive Calendar Section --}}
    <div id="calendar-section" class="row g-4 mb-4" style="scroll-behavior: smooth; scroll-margin-top: 20px;">
        <div class="col-lg-8">
            <div class="panel p-4 h-100 shadow-sm border-0">
                <h5 class="fw-semibold mb-3"><i class="bi bi-calendar3 me-1"></i> Operations Calendar</h5>
                <div id="scheduleCalendar"></div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="panel p-4 h-100 d-flex flex-column shadow-sm border-0">
                <h5 class="fw-semibold mb-1"><i class="bi bi-list-task me-1"></i> Schedules on <span`
                        class="text-brand fw-bold mb-3 fs-5" id="selectedDateLabel">Today</span>
                </h5>
                <div id="dateSchedulesList" class="overflow-auto flex-grow-1 pe-1 mt-3" style="max-height: 380px;">
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-calendar-event fs-2 d-block mb-2"></i>
                        Select a date from the calendar.
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Schedules Panel --}}
    <div id="recent-schedules-section" class="panel mb-4 shadow-sm border-0 scroll-mt">
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
                        <th>Date & Time</th>
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
                            <td class="text-muted small">
                                {{ $schedule->schedule_date ? $schedule->schedule_date->format('M d, Y h:i A') : '—' }}
                            </td>
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
                                No schedules found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const schedulesData = @json($calendarSchedules);
                const calendarEl = document.getElementById('scheduleCalendar');
                const dateLabel = document.getElementById('selectedDateLabel');
                const listContainer = document.getElementById('dateSchedulesList');

                function loadSchedulesForDate(dateStr) {
                    const options = {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    };
                    const formattedDateHeader = new Date(dateStr + 'T00:00:00').toLocaleDateString(undefined, options);
                    dateLabel.textContent = formattedDateHeader;

                    const filtered = schedulesData.filter(item => item.date === dateStr);

                    if (filtered.length === 0) {
                        listContainer.innerHTML = `
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-calendar-x fs-3 d-block mb-2 text-secondary"></i>
                            <span class="small">No schedules on this date.</span>
                        </div>`;
                        return;
                    }

                    let html = '<div class="list-group list-group-flush border-0 gap-2">';
                    filtered.forEach(item => {
                        const badgeColor = item.status === 'completed' ? 'bg-success text-white' :
                            item.status === 'ongoing' ? 'bg-primary text-white' :
                            item.status === 'cancelled' ? 'bg-danger text-white' : 'bg-secondary text-white';

                        html += `
                        <div class="list-group-item p-3 shadow-sm border rounded-3 mb-2" style="background: var(--card); border-color: var(--line) !important;">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <span class="fw-bold text-brand" style="font-size: 0.95rem;">${item.quote_number}</span>
                                    <div class="fw-medium mt-1" style="font-size: 0.875rem; color: var(--ink);">${item.scheduled_to}</div>
                                </div>
                                <span class="badge ${badgeColor} text-uppercase px-2 py-1" style="font-size: 0.6rem; letter-spacing: .05em;">${item.status}</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between pt-2 border-top" style="border-color: var(--line) !important;">
                                <span class="text-muted small d-flex align-items-center">
                                    <i class="bi bi-clock text-primary me-1"></i> ${item.time || 'All Day'}
                                </span>
                                <a href="${item.url}" class="btn btn-sm btn-outline-primary px-3 py-1" style="font-size: 0.75rem;">
                                    <i class="bi bi-eye me-1"></i> View
                                </a>
                            </div>
                        </div>`;
                    });
                    html += '</div>';
                    listContainer.innerHTML = html;
                }

                const todayStr = new Date().toISOString().split('T')[0];
                loadSchedulesForDate(todayStr);

                const calendar = new window.FullCalendar.Calendar(calendarEl, {
                    plugins: [
                        window.FullCalendar.dayGridPlugin,
                        window.FullCalendar.timeGridPlugin,
                        window.FullCalendar.interactionPlugin
                    ],
                    initialView: 'dayGridMonth',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek'
                    },
                    events: schedulesData,
                    dateClick: function(info) {
                        loadSchedulesForDate(info.dateStr);
                    },
                    eventClick: function(info) {
                        info.jsEvent.preventDefault();
                        window.location.href = info.event.url;
                    }
                });
                calendar.render();
            });
        </script>
    @endpush
@endsection
