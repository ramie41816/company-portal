@extends('layouts.quotation')

@section('quotation_content')
    <div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
        <a href="{{ route('quotations.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to list
        </a>
        <div class="d-flex align-items-center">
            @if (auth()->user()->hasPermission('quotations.update'))
                <a href="{{ route('quotations.edit', $quotation->id) }}" wire:navigate class="btn btn-warning btn-sm me-2">
                    <i class="bi bi-pencil-square me-1"></i> Edit Quotation
                </a>
                <div class="btn-group">
                    @if (auth()->user()->hasPermission('quotations.generate.pdf'))
                        <a href="{{ route('quotations.prepare', $quotation->id) }}" wire:navigate
                            class="btn btn-sm btn-primary">
                            <i class="bi bi-file-earmark-pdf me-1"></i> Prepare Quote
                        </a>
                    @endif
                    @if (auth()->user()->hasPermission('quotations.coc.generate.pdf') && $quotation->status === 'completed')
                        <a href="{{ route('quotations.coc', $quotation->id) }}" wire:navigate
                            class="btn btn-sm btn-success">
                            <i class="bi bi-file-earmark-check me-1"></i> Prepare CoC/CoT
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <div class="panel">
        <div class="p-4 p-md-5">

            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
                <div>
                    <h4 class="font-serif text-brand mb-0">{{ config('company.name') }}</h4>
                    <div class="text-muted small">{{ config('company.tagline') }}</div>
                    @if (config('company.address'))
                        <div class="text-muted small">{{ config('company.address') }}</div>
                    @endif
                    @if (config('company.phone'))
                        <div class="text-muted small">Phone: {{ config('company.phone') }}</div>
                    @endif
                </div>
                <div class="text-md-end">
                    <h2 class="mb-1">QUOTATION</h2>
                    <div class="fw-semibold text-brand">No: {{ $quotation->quotation_number }}</div>
                    <div class="text-muted small">Date: {{ $quotation->quotation_date->format('F d, Y') }}</div>
                    @if ($quotation->valid_until)
                        <div class="text-muted small">Valid Until: {{ $quotation->valid_until->format('F d, Y') }}</div>
                    @endif
                </div>
            </div>

            <div class="mb-4" style="height: 3px; background: var(--accent); border-radius: 2px;"></div>

            {{-- Status & Remarks Callout Box --}}
            <div class="card border mb-4 p-3 rounded-3"
                style="background-color: var(--surface); border-color: var(--line) !important;">
                <div class="row align-items-center g-3">
                    <div class="col-md-4">
                        <span class="text-uppercase small text-muted d-block mb-1"
                            style="letter-spacing: .08em;">Status</span>
                        @php
                            $statusBadge = match (strtolower($quotation->status)) {
                                'approved' => 'bg-success text-white',
                                'rejected' => 'bg-danger text-white',
                                'pending' => 'bg-warning text-dark',
                                'emailed' => 'bg-info text-white',
                                'ongoing' => 'bg-primary text-white',
                                'completed' => 'bg-success text-white',
                                default => 'bg-secondary text-white',
                            };
                        @endphp
                        <span class="badge {{ $statusBadge }} px-3 py-2 text-uppercase fs-6">
                            {{ ucfirst($quotation->status) }}
                        </span>
                    </div>
                    <div class="col-md-8">
                        <span class="text-uppercase small text-muted d-block mb-1" style="letter-spacing: .08em;">Remarks /
                            Audit Info</span>
                        <div class="fw-medium mb-1" style="color: var(--ink);">
                            {{ $quotation->remarks ?: 'No remarks provided.' }}
                        </div>
                        @if ($quotation->status_modified_by)
                            <div class="text-muted small fst-italic">
                                Last updated by <span class="fw-semibold"
                                    style="color: var(--ink);">{{ $quotation->status_modified_by }}</span>
                                @if ($quotation->status_modified_at)
                                    on {{ \Carbon\Carbon::parse($quotation->status_modified_at)->format('F d, Y') }}
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Customer & Project Details Grid --}}
            <div class="row g-4 mb-5">
                <div class="col-md-6">
                    <div class="text-uppercase small text-muted mb-1" style="letter-spacing: .08em;">Quote To</div>
                    <div class="fw-semibold fs-5">{{ $quotation->quoted_to }}</div>
                    @if ($quotation->client_company)
                        <div class="text-muted">{{ $quotation->client_company }}</div>
                    @endif
                    @if ($quotation->client_address)
                        <div class="text-muted small mt-1">{{ $quotation->client_address }}</div>
                    @endif
                    @if ($quotation->client_phone)
                        <div class="text-muted small"><i class="bi bi-telephone me-1"></i>{{ $quotation->client_phone }}
                        </div>
                    @endif
                    @if ($quotation->contact_email)
                        <div class="text-muted small"><i class="bi bi-envelope me-1"></i>{{ $quotation->contact_email }}
                        </div>
                    @endif
                </div>
                <div class="col-md-6 text-md-end">
                    @if ($quotation->reference_project)
                        <div class="mb-2">
                            <span class="text-uppercase small text-muted d-block" style="letter-spacing: .08em;">Reference /
                                Project</span>
                            <span class="fw-semibold">{{ $quotation->reference_project }}</span>
                        </div>
                    @endif
                    @if ($quotation->attention)
                        <div class="mb-2">
                            <span class="text-uppercase small text-muted d-block" style="letter-spacing: .08em;">Contact
                                Person (Attention)</span>
                            <span class="fw-medium">{{ $quotation->attention }}</span>
                        </div>
                    @endif
                    @if ($quotation->prepared_by)
                        <div>
                            <span class="text-uppercase small text-muted d-block" style="letter-spacing: .08em;">Prepared
                                By</span>
                            <span class="fw-medium">{{ $quotation->prepared_by }}</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Scope of work & Hierarchical Sub-items --}}
            <div class="text-uppercase small text-muted mb-2" style="letter-spacing: .08em;">Scope of Work & Services</div>

            <div class="table-responsive mb-4">
                <table class="table table-elegant align-middle">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Description</th>
                            <th class="text-end">Unit Price</th>
                            <th class="text-end">Quantity</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $mainItems = $quotation->items->whereNull('parent_id');
                            $x = 1;
                        @endphp
                        @foreach ($mainItems as $item)
                            @php
                                $subItems = $quotation->items->where('parent_id', $item->id);
                                $y = 1;
                            @endphp
                            <tr class="main-item-row">
                                <td>{{ $x }}</td>
                                <td>
                                    <div>{{ $item->description }}</div>
                                </td>
                                <td class="text-center">
                                    @if ($item->unit_price > 0 && $item->quantity > 0)
                                        {{ rtrim(rtrim(number_format($item->quantity, 2), '0'), '.') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if ($item->unit_price > 0)
                                        {!! 'PHP ' . number_format($item->unit_price, 2) !!}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-end" style="color: #0f3460;">
                                    @if ($item->total > 0)
                                        {!! 'PHP ' . number_format($item->total, 2) !!}
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>

                            {{-- Sub-items rows --}}
                            @foreach ($subItems as $subItem)
                                @php
                                    $numbering = $x . '.' . $y;
                                @endphp
                                <tr class="sub-item-row">
                                    <td></td>
                                    <td style="padding-left: 15px;">
                                        <span
                                            style="color: #166534; font-weight: bold; margin-right: 5px;">{{ $numbering }}</span>
                                        {{ $subItem->description }}
                                    </td>
                                    <td class="text-center">
                                        @if ($subItem->unit_price > 0 || $subItem->quantity > 0)
                                            {{ rtrim(rtrim(number_format($subItem->quantity, 2), '0'), '.') }}
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($subItem->unit_price > 0)
                                            {!! 'PHP ' . number_format($subItem->unit_price, 2) !!}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="text-end" style="font-weight: 600;">
                                        @if ($subItem->total > 0)
                                            {!! 'PHP ' . number_format($subItem->total, 2) !!}
                                        @else
                                            —
                                        @endif
                                    </td>
                                </tr>
                                @php $y += 1; @endphp
                            @endforeach
                            @php $x += 1; @endphp
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-end fw-semibold">Grand Total</td>
                            <td class="text-end fw-bold fs-5 text-brand">@money($quotation->total)</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- Other Information / Notes Section (Rich Text Output) --}}
            @if (!empty($quotation->other_info))
                <div class="mt-4 pt-3 border-top">
                    <div class="text-uppercase small text-muted mb-2" style="letter-spacing: .08em;">Other Information /
                        Terms & Notes</div>
                    <div class="p-3 bg-light rounded-3 text-secondary small" style="line-height: 1.6;">
                        {!! $quotation->other_info !!}
                    </div>
                </div>
            @endif

        </div>
    </div>
@endsection
