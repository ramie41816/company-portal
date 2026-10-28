<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Quotation {{ $quotation->quotation_number }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 15mm 12mm 15mm;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1a202c;
            font-size: 11px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
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
            color: #2b3a67;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin: 0 0 5px 0;
        }

        .company-meta-box {
            font-size: 10.5px;
            color: #4a5568;
            line-height: 1.5;
            width: 55%;
        }

        .divider {
            border-bottom: 2px solid #2b3a67;
            margin: 15px 0;
        }

        /* Quote To / Meta Section Grid */
        .info-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .info-grid>tbody>tr>td {
            width: 50%;
            vertical-align: top;
        }

        .section-subtitle {
            font-size: 12px;
            font-weight: bold;
            color: #2b3a67;
            margin-bottom: 6px;
            text-transform: uppercase;
        }

        /* Robust Sub-table for fields */
        .field-table {
            width: 100%;
            border-collapse: collapse;
        }

        .field-table td {
            padding-bottom: 4px;
            vertical-align: top;
        }

        .field-label {
            font-weight: 600;
            color: #4a5568;
            width: 105px;
            white-space: nowrap;
        }

        .field-label-right {
            font-weight: 600;
            color: #4a5568;
            width: 135px;
            white-space: nowrap;
        }

        .field-val {
            color: #1a202c;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .items-table th {
            background-color: #f7fafc;
            color: #2d3748;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 8px 10px;
            border-top: 1px solid #cbd5e0;
            border-bottom: 2px solid #cbd5e0;
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
            padding: 9px 10px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 11px;
            color: #2d3748;
            vertical-align: top;
        }

        /* Totals Block */
        .totals-table {
            width: 300px;
            float: right;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 20px;
        }

        .totals-table td {
            padding: 6px 10px;
            font-size: 11px;
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
            border-top: 2px solid #2b3a67 !important;
            border-bottom: 2px solid #2b3a67 !important;
            font-weight: bold !important;
            background-color: #f7fafc;
            font-size: 12px;
            color: #2b3a67 !important;
        }

        .clear {
            clear: both;
        }

        /* Signatory Section */
        .signatory-section {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
            page-break-inside: avoid;
        }

        .signatory-section td {
            width: 50%;
            vertical-align: top;
            padding: 0 15px;
        }

        .signatory-box {
            font-size: 11px;
        }

        /* Footer & Terms */
        .footer-section {
            margin-top: 25px;
            page-break-inside: avoid;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
        }

        .terms-box {
            font-size: 9.5px;
            color: #4a5568;
            line-height: 1.5;
            width: 100%;
        }
    </style>
</head>

