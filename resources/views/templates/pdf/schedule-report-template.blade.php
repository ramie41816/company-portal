@php
    if (isset($template_viewing) && $template_viewing):
        $schedule =
            $schedule ??
            (object) [
                'schedule_number' => 'SCH-8AECB25HB5231',
                'scheduled_to' => 'Valued Client Name',
                'client_company' => 'Sample Client Corporation',
                'client_address' => '123 Business Rd, Metro Manila',
                'client_phone' => '+63 912 345 6789',
                'contact_email' => 'client@example.com',
                'schedule_date' => now(),
                'reference_project' => 'Sample Enterprise System Implementation',
                'status' => 'completed',
                'remarks' => 'All milestones successfully delivered and accepted.',
                'total' => 150000.0,
                'items' => collect([
                    (object) [
                        'id' => 1,
                        'parent_id' => null,
                        'description' => 'System Analysis & Requirements Gathering',
                        'quantity' => 1,
                        'unit_price' => 50000.0,
                        'total' => 50000.0,
                    ],
                    (object) [
                        'id' => 2,
                        'parent_id' => null,
                        'description' => 'Core Module Development & Integration',
                        'quantity' => 1,
                        'unit_price' => 100000.0,
                        'total' => 100000.0,
                    ],
                ]),
                'personnel' => collect([
                    (object) [
                        'name' => 'Juan Dela Cruz',
                        'role_in_schedule' => 'Lead Technician',
                        'notes' => 'Full day deployment',
                        'is_paid' => true,
                        'payment' => 3500.0,
                    ],
                    (object) [
                        'name' => 'Pedro Santos',
                        'role_in_schedule' => 'Assistant Tech',
                        'notes' => 'Half day support',
                        'is_paid' => false,
                        'payment' => 1500.0,
                    ],
                ]),
                'expenses' => collect([
                    (object) [
                        'expense_type' => 'Fuel',
                        'notes' => 'Service van gas refill',
                        'amount' => 1200.0,
                    ],
                    (object) [
                        'expense_type' => 'Tolls',
                        'notes' => 'SLEX / NLEX RFID pass',
                        'amount' => 450.0,
                    ],
                ]),
                'attention' => 'Jane Doe',
            ];

        $executedBy = $executedBy ?? 'System Administrator';
        $signatureDataUri = $signatureDataUri ?? null;
    endif;

    $hasQuotation = isset($schedule->quotation_id) && !empty($schedule->quotation_id);
    $quotationNumber = $hasQuotation ? optional($schedule->quotation)->quotation_number : null;

    $totalRevenue = $schedule->total ?? 0;
    $totalPaidPersonnel = collect($schedule->personnel ?? [])
        ->where('is_paid', true)
        ->sum('payment');
    $totalUnpaidPersonnel = collect($schedule->personnel ?? [])
        ->where('is_paid', false)
        ->sum('payment');
    $totalPersonnelOverall = collect($schedule->personnel ?? [])->sum('payment');
    $totalExpenses = collect($schedule->expenses ?? [])->sum('amount');
    $netJobProfit = $totalRevenue - $totalPaidPersonnel - $totalExpenses;
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Schedule Execution Report - {{ $schedule->schedule_number ?? 'SCH-8AECB25HB5231' }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 32mm 15mm 28mm 15mm;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #2d3748;
            font-size: 10px;
            line-height: 1.45;
            margin: 0;
            padding: 0;
        }

        .header-banner-fixed {
            position: fixed;
            top: -32mm;
            left: -15mm;
            right: -15mm;
            width: calc(100% + 30mm);
            height: 100px;
            overflow: hidden;
            z-index: -1000;
        }

        .header-banner-fixed img {
            width: 100%;
            height: 100px;
            object-fit: cover;
            display: block;
        }

        .footer-banner-fixed {
            position: fixed;
            bottom: -28mm;
            left: -15mm;
            right: -15mm;
            width: calc(100% + 30mm);
            height: 100px;
            overflow: hidden;
            z-index: -1000;
        }

        .footer-banner-fixed img {
            width: 100%;
            height: 100px;
            object-fit: cover;
            display: block;
        }

        .w-full {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            vertical-align: top;
        }

        .schedule-main-title {
            font-size: 18px;
            font-weight: 800;
            color: #0f3460;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin: 0 0 2px 0;
        }

        .company-tagline {
            font-size: 9.5px;
            color: #166534;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 4px;
        }

        .company-meta-box {
            font-size: 9.5px;
            color: #4a5568;
            line-height: 1.35;
        }

        .section-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 10px 12px;
            margin-bottom: 12px;
        }

        .section-title {
            font-size: 10px;
            font-weight: 700;
            color: #0f3460;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 6px;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 3px;
        }

        .info-grid {
            width: 100%;
            border-collapse: collapse;
        }

        .info-grid>tbody>tr>td {
            width: 50%;
            vertical-align: top;
        }

        .field-table {
            width: 100%;
            border-collapse: collapse;
        }

        .field-table td {
            padding-bottom: 2px;
            vertical-align: top;
            font-size: 9.5px;
        }

        .field-label {
            font-weight: 600;
            color: #4a5568;
            width: 90px;
            white-space: nowrap;
        }

        .field-label-right {
            font-weight: 600;
            color: #4a5568;
            width: 110px;
            white-space: nowrap;
        }

        .field-val {
            color: #1a202c;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        .items-table th {
            background-color: #0f3460;
            color: #ffffff;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 6px 8px;
            text-align: left;
            font-weight: bold;
        }

        .items-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 9.5px;
            color: #2d3748;
            vertical-align: top;
        }

        .items-table tfoot td {
            background-color: #f8fafc;
            font-weight: bold;
            color: #0f3460;
            border-top: 1px solid #cbd5e1;
            border-bottom: 1px solid #cbd5e1;
            padding: 6px 8px;
        }

        .financial-summary-box {
            background-color: #f1f5f9;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 10px 12px;
            margin-bottom: 12px;
        }

        .financial-grid {
            width: 100%;
            border-collapse: collapse;
        }

        .financial-grid td {
            width: 25%;
            vertical-align: top;
            padding: 0 4px;
        }

        .fin-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            padding: 8px;
            border-radius: 4px;
            text-align: center;
        }

        .badge-pill {
            font-weight: bold;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            letter-spacing: 0.03em;
            display: inline-block;
        }

        .badge-completed {
            color: #166534;
            background-color: #dcfce7;
            border: 1px solid #bbf7d0;
        }

        .badge-pending {
            color: #9a3412;
            background-color: #ffedd5;
            border: 1px solid #fed7aa;
        }

        .notes-box {
            font-size: 9.5px;
            line-height: 1.4;
            color: #2d3748;
            background: #fdfdfd;
            border: 1px solid #e2e8f0;
            border-left: 3px solid #0f3460;
            padding: 8px 10px;
            border-radius: 4px;
            margin-bottom: 12px;
        }

        .signatory-container {
            width: 100%;
            text-align: center;
            margin-top: 25px;
            page-break-inside: avoid;
        }

        .signatory-box {
            display: inline-block;
            width: 240px;
            text-align: center;
        }
    </style>
