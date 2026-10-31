<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quotation {{ $quotation->quotation_number }}</title>
    <style>
        body {
            background-color: #f8fafc;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #334155;
            line-height: 1.6;
            font-size: 14.5px;
            margin: 0;
            padding: 0;
        }

        .email-wrapper {
            width: 100%;
            background-color: #f8fafc;
            padding: 40px 0;
        }

        /* Expanded max-width for a landscape/widescreen PC layout */
        .email-content {
            max-width: 720px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            border: 1px solid #e2e8f0;
        }

        /* Elegant Brand Header */
        .email-header {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            padding: 35px 45px;
            text-align: left;
            border-bottom: 3px solid #3b82f6;
        }

        .email-header h1 {
            color: #ffffff;
            margin: 0 0 4px 0;
            font-size: 22px;
            font-weight: 600;
            letter-spacing: -0.25px;
        }

        .email-header p {
            color: #94a3b8;
            margin: 0;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Body Area */
        .email-body {
            padding: 45px;
        }

        .email-body p {
            margin-top: 0;
            margin-bottom: 20px;
            color: #475569;
        }

        /* Landscape Summary Card Container */
        .quote-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 24px 30px;
            margin: 25px 0;
        }

        .quote-card-title {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #64748b;
            font-weight: 600;
            margin-bottom: 16px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 8px;
        }

        /* Two-column landscape split layout for wider screens */
        .quote-table {
            width: 100%;
            border-collapse: collapse;
        }

        .quote-column {
            width: 50%;
            vertical-align: top;
        }

        .quote-column-left {
            padding-right: 15px;
        }

        .quote-column-right {
            padding-left: 15px;
            border-left: 1px solid #e2e8f0;
        }

        .quote-grid {
            width: 100%;
            border-collapse: collapse;
        }

        .quote-grid td {
            padding: 6px 0;
            font-size: 14px;
            vertical-align: top;
        }

        .quote-grid td.label {
            color: #64748b;
            width: 42%;
            font-weight: 400;
        }

        .quote-grid td.value {
            color: #0f172a;
            font-weight: 600;
        }

        /* Action Reminder Callout */
        .notice-box {
            background-color: #eff6ff;
            border-left: 3px solid #3b82f6;
            padding: 14px 18px;
            border-radius: 0 6px 6px 0;
            font-size: 13.5px;
            color: #1e40af;
            margin-bottom: 25px;
        }

        /* Signature Block */
        .signature-section {
            margin-top: 35px;
            border-top: 1px solid #f1f5f9;
            padding-top: 24px;
        }

        .signature-name {
            font-size: 15px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 2px;
        }

        .signature-company {
            color: #334155;
            font-weight: 500;
            font-size: 13.5px;
            margin-bottom: 6px;
        }

        .signature-details {
            color: #64748b;
            font-size: 13px;
            line-height: 1.5;
        }

        /* Footer */
        .footer {
            background-color: #f8fafc;
            font-size: 12px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding: 24px 45px;
            text-align: center;
        }

        /* Responsive Mobile Adjustments (Stacks columns on smaller screens) */
        @media screen and (max-width: 640px) {
            .email-wrapper {
                padding: 0 !important;
            }

            .email-content {
                width: 100% !important;
                border-radius: 0 !important;
                border: none !important;
            }

            .email-header,
            .email-body,
            .footer {
                padding: 25px 20px !important;
            }

            /* Stack two columns vertically on mobile */
            .quote-column {
                display: block !important;
                width: 100% !important;
            }

            .quote-column-left {
                padding-right: 0 !important;
                padding-bottom: 12px;
            }

            .quote-column-right {
                padding-left: 0 !important;
                border-left: none !important;
                border-top: 1px solid #e2e8f0;
                padding-top: 12px;
            }
        }
    </style>
</head>

<body>
    <div class="email-wrapper">
        <div class="email-content">

            <!-- Header Banner -->
            <div class="email-header">
                <h1>{{ config('company.name') }}</h1>
                @if (config('company.tagline'))
                    <p>{{ config('company.tagline') }}</p>
                @endif
            </div>

            <!-- Content Area -->
            <div class="email-body">
                <p>Dear <strong>{{ $quotation->attention ?? $quotation->quoted_to }}</strong>,</p>

                <p>Thank you for the opportunity to present our proposal. We are pleased to provide our official
                    quotation for your review.</p>

                <!-- Landscape Split Summary Card -->
                <div class="quote-card">
                    <div class="quote-card-title">Quotation Summary</div>
                    <table class="quote-table">
                        <tr>
                            <!-- Left Column -->
                            <td class="quote-column quote-column-left">
                                <table class="quote-grid">
                                    <tr>
                                        <td class="label">Reference No:</td>
                                        <td class="value" style="color: #2563eb;">{{ $quotation->quotation_number }}
                                        </td>
                                    </tr>
                                    @if ($quotation->reference_project)
                                        <tr>
                                            <td class="label">Project Title:</td>
                                            <td class="value">{{ $quotation->reference_project }}</td>
                                        </tr>
                                    @endif
                                </table>
                            </td>
                            <!-- Right Column -->
                            <td class="quote-column quote-column-right">
                                <table class="quote-grid" style="padding-left: 5px;">
                                    <tr>
                                        <td class="label">Issue Date:</td>
                                        <td class="value">{{ $quotation->quotation_date->format('F d, Y') }}</td>
                                    </tr>
                                    @if ($quotation->valid_until)
                                        <tr>
                                            <td class="label">Valid Until:</td>
                                            <td class="value">{{ $quotation->valid_until->format('F d, Y') }}</td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td class="label">Total Amount:</td>
                                        <td class="value" style="font-size: 15px; color: #0f172a;">@money($quotation->total)
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="notice-box">
                    📄 A comprehensive itemized breakdown of this quotation has been securely attached to this email as
                    a PDF document for your evaluation.
                </div>

                <p>Should you have any inquiries, require custom modifications, or wish to approve this proposal, please
                    feel free to reply directly to this email.</p>

                <!-- Signature Block -->
                <div class="signature-section">
                    <p style="margin: 0 0 8px 0; color: #64748b; font-size: 13px;">Warm regards,</p>
                    <div class="signature-name">{{ $quotation->prepared_by ?? config('company.name') }}</div>
                    <div class="signature-company">{{ config('company.name') }}</div>

                    <div class="signature-details">
                        @if (config('company.address'))
                            {{ config('company.address') }}<br>
                        @endif
                        @if (config('company.phone'))
                            Phone: {{ config('company.phone') }}<br>
                        @endif
                        @if (config('company.email'))
                            Email: {{ config('company.email') }}
                        @endif
                    </div>
                </div>

            </div>

            <!-- Footer -->
            <div class="footer">
                This is an automated email from the {{ config('company.name') }} portal.
            </div>

        </div>
    </div>
</body>

</html>
