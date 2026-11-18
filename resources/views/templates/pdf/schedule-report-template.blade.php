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
            margin: 14mm 14mm 16mm 14mm;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 8.5px;
            line-height: 1.45;
            margin: 0;
            padding: 0;
            background: #ffffff;
        }

        .w-full {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            vertical-align: top;
        }

        .schedule-main-title {
            font-size: 15px;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin: 0 0 3px 0;
        }

        .company-meta-box {
            font-size: 8.5px;
            color: #475569;
            line-height: 1.35;
        }

        .section-box {
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 8px 10px;
            margin-bottom: 12px;
        }

        .section-title {
            font-size: 8.5px;
            font-weight: 700;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 6px;
            padding-bottom: 3px;
            border-bottom: 1px solid #e2e8f0;
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
            padding: 2.5px 0;
            vertical-align: top;
            font-size: 8.5px;
        }

        .field-label {
            font-weight: 600;
            color: #64748b;
            width: 85px;
            white-space: nowrap;
        }

        .field-label-right {
            font-weight: 600;
            color: #64748b;
            width: 95px;
            white-space: nowrap;
        }

        .field-val {
            color: #0f172a;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        .items-table th {
            background-color: #ffffff;
            color: #475569;
            font-size: 7.5px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 5px 6px;
            text-align: left;
            font-weight: 700;
            border-bottom: 1.5px solid #cbd5e1;
        }

        .items-table td {
            padding: 5px 6px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 8.5px;
            color: #1e293b;
            vertical-align: middle;
        }

        .items-table tfoot td {
            background-color: #ffffff;
            font-weight: bold;
            color: #0f172a;
            border-top: 1.5px solid #cbd5e1;
            border-bottom: 1px solid #cbd5e1;
            padding: 5px 6px;
        }

        .financial-summary-box {
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 8px 10px;
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
            border: 1px solid #cbd5e1;
            border-radius: 3px;
            padding: 5px 6px;
            text-align: center;
        }

        .badge-pill {
            font-weight: 700;
            padding: 1px 4px;
            font-size: 7.5px;
            letter-spacing: 0.03em;
            display: inline-block;
            border: 1px solid #94a3b8;
            border-radius: 2px;
            color: #1e293b;
            background: #ffffff;
        }

        .notes-box {
            font-size: 8.5px;
            line-height: 1.4;
            color: #334155;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-left: 3px solid #64748b;
            padding: 6px 8px;
            border-radius: 2px;
            margin-bottom: 12px;
        }

        .signatory-container {
            width: 100%;
            text-align: center;
            margin-top: 16px;
            page-break-inside: avoid;
        }

        .signatory-box {
            display: inline-block;
            width: 200px;
            text-align: center;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>

<body>

    {{-- Header Section --}}
    <table class="w-full header-table"
        style="margin-bottom: 10px; border-bottom: 1px solid #0f172a; padding-bottom: 6px;">
        <tr>
            <td>
                <div class="schedule-main-title">Schedule Execution Report</div>
                <div class="company-meta-box">
                    <div><strong>{{ strtoupper(config('company.name')) }}</strong></div>
                    <div>{{ config('company.address') }} | Phone: {{ config('company.phone') }} | Email:
                        {{ config('company.email') }}</div>
                </div>
            </td>
            <td style="text-align: right; vertical-align: top;">
                <div style="font-weight: bold; font-size: 9.5px; color: #0f172a;">REF:
                    {{ $schedule->schedule_number ?? 'SCH-8AECB25HB5231' }}</div>
                <div style="font-size: 8px; color: #64748b;">DATE:
                    {{ isset($schedule->schedule_date) ? \Carbon\Carbon::parse($schedule->schedule_date)->format('Y-m-d H:i') : '—' }}
                </div>
            </td>
        </tr>
    </table>

    {{-- Client & Schedule Overview Section --}}
    <div class="section-box">
        <table class="info-grid">
            <tr>
                <td style="padding-right: 8px;">
                    <div class="section-title">Client Details</div>
                    <table class="field-table">
                        <tr>
                            <td class="field-label">Client Name:</td>
                            <td class="field-val">{{ $schedule->scheduled_to ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="field-label">Company:</td>
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
                <td style="padding-left: 8px; border-left: 1px solid #f1f5f9;">
                    <div class="section-title">Schedule Details</div>
                    <table class="field-table">
                        <tr>
                            <td class="field-label-right">Schedule No:</td>
                            <td class="field-val" style="font-weight: bold;">
                                {{ $schedule->schedule_number ?? 'SCH-8AECB25HB5231' }}</td>
                        </tr>
                        <tr>
                            <td class="field-label-right">Schedule Date:</td>
                            <td class="field-val">
                                {{ isset($schedule->schedule_date) ? \Carbon\Carbon::parse($schedule->schedule_date)->format('F d, Y h:i A') : '—' }}
                            </td>
                        </tr>
                        <tr>
                            <td class="field-label-right">Project Ref:</td>
                            <td class="field-val">{{ $schedule->reference_project ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="field-label-right">Status:</td>
                            <td class="field-val">
                                <span class="badge-pill">
                                    {{ strtoupper($schedule->status ?? 'SCHEDULED') }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    {{-- Financial Summary Cards --}}
    <div class="financial-summary-box">
        <div class="section-title" style="margin-bottom: 6px;">Schedule Financial Summary</div>
        <table class="financial-grid">
            <tr>
                <td>
                    <div class="fin-card">
                        <div style="font-size: 7px; text-transform: uppercase; font-weight: bold; color: #64748b;">Total
                            Revenue</div>
                        <div style="font-size: 9px; font-weight: bold; margin-top: 2px;">PHP
                            {{ number_format($totalRevenue, 2) }}</div>
                    </div>
                </td>
                <td>
                    <div class="fin-card">
                        <div style="font-size: 7px; text-transform: uppercase; font-weight: bold; color: #64748b;">Staff
                            Payouts (Paid)</div>
                        <div style="font-size: 9px; font-weight: bold; margin-top: 2px;">PHP
                            {{ number_format($totalPaidPersonnel, 2) }}</div>
                    </div>
                </td>
                <td>
                    <div class="fin-card">
                        <div style="font-size: 7px; text-transform: uppercase; font-weight: bold; color: #64748b;">
                            Operational Expenses</div>
                        <div style="font-size: 9px; font-weight: bold; margin-top: 2px;">PHP
                            {{ number_format($totalExpenses, 2) }}</div>
                    </div>
                </td>
                <td>
                    <div class="fin-card">
                        <div style="font-size: 7px; text-transform: uppercase; font-weight: bold; color: #64748b;">Net
                            Profit</div>
                        <div style="font-size: 9px; font-weight: bold; margin-top: 2px;">PHP
                            {{ number_format($netJobProfit, 2) }}</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- Scope of Work Executed --}}
    <div style="font-size: 8.5px; font-weight: bold; text-transform: uppercase; margin-bottom: 4px; color: #0f172a;">
        Scope of Work & Services Rendered</div>
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 20px;">#</th>
                <th>Description</th>
                <th class="text-center" style="width: 35px;">Qty</th>
                <th style="width: 75px; text-align: right;">Unit Price</th>
                <th style="width: 80px; text-align: right;">Total</th>
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
                        <td style="padding-left: 10px;">
                            <span
                                style="font-weight: bold; margin-right: 3px; color: #64748b;">{{ $numbering }}</span>
                            <span>{{ $subItem->description ?? 'Sub-item description' }}</span>
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

    {{-- Assigned Staff & Compensation --}}
    <div style="font-size: 8.5px; font-weight: bold; text-transform: uppercase; margin-bottom: 4px; color: #0f172a;">
        Assigned Staff & Compensation</div>
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 20px;">#</th>
                <th>Staff Name</th>
                <th>Role / Assignment</th>
                <th>Notes</th>
                <th style="width: 60px; text-align: center;">Status</th>
                <th style="width: 70px; text-align: right;">Amount</th>
                <th style="width: 90px; text-align: center;">Signature</th>
                <th style="width: 65px; text-align: center;">Date Signed</th>
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
                            <span class="badge-pill">PAID</span>
                        @else
                            <span class="badge-pill">UNPAID</span>
                        @endif
                    </td>
                    <td style="text-align: right; font-weight: bold;">PHP {{ number_format($p->payment ?? 0, 2) }}
                    </td>
                    <td></td>
                    <td></td>
                </tr>
            @empty
                <tr>
                    <td colspan="8"
                        style="text-align: center; color: #64748b; font-style: italic; padding: 8px 5px;">
                        No staff assigned to this schedule.
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if (collect($schedule->personnel ?? [])->isNotEmpty())
            <tfoot>
                <tr>
                    <td colspan="5" style="text-align: right;">Total Staff Payout (Overall):</td>
                    <td style="text-align: right;">PHP {{ number_format($totalPersonnelOverall, 2) }}</td>
                    <td colspan="2"></td>
                </tr>
                <tr>
                    <td colspan="5" style="text-align: right;">Total Staff Payout (Paid Only):</td>
                    <td style="text-align: right;">PHP {{ number_format($totalPaidPersonnel, 2) }}</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        @endif
    </table>

    {{-- Operational Expenses --}}
    <div style="font-size: 8.5px; font-weight: bold; text-transform: uppercase; margin-bottom: 4px; color: #0f172a;">
        Operational Expenses (Fuel, Tolls, Supplies, Etc.)</div>
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 20px;">#</th>
                <th>Expense Type</th>
                <th>Reference / Notes</th>
                <th style="width: 80px; text-align: right;">Amount</th>
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
                    <td colspan="4"
                        style="text-align: center; color: #64748b; font-style: italic; padding: 8px 5px;">
                        No operational expenses recorded.
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if (collect($schedule->expenses ?? [])->isNotEmpty())
            <tfoot>
                <tr>
                    <td colspan="3" style="text-align: right;">Total Operational Expenses:</td>
                    <td style="text-align: right;">PHP {{ number_format($totalExpenses, 2) }}</td>
                </tr>
            </tfoot>
        @endif
    </table>

    {{-- General Remarks / Notes --}}
    @if (!empty($schedule->remarks))
        <div class="notes-box">
            <strong>Internal Operational Remarks:</strong><br>
            {!! nl2br(e($schedule->remarks)) !!}
        </div>
    @endif

    {{-- Signatory Section --}}
    <div class="signatory-container">
        <div class="signatory-box">
            <div
                style="font-size: 8px; font-weight: bold; text-transform: uppercase; margin-bottom: 3px; color: #475569;">
                Prepared By
            </div>
            <div style="width: 150px; height: 30px; text-align: center; margin: 0 auto;">
                @if (!empty($signatureDataUri))
                    <img src="{{ $signatureDataUri }}" alt="eSignature"
                        style="max-height: 28px; max-width: 150px; width: auto; display: inline-block;">
                @endif
            </div>
            <div style="border-bottom: 1px solid #475569; width: 100%; margin-bottom: 3px; margin-top: 2px;"></div>
            <div style="font-weight: bold; font-size: 8px; color: #0f172a;">
                {{ $executedBy ?? config('company.name') }}</div>
            <div style="font-size: 7.5px; color: #64748b;">Operations Management</div>
        </div>
    </div>

    {{-- Dompdf Page Numbering Script --}}
    <script type="text/php">
        if (isset($pdf)) {
            $font = $fontMetrics->getFont("Helvetica", "normal");
            $size = 7.5;
            $color = array(0.39, 0.45, 0.55);
            
            $companyText = "{{ config('company.name') }}";
            $pdf->page_text(40, 810, $companyText, $font, $size, $color);
            
            $scheduleNumber = "{{ $schedule->schedule_number ?? 'SCH-8AECB25HB5231' }}";
            $pageText = "Page {PAGE_NUM} of {PAGE_COUNT} | " . $scheduleNumber;
            $pdf->page_text(440, 810, $pageText, $font, $size, $color);
        }
    </script>
</body>

</html>