</head>

<body>
    {{-- Fixed Header Banner --}}
    <div class="header-banner-fixed">
        <img src="{{ public_path('images/banner/header.jpg') }}" alt="Header Banner">
    </div>

    {{-- Fixed Footer Banner --}}
    <div class="footer-banner-fixed">
        <img src="{{ public_path('images/banner/footer.jpg') }}" alt="Footer Banner">
    </div>

    {{-- Header Section --}}
    <table class="w-full header-table" style="margin-bottom: 12px;">
        <tr>
            <td>
                <div class="schedule-main-title">Schedule Execution & Audit Report</div>
                @if (config('company.tagline'))
                    <div class="company-tagline">{{ config('company.tagline') }}</div>
                @endif
                <div class="company-meta-box">
                    <div><strong>{{ strtoupper(config('company.name')) }}</strong></div>
                    <div>{{ config('company.address') }}</div>
                    <div>Phone: {{ config('company.phone') }} | Email: {{ config('company.email') }}</div>
                </div>
            </td>
            <td style="text-align: right;">
                @if (config('company.logo'))
                    <img src="{{ public_path(config('company.logo')) }}" alt="Logo" height="60"
                        style="display: inline-block;">
                @endif
            </td>
        </tr>
    </table>

    {{-- Client & Schedule Overview Section --}}
    <div class="section-box">
        <table class="info-grid">
            <tr>
                <td style="padding-right: 8px;">
                    <div class="section-title">Client Information</div>
                    <table class="field-table">
                        <tr>
                            <td class="field-label">Client Name:</td>
                            <td class="field-val">{{ $schedule->scheduled_to ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="field-label">Company Name:</td>
                            <td class="field-val">{{ $schedule->client_company ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="field-label">Address:</td>
                            <td class="field-val">{{ $schedule->client_address ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="field-label">Phone:</td>
                            <td class="field-val">{{ $schedule->client_phone ?? '—' }}</td>
                        </tr>
                    </table>
                </td>
                <td style="padding-left: 8px; border-left: 1px solid #cbd5e1;">
                    <div class="section-title">Execution Overview</div>
                    <table class="field-table">
                        <tr>
                            <td class="field-label-right">Schedule Number:</td>
                            <td class="field-val" style="font-weight: bold; color: #0f3460;">
                                {{ $schedule->schedule_number ?? 'SCH-8AECB25HB5231' }}</td>
                        </tr>
                        <tr>
                            <td class="field-label-right">Schedule Date:</td>
                            <td class="field-val">
                                {{ isset($schedule->schedule_date) ? \Carbon\Carbon::parse($schedule->schedule_date)->format('F d, Y h:i A') : '—' }}
                            </td>
                        </tr>
                        <tr>
                            <td class="field-label-right">Project Reference:</td>
                            <td class="field-val">{{ $schedule->reference_project ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="field-label-right">Operational Status:</td>
                            <td class="field-val">
                                <span
                                    class="badge-pill {{ strtolower($schedule->status ?? '') === 'completed' ? 'badge-completed' : 'badge-pending' }}">
                                    {{ strtoupper($schedule->status ?? 'SCHEDULED') }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    {{-- Financial Audit Summary Cards --}}
    <div class="financial-summary-box">
        <div class="section-title" style="margin-bottom: 8px;">Job Financial & Cost Audit Summary</div>
        <table class="financial-grid">
            <tr>
                <td>
                    <div class="fin-card">
                        <div style="font-size: 8.5px; color: #64748b; text-transform: uppercase; font-weight: bold;">
                            Total Revenue</div>
                        <div style="font-size: 11px; font-weight: bold; color: #0f3460; margin-top: 2px;">PHP
                            {{ number_format($totalRevenue, 2) }}</div>
                    </div>
                </td>
                <td>
                    <div class="fin-card">
                        <div style="font-size: 8.5px; color: #64748b; text-transform: uppercase; font-weight: bold;">
                            Staff Payouts (Paid)</div>
                        <div style="font-size: 11px; font-weight: bold; color: #b91c1c; margin-top: 2px;">PHP
                            {{ number_format($totalPaidPersonnel, 2) }}</div>
                    </div>
                </td>
                <td>
                    <div class="fin-card">
                        <div style="font-size: 8.5px; color: #64748b; text-transform: uppercase; font-weight: bold;">
                            Operational Expenses</div>
                        <div style="font-size: 11px; font-weight: bold; color: #b91c1c; margin-top: 2px;">PHP
                            {{ number_format($totalExpenses, 2) }}</div>
                    </div>
                </td>
                <td>
                    <div class="fin-card" style="background: {{ $netJobProfit >= 0 ? '#f0fdf4' : '#fef2f2' }};">
                        <div style="font-size: 8.5px; color: #64748b; text-transform: uppercase; font-weight: bold;">Net
                            Job Profit</div>
                        <div
                            style="font-size: 11px; font-weight: bold; color: {{ $netJobProfit >= 0 ? '#166534' : '#b91c1c' }}; margin-top: 2px;">
                            PHP {{ number_format($netJobProfit, 2) }}</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- Scope of Work Executed --}}
    <div style="font-size: 10px; font-weight: 700; color: #0f3460; text-transform: uppercase; margin-bottom: 4px;">Scope
        of Work & Services Rendered</div>
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 30px;">#</th>
                <th>Description</th>
                <th class="text-center" style="width: 45px;">Qty</th>
                <th style="width: 85px; text-align: right;">Unit Price</th>
                <th style="width: 95px; text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @php
                $itemsCollection = collect($schedule->items ?? []);
                $mainItems = $itemsCollection->whereNull('parent_id');
                $x = 1;
            @endphp
            @foreach ($mainItems as $item)
                @php
                    $subItems = $itemsCollection->where('parent_id', $item->id);
                    $y = 1;
                @endphp
                <tr>
                    <td style="font-weight: bold;">{{ $x }}</td>
                    <td style="font-weight: bold;">{{ $item->description ?? 'Item description' }}</td>
                    <td class="text-center">{{ rtrim(rtrim(number_format($item->quantity ?? 1, 2), '0'), '.') }}</td>
                    <td style="text-align: right;">PHP {{ number_format($item->unit_price ?? 0, 2) }}</td>
                    <td style="text-align: right; font-weight: bold;">PHP {{ number_format($item->total ?? 0, 2) }}
                    </td>
                </tr>
                @foreach ($subItems as $subItem)
                    @php $numbering = $x . '.' . $y; @endphp
                    <tr>
                        <td></td>
                        <td style="padding-left: 12px;">
                            <span
                                style="color: #166534; font-weight: bold; margin-right: 4px;">{{ $numbering }}</span>
                            <span style="color: #4a5568;">{{ $subItem->description ?? 'Sub-item description' }}</span>
                        </td>
                        <td class="text-center">{{ rtrim(rtrim(number_format($subItem->quantity ?? 1, 2), '0'), '.') }}
                        </td>
                        <td style="text-align: right;">PHP {{ number_format($subItem->unit_price ?? 0, 2) }}</td>
                        <td style="text-align: right;">PHP {{ number_format($subItem->total ?? 0, 2) }}</td>
                    </tr>
                    @php $y++; @endphp
                @endforeach
                @php $x++; @endphp
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" style="text-align: right;">Grand Total (Revenue):</td>
                <td style="text-align: right;">PHP {{ number_format($totalRevenue, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- Assigned Staff & Payouts --}}
    <div style="font-size: 10px; font-weight: 700; color: #0f3460; text-transform: uppercase; margin-bottom: 4px;">
        Assigned Staff & Compensation Audit</div>
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 30px;">#</th>
                <th>Staff Name</th>
                <th>Role / Assignment</th>
                <th>Notes</th>
                <th style="width: 75px; text-align: center;">Payment Status</th>
                <th style="width: 85px; text-align: right;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse (collect($schedule->personnel ?? []) as $index => $p)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="font-weight: bold;">{{ $p->name ?? ($p->user->name ?? 'Staff') }}</td>
                    <td>{{ $p->role_in_schedule ?? '—' }}</td>
                    <td>{{ $p->notes ?? '—' }}</td>
                    <td class="text-center">
                        @if (!empty($p->is_paid))
                            <span class="badge-pill badge-completed">PAID</span>
                        @else
                            <span class="badge-pill badge-pending">UNPAID</span>
                        @endif
                    </td>
                    <td style="text-align: right; font-weight: bold;">PHP {{ number_format($p->payment ?? 0, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #64748b;">No staff assigned to this schedule.
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" style="text-align: right;">Total Staff Payout (Overall):</td>
                <td style="text-align: right;">PHP {{ number_format($totalPersonnelOverall, 2) }}</td>
            </tr>
            <tr>
                <td colspan="5" style="text-align: right;">Total Staff Payout (Paid Only):</td>
                <td style="text-align: right;">PHP {{ number_format($totalPaidPersonnel, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- Operational Expenses --}}
    <div style="font-size: 10px; font-weight: 700; color: #0f3460; text-transform: uppercase; margin-bottom: 4px;">
        Operational Expenses (Fuel, Tolls, Supplies, Etc.)</div>
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 30px;">#</th>
                <th>Expense Type</th>
                <th>Reference / Notes</th>
                <th style="width: 95px; text-align: right;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse (collect($schedule->expenses ?? []) as $index => $exp)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="font-weight: bold;">{{ $exp->expense_type ?? 'Expense' }}</td>
                    <td>{{ $exp->notes ?? '—' }}</td>
                    <td style="text-align: right; font-weight: bold;">PHP {{ number_format($exp->amount ?? 0, 2) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center; color: #64748b;">No operational expenses recorded.
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" style="text-align: right;">Total Operational Expenses:</td>
                <td style="text-align: right;">PHP {{ number_format($totalExpenses, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- General Remarks / Notes --}}
    @if (!empty($schedule->remarks))
        <div class="notes-box">
            <strong>Internal Operational Remarks:</strong><br>
            {!! nl2br(e($schedule->remarks)) !!}
        </div>
    @endif

    {{-- Signatory Section (Centered, Clean Background) --}}
    <div class="signatory-container">
        <div class="signatory-box">
            <div
                style="font-size: 9.5px; font-weight: 700; color: #0f3460; text-transform: uppercase; margin-bottom: 6px;">
                Prepared / Audited By
            </div>
            <div style="width: 180px; height: 40px; text-align: center; margin: 2px auto 0 auto;">
                @if (!empty($signatureDataUri))
                    <img src="{{ $signatureDataUri }}" alt="eSignature"
                        style="max-height: 38px; max-width: 180px; width: auto; display: inline-block;">
                @endif
            </div>
            <div style="border-bottom: 1px solid #4a5568; width: 100%; margin-bottom: 4px; margin-top: 2px;"></div>
            <div style="font-weight: bold; color: #1a202c; font-size: 9.5px;">
                {{ $executedBy ?? config('company.name') }}</div>
            <div style="color: #4a5568; font-size: 9px;">Operations Management</div>
        </div>
    </div>

    {{-- Dompdf Page Numbering Script --}}
    <script type="text/php">
        if (isset($pdf)) {
            $font = $fontMetrics->getFont("Helvetica", "normal");
            $size = 9;
            $color = array(0, 0, 0, 0);
            
            $companyText = "{{ config('company.name') }}";
            $pdf->page_text(25, 795, $companyText, $font, $size, $color);
            
            $scheduleNumber = "{{ $schedule->schedule_number ?? 'SCH-8AECB25HB5231' }}";
            $pageText = "Page {PAGE_NUM} of {PAGE_COUNT} | " . $scheduleNumber;
            $pdf->page_text(420, 795, $pageText, $font, $size, $color);
        }
    </script>
</body>

</html>
