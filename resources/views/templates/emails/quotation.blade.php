@php
    if (isset($template_viewing) && $template_viewing):
        // Self-contained fallback defaults so this template handles empty data gracefully
        $quotation =
            $quotation ??
            (object) [
                'quotation_number' => 'QT-SAMPLE-000',
                'quoted_to' => 'Valued Client',
                'attention' => 'Jane Doe',
                'reference_project' => 'Sample Project Implementation',
                'total' => 150000.0,
                'valid_until' => now()->addDays(30),
                'prepared_by' => 'System Administrator',
            ];
    endif;
@endphp

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quotation {{ $quotation->quotation_number ?? 'SAMPLE' }}</title>
    <style>
        body {
            background-color: #f8fafc;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #334155;
            line-height: 1.6;
            font-size: 14px;
            margin: 0;
            padding: 0;
        }

        .email-wrapper {
            width: 100%;
            background-color: #f8fafc;
            padding: 30px 0;
        }

        .email-content {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }

        /* Clean Minimal Header matching brand accent */
        .email-header {
            background-color: #0f3460;
            padding: 24px 30px;
            text-align: left;
        }

        .email-header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 18px;
            font-weight: 600;
        }

        .email-header p {
            color: #4ade80;
            margin: 4px 0 0 0;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 700;
        }

        /* Body Area */
        .email-body {
            padding: 30px;
        }

        .email-body p {
            margin-top: 0;
            margin-bottom: 16px;
            color: #475569;
        }

        /* Clean Summary Box */
        .quote-box {
            background-color: #f8fafc;
            border-left: 3px solid #0f3460;
            padding: 16px 20px;
            margin: 20px 0;
            font-size: 13.5px;
            border-radius: 0 6px 6px 0;
        }

        .quote-box table {
            width: 100%;
            border-collapse: collapse;
        }

        .quote-box td {
            padding: 5px 0;
        }

        .quote-box td.label {
            color: #64748b;
            width: 38%;
        }

        .quote-box td.value {
            color: #0f172a;
            font-weight: 600;
        }

        /* Signature Block */
        .signature-section {
            margin-top: 25px;
            border-top: 1px solid #f1f5f9;
            padding-top: 20px;
            font-size: 13px;
            color: #64748b;
        }

        .signature-name {
            font-weight: 600;
            color: #0f172a;
            font-size: 14px;
        }

        /* Footer */
        .footer {
            background-color: #f8fafc;
            font-size: 11.5px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding: 20px 30px;
            text-align: center;
        }

        @media screen and (max-width: 600px) {
            .email-content {
                width: 100% !important;
                border-radius: 0 !important;
                border: none !important;
            }

            .email-header,
            .email-body,
            .footer {
                padding: 20px !important;
            }
        }
    </style>
</head>

<body>
    <div class="email-wrapper">
        <div class="email-content">

            <!-- Header -->
            <div class="email-header">
                <h1>{{ config('company.app_name', 'Company Portal') }}</h1>
                <p>{{ config('company.tagline', 'Your Tagline') }}</p>
            </div>

            <!-- Body -->
            <div class="email-body">
                <p>Dear <strong>{{ $quotation->attention ?? ($quotation->quoted_to ?? 'Valued Client') }}</strong>,</p>

                <p>Thank you for the opportunity. Please find attached our official quotation summary for your review
                    and evaluation.</p>

                <!-- Concise Summary Box -->
                <div class="quote-box">
                    <table>
                        <tr>
                            <td class="label">Quotation No:</td>
                            <td class="value" style="color: #0f3460;">{{ $quotation->quotation_number ?? '—' }}</td>
                        </tr>
                        @if (!empty($quotation->reference_project))
                            <tr>
                                <td class="label">Project Title:</td>
                                <td class="value">{{ $quotation->reference_project }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td class="label">Total Amount:</td>
                            <td class="value" style="color: #166534;">
                                @if (isset($quotation->total))
                                    @money($quotation->total)
                                @else
                                    $0.00
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="label">Valid Until:</td>
                            <td class="value">
                                @if (isset($quotation->valid_until) && $quotation->valid_until)
                                    {{ \Carbon\Carbon::parse($quotation->valid_until)->format('F d, Y') }}
                                @else
                                    30 days from issue
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>

                <p>Should you have any inquiries or wish to move forward with this proposal, feel free to reply directly
                    to this email.</p>

                <!-- Signature -->
                <div class="signature-section">
                    <p style="margin: 0 0 4px 0;">Best regards,</p>
                    <div class="signature-name">
                        {{ $quotation->prepared_by ?? config('company.app_name', 'Company Portal') }}</div>
                    <div>{{ config('company.app_name', 'Company Portal') }}</div>
                    @if (config('company.phone'))
                        <div>Phone: {{ config('company.phone') }}</div>
                    @endif
                </div>
            </div>

            <!-- Footer -->
            <div class="footer">
                This is an automated email from {{ config('company.app_name', 'Company Portal') }}.
            </div>

        </div>
    </div>
</body>

</html>
