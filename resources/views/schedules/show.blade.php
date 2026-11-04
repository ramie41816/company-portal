@extends('layouts.schedule')

@section('schedule_content')
    <div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
        <a href="{{ route('schedules.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to list
        </a>
        <div class="d-flex align-items-center">
            @if (auth()->user()->hasPermission('schedules.update'))
                <a href="{{ route('schedules.edit', $schedule->id) }}" class="btn btn-warning btn-sm me-2">
                    <i class="bi bi-pencil-square me-1"></i> Edit Schedule
                </a>
            @endif
        </div>
    </div>

    <div class="panel">
        <div class="p-4 p-md-5">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
                <div>
                    <h4 class="font-serif text-brand mb-0">{{ config('company.name') }}</h4>
                    <div class="text-muted small">{{ config('company.tagline') }}</div>
                    @if (config('company.address'))
                        <div class="text-muted small">{{ config('company.address') }}</div>
                    @endif
                </div>
                <div class="text-md-end">
                    <h2 class="mb-1">SCHEDULE</h2>
                    <div class="fw-semibold text-brand">No: {{ $schedule->schedule_number }}</div>
                    <div class="text-muted small">Date: {{ $schedule->schedule_date->format('F d, Y') }}</div>
                    @if ($schedule->start_date)
                        <div class="text-muted small">Start: {{ $schedule->start_date->format('F d, Y') }}</div>
                    @endif
                    @if ($schedule->end_date)
                        <div class="text-muted small">End: {{ $schedule->end_date->format('F d, Y') }}</div>
                    @endif
                </div>
            </div>

            <div class="mb-4" style="height: 3px; background: var(--accent); border-radius: 2px;"></div>

            <div class="card border mb-4 p-3 rounded-3"
                style="background-color: var(--surface); border-color: var(--line) !important;">
                <div class="row align-items-center g-3">
                    <div class="col-md-4">
                        <span class="text-uppercase small text-muted d-block mb-1"
                            style="letter-spacing: .08em;">Status</span>
                        <span class="badge bg-secondary px-3 py-2 text-uppercase fs-6">
                            {{ ucfirst($schedule->status) }}
                        </span>
                    </div>
                    <div class="col-md-8">
                        <span class="text-uppercase small text-muted d-block mb-1"
                            style="letter-spacing: .08em;">Remarks</span>
                        <div class="fw-medium mb-1" style="color: var(--ink);">
                            {{ $schedule->remarks ?: 'No remarks provided.' }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-5">
                <div class="col-md-6">
                    <div class="text-uppercase small text-muted mb-1" style="letter-spacing: .08em;">Schedule For</div>
                    <div class="fw-semibold fs-5">{{ $schedule->quoted_to }}</div>
                    @if ($schedule->client_company)
                        <div class="text-muted">{{ $schedule->client_company }}</div>
                    @endif
                    @if ($schedule->client_address)
                        <div class="text-muted small mt-1">{{ $schedule->client_address }}</div>
                    @endif
                    @if ($schedule->client_phone)
                        <div class="text-muted small"><i class="bi bi-telephone me-1"></i>{{ $schedule->client_phone }}
                        </div>
                    @endif
                </div>
                <div class="col-md-6 text-md-end">
                    @if ($schedule->reference_project)
                        <div class="mb-2">
                            <span class="text-uppercase small text-muted d-block" style="letter-spacing: .08em;">Reference /
                                Project</span>
                            <span class="fw-semibold">{{ $schedule->reference_project }}</span>
                        </div>
                    @endif
                    @if ($schedule->attention)
                        <div class="mb-2">
                            <span class="text-uppercase small text-muted d-block"
                                style="letter-spacing: .08em;">Attention</span>
                            <span class="fw-medium">{{ $schedule->attention }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="text-uppercase small text-muted mb-2" style="letter-spacing: .08em;">Scope of Work & Services</div>

            <div class="table-responsive mb-4">
                <table class="table table-elegant align-middle">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Description</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-end">Unit Price</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $mainItems = $schedule->items->whereNull('parent_id');
                            $x = 1;
                        @endphp
                        @foreach ($mainItems as $item)
                            @php
                                $subItems = $schedule->items->where('parent_id', $item->id);
                                $y = 1;
                            @endphp
                            <tr class="main-item-row">
                                <td>{{ $x }}</td>
                                <td>
                                    <div>{{ $item->description }}</div>
                                </td>
                                <td class="text-center">{{ rtrim(rtrim(number_format($item->quantity, 2), '0'), '.') }}
                                </td>
                                <td class="text-end">{!! 'PHP ' . number_format($item->unit_price, 2) !!}</td>
                                <td class="text-end fw-semibold text-brand">{!! 'PHP ' . number_format($item->total, 2) !!}</td>
                            </tr>
                            @foreach ($subItems as $subItem)
                                <tr class="sub-item-row">
                                    <td></td>
                                    <td style="padding-left: 15px;">
                                        <span
                                            style="color: #166534; font-weight: bold; margin-right: 5px;">{{ $x . '.' . $y }}</span>
                                        {{ $subItem->description }}
                                    </td>
                                    <td class="text-center">
                                        {{ rtrim(rtrim(number_format($subItem->quantity, 2), '0'), '.') }}</td>
                                    <td class="text-end">{!! 'PHP ' . number_format($subItem->unit_price, 2) !!}</td>
                                    <td class="text-end fw-semibold">{!! 'PHP ' . number_format($subItem->total, 2) !!}</td>
                                </tr>
                                @php $y += 1; @endphp
                            @endforeach
                            @php $x += 1; @endphp
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-end fw-semibold">Grand Total</td>
                            <td class="text-end fw-bold fs-5 text-brand">@money($schedule->total)</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            @if (!empty($schedule->other_info))
                <div class="mt-4 pt-3 border-top">
                    <div class="text-uppercase small text-muted mb-2" style="letter-spacing: .08em;">Other Information /
                        Notes</div>
                    <div class="p-3 bg-light rounded-3 text-secondary small" style="line-height: 1.6;">
                        {!! $schedule->other_info !!}
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
