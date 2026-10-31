<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Quotation {{ $quotation->quotation_number }}</title>
    <style>
        @page {
            size: A4 portrait;
            /* Top and bottom margins reserve space for the 100px fixed banners */
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

        /* True Word-like Running Header Banner (Fixed 100px Height) */
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

        /* True Word-like Running Footer Banner (Fixed 100px Height) */
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

        /* Header Block */
        .header-table td {
            vertical-align: top;
        }

        .quotation-main-title {
            font-size: 24px;
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

        /* Quote To / Meta Section Grid */
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

        /* Field tables */
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

        /* Greeting */
        .greeting-text {
            margin-bottom: 10px;
            text-align: justify;
            color: #2d3748;
        }

        /* Items Table */
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

        .items-table th.text-end,
        .items-table td.text-end {
            text-align: right;
        }

        .items-table th.text-center,
        .items-table td.text-center {
            text-align: center;
        }

        .items-table td {
            padding: 7px 10px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 10.5px;
            color: #2d3748;
            vertical-align: top;
        }

        .main-item-row td {
            background-color: #ffffff;
            font-weight: bold;
        }

        .sub-item-row td {
            background-color: #f8fafc;
            color: #4a5568;
            font-size: 10px;
            padding: 5px 10px !important;
            border-bottom: 1px solid #edf2f7;
        }

        /* Totals Block */
        .totals-table {
            width: 280px;
            float: right;
            border-collapse: collapse;
            margin-top: 5px;
            margin-bottom: 10px;
        }

        .totals-table td {
            padding: 5px 10px;
            font-size: 10.5px;
            border-bottom: 1px solid #e2e8f0;
        }

        .totals-table td.label {
            font-weight: 600;
            color: #4a5568;
            text-align: right;
        }

        .totals-table td.value {
            text-align: right;
            font-weight: 600;
            color: #1a202c;
        }

        .grand-total-row td {
            border-top: 2px solid #0f3460 !important;
            border-bottom: 2px solid #0f3460 !important;
            font-weight: bold !important;
            background-color: #f1f5f9;
            font-size: 11.5px;
            color: #0f3460 !important;
        }

        .clear {
            clear: both;
        }

        /* Signatory Section */
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
            /* Pure white background for e-signature compatibility */
            border: 1px solid #e2e8f0;
            padding: 10px;
            border-radius: 4px;
        }

        /* Terms & Conditions Section */
        .footer-terms-section {
            margin-top: 15px;
            page-break-inside: avoid;
            border-top: 1.5px solid #166534;
            padding-top: 8px;
            margin-bottom: 15px;
        }

        .terms-box {
            font-size: 9px;
            color: #4a5568;
            line-height: 1.3;
            width: 100%;
        }

        /* Quill Editor Bullet Fix */
        ol li[data-list="bullet"] {
            list-style-type: disc !important;
            display: list-item;
        }

        body ol {
            margin: 0 0 10px 15px;
            padding-left: 0;
        }

        /* Dompdf Page Numbering Script Styling */
        #pagenum:before {
            content: counter(page);
        }

        #totalpages:before {
            content: counter(pages);
        }
    </style>
</head>

