<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Certificate of Completion - {{ $quotation->quotation_number }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 32mm 15mm 28mm 15mm;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #2d3748;
            font-size: 10.5px;
            line-height: 1.5;
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

        .quotation-main-title {
            font-size: 22px;
            font-weight: 800;
            color: #0f3460;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin: 0 0 2px 0;
        }

        .company-tagline {
            font-size: 10px;
            color: #166534;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 6px;
        }

        .company-meta-box {
            font-size: 10px;
            color: #4a5568;
            line-height: 1.4;
        }

        .info-grid-container {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px 15px;
            margin: 10px 0;
        }

        .info-grid {
            width: 100%;
            border-collapse: collapse;
        }

        .info-grid>tbody>tr>td {
            width: 50%;
            vertical-align: top;
        }

        .section-subtitle {
            font-size: 11px;
            font-weight: 700;
            color: #0f3460;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 4px;
        }

        .field-table {
            width: 100%;
            border-collapse: collapse;
        }

        .field-table td {
            padding-bottom: 3px;
            vertical-align: top;
            font-size: 10px;
        }

        .field-label {
            font-weight: 600;
            color: #4a5568;
            width: 95px;
            white-space: nowrap;
        }

        .field-label-right {
            font-weight: 600;
            color: #4a5568;
            width: 115px;
            white-space: nowrap;
        }

        .field-val {
            color: #1a202c;
        }

        .completion-statement {
            margin: 12px 0;
            text-align: justify;
            color: #2d3748;
            font-size: 10.5px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            margin-bottom: 10px;
        }

        .items-table th {
            background-color: #0f3460;
            color: #ffffff;
            font-size: 9.5px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 7px 10px;
            text-align: left;
            font-weight: bold;
        }

        .items-table td {
            padding: 7px 10px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 10px;
            color: #2d3748;
            vertical-align: top;
        }

        .badge-pill {
            font-weight: bold;
            padding: 3px 7px;
            border-radius: 4px;
            font-size: 8.5px;
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

        .signatory-section {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            page-break-inside: avoid;
        }

        .signatory-section td {
            width: 50%;
            vertical-align: top;
            padding: 0 10px;
        }

        .signatory-box {
            font-size: 10px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            padding: 10px;
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
    <table class="w-full header-table">
        <tr>
            <td>
                <div class="quotation-main-title">Certificate of Completion</div>
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
                    <img src="{{ public_path(config('company.logo')) }}" alt="Logo" height="70"
                        style="display: inline-block;">
                @endif
            </td>
        </tr>
    </table>

    {{-- Client & Quotation Overview Grid Container --}}
    <div class="info-grid-container">
        <table class="info-grid">
            <tr>
                <td style="padding-right: 10px;">
                    <div class="section-subtitle">Quote To (Client)</div>
                    <table class="field-table">
                        @if ($quotation->quoted_to !== $quotation->client_company)
                            <tr>
                                <td class="field-label">Client Name:</td>
                                <td class="field-val">{{ $quotation->quoted_to }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td class="field-label">Company Name:</td>
                            <td class="field-val">{{ $quotation->client_company ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="field-label">Address:</td>
                            <td class="field-val">{{ $quotation->client_address ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="field-label">Phone:</td>
                            <td class="field-val">{{ $quotation->client_phone ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="field-label">Email:</td>
                            <td class="field-val">{{ $quotation->contact_email ?? '—' }}</td>
                        </tr>
                    </table>
                </td>
                <td style="padding-left: 10px; border-left: 1px solid #cbd5e1;">
                    <div class="section-subtitle">Quotation Overview</div>
                    <table class="field-table">
                        <tr>
                            <td class="field-label-right">Quotation Number:</td>
                            <td class="field-val" style="font-weight: bold; color: #0f3460;">
                                {{ $quotation->quotation_number }}</td>
                        </tr>
                        <tr>
                            <td class="field-label-right">Issue Date:</td>
                            <td class="field-val">{{ $quotation->quotation_date->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td class="field-label-right">Project Reference:</td>
                            <td class="field-val">{{ $quotation->reference_project ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="field-label-right">Total Amount:</td>
                            <td class="field-val" style="font-weight: bold; color: #0f3460;">PHP
                                {{ number_format($quotation->total, 2) }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <div class="completion-statement">
        This is to certify that the services and scope of work outlined under quotation reference
        <strong>{{ $quotation->quotation_number }}</strong> have been inspected, reviewed, and executed in accordance
        with the specified agreements. Below is the itemized completion status and corresponding remarks:
    </div>

    {{-- Scope of Work Items Table --}}
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 25px;">#</th>
                <th>Description</th>
                <th class="text-center" style="width: 55px;">Qty</th>
                <th style="width: 90px; text-align: center;">Status</th>
                <th style="width: 140px;">Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($processedItems as $index => $data)
                @php
                    $item = $data['item'];
                    $num = $index + 1;
                @endphp
                <tr>
                    <td>{{ $num }}</td>
                    <td>{{ $item->description }}</td>
                    <td class="text-center">{{ rtrim(rtrim(number_format($item->quantity, 2), '0'), '.') }}</td>
                    <td class="text-center">
                        @if ($data['included'])
                            <span class="badge-pill badge-completed">COMPLETED</span>
                        @else
                            <span class="badge-pill badge-skipped">NOT EXECUTED</span>
                        @endif
                    </td>
                    <td>{{ $data['remarks'] ?: '—' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- General Remarks Block --}}
    @if (!empty($generalRemarks))
        <div
            style="margin-top: 10px; margin-bottom: 10px; font-size: 10px; line-height: 1.4; color: #2d3748; background: #fdfdfd; border-left: 3px solid #166534; padding: 6px 10px;">
            <strong>Completion Notes:</strong><br>
            {!! nl2br(e($generalRemarks)) !!}
        </div>
    @endif

    {{-- Signatory Section with E-Signature Binding --}}
    <table class="signatory-section">
        <tr>
            <td>
                <div class="signatory-box">
                    <strong style="color: #0f3460;">Prepared / Executed By:</strong>

                    <div style="width: 180px; height: 45px; text-align: center; margin: 4px auto 0 auto;">
                        @if (!empty($tempSignaturePath) && file_exists($tempSignaturePath))
                            <img src="{{ $tempSignaturePath }}" alt="eSignature"
                                style="max-height: 43px; max-width: 180px; width: auto; display: inline-block;">
                        @endif
                    </div>

                    <div style="border-bottom: 1px solid #4a5568; width: 100%; margin-bottom: 4px;"></div>
                    <div style="font-weight: bold; color: #1a202c;">
                        {{ $executedBy }}</div>
                    <div style="color: #4a5568; font-size: 9.5px;">Authorized Representative</div>
                </div>
            </td>
            <td>
                <div class="signatory-box">
                    <strong style="color: #0f3460;">Received & Accepted By (Client):</strong>
                    <div style="width: 180px; height: 45px; margin: 4px auto 0 auto;"></div>
                    <div style="border-bottom: 1px solid #4a5568; width: 100%; margin-bottom: 4px;"></div>
                    <div style="font-weight: bold; color: #1a202c;">
                        {{ $quotation->attention ?? $quotation->quoted_to }}</div>
                    <div style="color: #4a5568; font-size: 9.5px;">Date: ________________________</div>
                </div>
            </td>
        </tr>
    </table>

    {{-- Dompdf Page Numbering Script --}}
    <script type="text/php">
        if (isset($pdf)) {
            $font = $fontMetrics->getFont("Helvetica", "normal");
            $size = 9;
            $color = array(0, 0, 0, 0);
            
            $companyText = "{{ config('company.name') }}";
            $pdf->page_text(25, 795, $companyText, $font, $size, $color);
            
            $pageText = "Page {PAGE_NUM} of {PAGE_COUNT} | {{ $quotation->quotation_number }}";
            $pdf->page_text(420, 795, $pageText, $font, $size, $color);
        }
    </script>
</body>

</html>
