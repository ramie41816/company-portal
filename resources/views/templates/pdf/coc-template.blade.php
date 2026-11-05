@php
    if (isset($template_viewing) && $template_viewing):
        // Self-contained fallback defaults so this template handles empty data gracefully
        $quotation =
            $quotation ??
            (object) [
                'schedule_number' => 'QT-SAMPLE-000',
                'scheduled_to' => 'Valued Client Name',
                'client_company' => 'Sample Client Corporation',
                'client_address' => '123 Business Rd, Metro Manila',
                'client_phone' => '+63 912 345 6789',
                'contact_email' => 'client@example.com',
                'schedule_date' => now(),
                'reference_project' => 'Sample Enterprise System Implementation',
                'total' => 150000.0,
                'items' => collect([
                    (object) [
                        'id' => 1,
                        'parent_id' => null,
                        'description' => 'System Analysis & Requirements Gathering',
                        'quantity' => 1,
                        'unit_price' => 50000.0,
                    ],
                    (object) [
                        'id' => 2,
                        'parent_id' => null,
                        'description' => 'Core Module Development & Integration',
                        'quantity' => 1,
                        'unit_price' => 100000.0,
                    ],
                ]),
                'attention' => 'Jane Doe',
            ];

        $processedItems = $processedItems ?? [
            ['item' => (object) ['id' => 1], 'included' => true, 'remarks' => 'Completed on schedule.'],
            ['item' => (object) ['id' => 2], 'included' => true, 'remarks' => 'Tested and deployed successfully.'],
        ];

        $executedBy = $executedBy ?? 'System Administrator';
        $generalRemarks =
            $generalRemarks ?? 'All project milestones have been met successfully with full client acceptance.';
        $signatureDataUri = $signatureDataUri ?? null;
    endif;

    $hasQuotation = isset($schedule->quotation_id) && !empty($schedule->quotation_id);
    $quotationNumber = $hasQuotation ? optional($schedule->quotation)->quotation_number : null;
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Certificate of Completion - {{ $schedule->schedule_number ?? 'SAMPLE' }}</title>
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
            font-size: 20px;
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
            font-size: 10.5px;
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

        .completion-statement {
            margin-bottom: 12px;
            text-align: justify;
            color: #2d3748;
            font-size: 10px;
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

        .badge-skipped {
            color: #991b1b;
            background-color: #fee2e2;
            border: 1px solid #fecaca;
        }

        .notes-box {
            font-size: 9.5px;
            line-height: 1.4;
            color: #2d3748;
            background: #fdfdfd;
            border: 1px solid #e2e8f0;
            border-left: 3px solid #166534;
            padding: 8px 10px;
            border-radius: 4px;
            margin-bottom: 12px;
        }

        .signatory-container {
            border: 1px solid #e2e8f0;
            background-color: #f8fafc;
            border-radius: 4px;
            padding: 10px;
            margin-top: 10px;
            page-break-inside: avoid;
        }

        .signatory-table {
            width: 100%;
            border-collapse: collapse;
        }

        .signatory-table td {
            width: 50%;
            vertical-align: top;
            padding: 0 6px;
        }

        .signatory-card {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            padding: 8px;
            border-radius: 4px;
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
                <div class="schedule-main-title">Certificate of Completion</div>
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
                        @if (($schedule->scheduled_to ?? '') !== ($schedule->client_company ?? ''))
                            <tr>
                                <td class="field-label">Client Name:</td>
                                <td class="field-val">{{ $schedule->scheduled_to ?? '—' }}</td>
                            </tr>
                        @endif
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
                        <tr>
                            <td class="field-label">Email:</td>
                            <td class="field-val">{{ $schedule->contact_email ?? '—' }}</td>
                        </tr>
                    </table>
                </td>
                <td style="padding-left: 8px; border-left: 1px solid #cbd5e1;">
                    <div class="section-title">Schedule Overview</div>
                    <table class="field-table">
                        @if ($hasQuotation)
                            <tr>
                                <td class="field-label-right">Quotation Ref:</td>
                                <td class="field-val" style="font-weight: bold; color: #0f3460;">
                                    {{ $quotationNumber }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td class="field-label-right">Schedule Number:</td>
                            <td class="field-val" style="font-weight: bold; color: #0f3460;">
                                {{ $schedule->schedule_number ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="field-label-right">Issue Date:</td>
                            <td class="field-val">
                                {{ isset($schedule->schedule_date) ? \Carbon\Carbon::parse($schedule->schedule_date)->format('d/m/Y') : now()->format('d/m/Y') }}
                            </td>
                        </tr>
                        <tr>
                            <td class="field-label-right">Project Reference:</td>
                            <td class="field-val">{{ $schedule->reference_project ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="field-label-right">Total Amount:</td>
                            <td class="field-val" style="font-weight: bold; color: #0f3460;">PHP
                                {{ number_format($schedule->total ?? 0, 2) }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    {{-- Statement Section --}}
    <div class="completion-statement">
        This is to certify that the services and scope of work outlined in
        @if ($hasQuotation)
            quotation reference <strong>{{ $quotationNumber }}</strong> under schedule reference
        @else
            schedule reference
        @endif
        <strong>{{ $schedule->schedule_number ?? '—' }}</strong> have been inspected, reviewed, and executed in
        accordance with the specified agreements. Below is the itemized completion status and corresponding remarks:
    </div>

    {{-- Scope of Work Items Section --}}
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 30px;">#</th>
                <th>Description</th>
                <th class="text-center" style="width: 45px;">Qty</th>
                <th style="width: 85px; text-align: center;">Status</th>
                <th style="width: 130px;">Remarks</th>
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
                    $mainData = collect($processedItems)->firstWhere('item.id', $item->id);
                @endphp
                {{-- Main Item Row --}}
                <tr>
                    <td style="font-weight: bold;">{{ $x }}</td>
                    <td style="font-weight: bold;">{{ $item->description ?? 'Item description' }}</td>
                    <td class="text-center">
                        {{ ($item->unit_price ?? 0) > 0 && ($item->quantity ?? 0) > 0 ? rtrim(rtrim(number_format($item->quantity, 2), '0'), '.') : '-' }}
                    </td>
                    <td class="text-center">
                        @if ($mainData && ($mainData['included'] ?? false))
                            <span class="badge-pill badge-completed">COMPLETED</span>
                        @else
                            <span class="badge-pill badge-skipped">NOT EXECUTED</span>
                        @endif
                    </td>
                    <td>{{ $mainData['remarks'] ?? '—' }}</td>
                </tr>

                {{-- Sub-items Rows --}}
                @foreach ($subItems as $subItem)
                    @php
                        $subData = collect($processedItems)->firstWhere('item.id', $subItem->id);
                        $numbering = $x . '.' . $y;
                    @endphp
                    <tr>
                        <td></td>
                        <td style="padding-left: 12px;">
                            <span
                                style="color: #166534; font-weight: bold; margin-right: 4px;">{{ $numbering }}</span>
                            <span style="color: #4a5568;">{{ $subItem->description ?? 'Sub-item description' }}</span>
                        </td>
                        <td class="text-center">
                            {{ ($subItem->unit_price ?? 0) > 0 || ($subItem->quantity ?? 0) > 0 ? rtrim(rtrim(number_format($subItem->quantity, 2), '0'), '.') : '—' }}
                        </td>
                        <td class="text-center">
                            @if ($subData && ($subData['included'] ?? false))
                                <span class="badge-pill badge-completed">COMPLETED</span>
                            @else
                                <span class="badge-pill badge-skipped">NOT EXECUTED</span>
                            @endif
                        </td>
                        <td>{{ $subData['remarks'] ?? '—' }}</td>
                    </tr>
                    @php $y++; @endphp
                @endforeach

                @php $x++; @endphp
            @endforeach
        </tbody>
    </table>

    {{-- General Remarks Section --}}
    @if (!empty($generalRemarks))
        <div class="notes-box">
            <strong>Completion Notes:</strong><br>
            {!! nl2br(e($generalRemarks)) !!}
        </div>
    @endif

    {{-- Signatory Section Container --}}
    <div class="signatory-container">
        <div
            style="font-size: 10px; font-weight: 700; color: #0f3460; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px;">
            Signatory Authorization & Acceptance
        </div>

        <table class="signatory-table">
            <tr>
                <td>
                    <div class="signatory-card">
                        <div
                            style="font-size: 9.5px; font-weight: 700; color: #0f3460; text-transform: uppercase; margin-bottom: 6px;">
                            Prepared / Executed By
                        </div>

                        <div style="width: 160px; height: 50px; text-align: center; margin: 2px auto 0 auto;">
                            @if (!empty($signatureDataUri))
                                <img src="{{ $signatureDataUri }}" alt="eSignature"
                                    style="max-height: 48px; max-width: 160px; width: auto; display: inline-block;">
                            @endif
                        </div>

                        <div
                            style="border-bottom: 1px solid #4a5568; width: 100%; margin-bottom: 4px; margin-top: 2px;">
                        </div>

                        <div style="font-weight: bold; color: #1a202c; font-size: 9.5px;">
                            {{ $executedBy ?? config('company.name') }}
                        </div>
                        <div style="color: #4a5568; font-size: 9px;">Authorized Representative</div>
                    </div>
                </td>

                <td>
                    <div class="signatory-card">
                        <div
                            style="font-size: 9.5px; font-weight: 700; color: #0f3460; text-transform: uppercase; margin-bottom: 6px;">
                            Conforme / Client Acceptance
                        </div>

                        <div style="width: 160px; height: 50px; margin: 2px auto 0 auto;"></div>

                        <div
                            style="border-bottom: 1px solid #4a5568; width: 100%; margin-bottom: 4px; margin-top: 2px;">
                        </div>

                        <div style="font-weight: bold; color: #1a202c; font-size: 9.5px;">
                            {{ $schedule->attention ?? ($schedule->scheduled_to ?? 'Client Name') }}
                        </div>
                        <div style="color: #4a5568; font-size: 9px;">Date: ________________________</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- Dompdf Page Numbering Script --}}
    <script type="text/php">
        if (isset($pdf)) {
            $font = $fontMetrics->getFont("Helvetica", "normal");
            $size = 9;
            $color = array(0, 0, 0, 0);
            
            $companyText = "{{ config('company.name') }}";
            $pdf->page_text(25, 795, $companyText, $font, $size, $color);
            
            $scheduleNumber = "{{ $schedule->schedule_number ?? 'SCH-6AECB05DB5031' }}";
            $pageText = "Page {PAGE_NUM} of {PAGE_COUNT} | " . $scheduleNumber;
            $pdf->page_text(424, 795, $pageText, $font, $size, $color);
        }
    </script>
</body>

</html>