<body>
    {{-- Fixed Header Banner (100px Height, Repeats on every page) --}}
    <div class="header-banner-fixed">
        <img src="{{ public_path('images/banner/header.jpg') }}" alt="Header Banner">
    </div>

    {{-- Fixed Footer Banner (100px Height, Repeats on every page) --}}
    <div class="footer-banner-fixed">
        <img src="{{ public_path('images/banner/footer.jpg') }}" alt="Footer Banner">
    </div>

    {{-- Header Section --}}
    <table class="w-full header-table">
        <tr>
            <td>
                <div class="quotation-main-title">Quotation</div>
                @if (config('company.tagline'))
                    <div class="company-tagline">{{ config('company.tagline') }}</div>
                @endif
                <div class="company-meta-box">
                    <div><strong>{{ strtoupper(config('company.name')) }}</strong></div>
                    <div>{{ config('company.address') }}</div>
                    <div>Phone: {{ config('company.phone') }} | Email: {{ config('company.email') }}</div>
                    @if (config('company.website'))
                        <div>Website: {{ config('company.website') }}</div>
                    @endif
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

    {{-- Quote To & Meta Information Grid Container --}}
    <div class="info-grid-container">
        <table class="info-grid">
            <tr>
                <td style="padding-right: 10px;">
                    <div class="section-subtitle">Quote To</div>
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
                        <tr>
                            <td class="field-label">Contact Person:</td>
                            <td class="field-val">{{ $quotation->attention ?? '—' }}</td>
                        </tr>
                    </table>
                </td>
                <td style="padding-left: 10px; border-left: 1px solid #cbd5e1;">
                    <div class="section-subtitle">Quotation Details</div>
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
                            <td class="field-label-right">Valid Until:</td>
                            <td class="field-val">
                                {{ $quotation->valid_until ? $quotation->valid_until->format('d/m/Y') : $quotation->quotation_date->copy()->addDays(30)->format('d/m/Y') }}
                            </td>
                        </tr>
                        <tr>
                            <td class="field-label-right">Reference / Project:</td>
                            <td class="field-val">{{ $quotation->reference_project ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="field-label-right">Prepared By:</td>
                            <td class="field-val">{{ $quotation->prepared_by ?? '—' }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <p class="greeting-text">
        Dear <strong>{{ $quotation->attention ?? $quotation->quoted_to }}</strong>,<br><br>
        Thank you for the opportunity to quote
        @if ($quotation->reference_project)
            for <strong>{{ $quotation->reference_project }}</strong>.
        @else
            for your project.
        @endif
        We are pleased to submit our formal proposal breakdown below for your review and consideration:
    </p>

    {{-- Items Table with Hierarchical Main and Sub-services --}}
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 25px;">#</th>
                <th>Description</th>
                <th class="text-center" style="width: 65px;">Quantity</th>
                <th class="text-end" style="width: 100px;">Unit Price</th>
                <th class="text-end" style="width: 100px;">Amount</th>
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
                        @if ($item->unit_price > 0 || $item->quantity > 0)
                            {{ rtrim(rtrim(number_format($item->quantity, 2), '0'), '.') }}
                        @endif
                    </td>
                    <td class="text-end">
                        @if ($item->unit_price > 0)
                            {!! 'PHP ' . number_format($item->unit_price, 2) !!}
                        @else
                            —
                        @endif
                    </td>
                    <td class="text-end" style="color: #0f3460;">
                        @if ($item->total > 0)
                            {!! 'PHP ' . number_format($item->total, 2) !!}
                        @else
                            —
                        @endif
                    </td>
                </tr>

                {{-- Sub-items rows --}}
                @foreach ($subItems as $subItem)
                    <tr class="sub-item-row">
                        <td></td>
                        <td style="padding-left: 15px;">
                            <span
                                style="color: #166534; font-weight: bold; margin-right: 5px;">{{ "$x.$y" }}</span>
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
    </table>

    {{-- Other Information Block --}}
    @if (!empty($quotation->other_info))
        <div class="other-info-box"
            style="margin-top: 8px; margin-bottom: 8px; font-size: 10px; line-height: 1.4; color: #2d3748; background: #fdfdfd; border-left: 3px solid #166534; padding: 6px 10px;">
            {!! $quotation->other_info !!}
        </div>
    @endif

    {{-- Financial Totals Block --}}
    <table class="totals-table">
        <tr class="grand-total-row">
            <td class="label">TOTAL QUOTATION</td>
            <td class="value">
                {!! 'PHP ' . number_format($quotation->total, 2) !!}
            </td>
        </tr>
    </table>

    <div class="clear"></div>

    <p style="margin-top: 5px; margin-bottom: 10px; color: #2d3748;">
        We trust that this proposal aligns with your requirements. Your favorable approval would be greatly appreciated.
        We look forward to the opportunity to serve you.
    </p>

    {{-- Signatory Section --}}
    <table class="signatory-section">
        <tr>
            <!-- Left Side: Prepared By -->
            <td>
                <div class="signatory-box">
                    <strong style="color: #0f3460;">Prepared By:</strong>

                    <div style="width: 180px; height: 55px; text-align: center; margin: 4px auto 0 auto;">
                        @if (!empty($tempSignaturePath) && file_exists(storage_path('app/public/' . $tempSignaturePath)))
                            <img src="{{ storage_path('app/public/' . $tempSignaturePath) }}" alt="eSignature"
                                style="max-height: 53px; max-width: 180px; width: auto; display: inline-block;">
                        @endif
                    </div>

                    <div style="border-bottom: 1px solid #4a5568; width: 100%; margin-bottom: 4px; margin-top: 2px;">
                    </div>

                    <div style="font-weight: bold; color: #1a202c;">
                        {{ $quotation->prepared_by ?? config('company.name') }}
                    </div>
                    <div style="color: #4a5568; font-size: 9.5px;">Authorized Representative</div>
                </div>
            </td>

            <!-- Right Side: Conforme (Client Acceptance) -->
            <td>
                <div class="signatory-box">
                    <strong style="color: #0f3460;">Conforme (Client Acceptance):</strong>

                    <div style="width: 180px; height: 55px; margin: 4px auto 0 auto;"></div>

                    <div style="border-bottom: 1px solid #4a5568; width: 100%; margin-bottom: 4px; margin-top: 2px;">
                    </div>

                    <div style="font-weight: bold; color: #1a202c;">
                        {{ $quotation->attention ?? $quotation->quoted_to }}
                    </div>
                    <div style="color: #4a5568; font-size: 9.5px;">Date: ________________________</div>
                </div>
            </td>
        </tr>
    </table>

    {{-- Terms & Conditions Section --}}
    <div class="footer-terms-section">
        <table class="w-full">
            <tr>
                <td>
                    <div class="terms-box">
                        <strong>Terms & Conditions:</strong><br>
                        • This quotation is valid for 30 days from the issue date.<br>
                        • Prices are subject to change without prior notice after the validity period.<br>
                        • Thank you for your business!
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- Dompdf PHP Script Tag for Dynamic Page Numbers & Company Name on Every Page --}}
    <script type="text/php">
        if (isset($pdf)) {
            $font = $fontMetrics->getFont("Helvetica", "normal");
            $size = 9;
            $color = array(0, 0, 0, 0);
            
            /* Left side: Company Name */
            $companyText = "{{ config('company.name') }}";
            $pdf->page_text(25, 795, $companyText, $font, $size, $color);
            
            /* Right side: Page Number */
            $pageText = "Page {PAGE_NUM} of {PAGE_COUNT} | {{ $quotation->quotation_number }}";
            $pdf->page_text(455, 795, $pageText, $font, $size, $color);
        }
    </script>
</body>

</html>