<body>

    {{-- Header Section --}}
    <table class="w-full header-table">
        <tr>
            <td>
                <div class="quotation-main-title">Quotation</div>
            </td>
            <td style="text-align: right;">
                @if (config('company.logo'))
                    <img src="{{ public_path(config('company.logo')) }}" alt="Logo" height="36"
                        style="display: inline-block;">
                @endif
            </td>
        </tr>
    </table>

    <div class="company-meta-box">
        <div><strong>{{ config('company.name') }}</strong></div>
        @if (config('company.tagline'))
            <div style="color: #718096; font-style: italic; margin-bottom: 3px;">{{ config('company.tagline') }}</div>
        @endif
        <div><strong>Address:</strong> {{ config('company.address') }}</div>
        <div><strong>Phone:</strong> {{ config('company.phone') }}</div>
        <div><strong>Email:</strong> {{ config('company.email') }}</div>
        @if (config('company.website'))
            <div><strong>Website:</strong> {{ config('company.website') }}</div>
        @endif
    </div>

    <div class="divider"></div>

    {{-- Quote To & Meta Information Grid --}}
    <table class="info-grid">
        <tr>
            <td>
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
            <td>
                <div style="padding-left: 20px;">
                    <table class="field-table">
                        <tr>
                            <td class="field-label-right">Quotation Number:</td>
                            <td class="field-val" style="font-weight: bold;">{{ $quotation->quotation_number }}</td>
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
                </div>
            </td>
        </tr>
    </table>

    <p style="margin-bottom: 12px; text-align: justify;">
        Dear <strong>{{ $quotation->attention ?? $quotation->quoted_to }}</strong>,<br><br>
        Thank you for the opportunity to quote
        @if ($quotation->reference_project)
            for <strong>{{ $quotation->reference_project }}</strong>.
        @else
            for your project.
        @endif
        We are pleased to submit our formal proposal breakdown below for your review and consideration:
    </p>

    {{-- Items Table --}}
    <table class="items-table">
        <thead>
            <tr>
                <th>Description</th>
                <th class="text-center" style="width: 70px;">Quantity</th>
                <th class="text-end" style="width: 110px;">Unit Price</th>
                <th class="text-end" style="width: 110px;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($quotation->items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td class="text-center">{{ rtrim(rtrim(number_format($item->quantity, 2), '0'), '.') }}</td>
                    <td class="text-end">
                        {!! 'PHP ' . number_format($item->unit_price, 2) !!}
                    </td>
                    <td class="text-end">
                        {!! 'PHP ' . number_format($item->total, 2) !!}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Financial Totals Block --}}
    <table class="totals-table">
        <tr class="grand-total-row">
            <td class="label" style="color: #2b3a67;">TOTAL QUOTATION</td>
            <td class="value" style="color: #2b3a67;">
                {!! 'PHP ' . number_format($quotation->total, 2) !!}
            </td>
        </tr>
    </table>

    <div class="clear"></div>

    <p style="margin-top: 5px; margin-bottom: 20px;">
        We trust that this proposal aligns with your requirements. Your favorable approval would be greatly appreciated.
        We look forward to the opportunity to serve you and express our gratitude in advance for considering us for this
        contract job.
    </p>

    <table class="signatory-section" style="width: 100%; border-collapse: collapse;">
        <tr>
            <!-- Left Side: Prepared By -->
            <td style="padding-left: 0; width: 50%; vertical-align: top;">
                <div class="signatory-box">
                    <strong>Prepared By:</strong>

                    {{-- Container width precisely matches the 180px line width so centering aligns perfectly --}}
                    <div style="width: 180px; height: 50px; text-align: center; margin-top: 5px; margin-bottom: 0;">
                        @if (!empty($tempSignaturePath) && file_exists(storage_path('app/public/' . $tempSignaturePath)))
                            <img src="{{ storage_path('app/public/' . $tempSignaturePath) }}" alt="eSignature"
                                style="max-height: 48px; max-width: 180px; width: auto; display: inline-block;">
                        @endif
                    </div>

                    {{-- Exactly 180px underline matching the image container width --}}
                    <div style="border-bottom: 1px solid #1a202c; width: 180px; margin-bottom: 4px; margin-top: 2px;">
                    </div>

                    <div style="font-weight: bold; color: #1a202c;">
                        {{ $quotation->prepared_by ?? config('company.name') }}
                    </div>
                    <div style="color: #4a5568; font-size: 10px;">Authorized Representative</div>
                </div>
            </td>

            <!-- Right Side: Conforme (Client Acceptance) -->
            <td style="padding-right: 0; width: 50%; vertical-align: top;">
                <div class="signatory-box">
                    <strong>Conforme (Client Acceptance):</strong>

                    {{-- Matching 180px width & height spacer block to keep rows symmetrical --}}
                    <div style="width: 180px; height: 50px; margin-top: 5px; margin-bottom: 0;"></div>

                    {{-- Matching 180px underline --}}
                    <div style="border-bottom: 1px solid #1a202c; width: 180px; margin-bottom: 4px; margin-top: 2px;">
                    </div>

                    <div style="font-weight: bold; color: #1a202c;">
                        {{ $quotation->attention ?? $quotation->quoted_to }}
                    </div>
                    <div style="color: #4a5568; font-size: 10px;">Date: ________________________</div>
                </div>
            </td>
        </tr>
    </table>

    {{-- Footer & Terms --}}
    <div class="footer-section">
        <div class="terms-box">
            <strong>Terms & Conditions:</strong><br>
            • This quotation is valid for 30 days from the issue date.<br>
            • Prices are subject to change without prior notice after validity period.<br>
            • Thank you for the opportunity to quote.
        </div>
    </div>

</body>

</html>
