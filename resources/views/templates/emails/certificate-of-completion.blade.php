@php
    // Self-contained fallback defaults so this template handles empty data gracefully
    $quotation =
        $quotation ??
        (object) [
            'quotation_number' => 'QT-SAMPLE-000',
            'quoted_to' => 'Valued Client',
            'reference_project' => 'Sample Project Name',
        ];
    $executedBy = $executedBy ?? 'System Administrator';
    $generalRemarks = $generalRemarks ?? null;
@endphp

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate of Completion - {{ $quotation->quotation_number ?? 'SAMPLE' }}</title>
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
        .project-box {
            background-color: #f8fafc;
            border-left: 3px solid #0f3460;
            padding: 16px 20px;
            margin: 20px 0;
            font-size: 13.5px;
            border-radius: 0 6px 6px 0;
        }

        .project-box table {
            width: 100%;
            border-collapse: collapse;
        }

        .project-box td {
            padding: 5px 0;
        }

        .project-box td.label {
            color: #64748b;
            width: 38%;
        }

        .project-box td.value {
            color: #0f172a;
            font-weight: 600;
        }

        /* Remarks Box */
        .remarks-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 12px 16px;
            border-radius: 6px;
            font-size: 13px;
            margin: 15px 0;
            color: #334155;
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
                <h1>Certificate of Completion</h1>
                <p>{{ config('company.name', 'Company Portal') }}</p>
            </div>

            <!-- Body -->
            <div class="email-body">
                <p>Dear <strong>{{ $quotation->quoted_to ?? 'Valued Client' }}</strong>,</p>

                <p>We are pleased to inform you that the project services associated with your quotation have been
                    successfully executed and completed.</p>

                <!-- Concise Project Details Box -->
                <div class="project-box">
                    <table>
                        <tr>
                            <td class="label">Quotation No:</td>
                            <td class="value" style="color: #0f3460;">{{ $quotation->quotation_number ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Project / Ref:</td>
                            <td class="value">{{ $quotation->reference_project ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Executed By:</td>
                            <td class="value">{{ $executedBy ?? 'System Administrator' }}</td>
                        </tr>
                    </table>
                </div>

                <p>Please find attached your official <strong>Certificate of Completion</strong> PDF document for your
                    records and review.</p>

                @if (!empty($generalRemarks))
                    <div class="remarks-box">
                        <strong>General Notes / Observations:</strong><br>
                        <em>{{ $generalRemarks }}</em>
                    </div>
                @endif

                <p>Thank you for your trust and business!</p>

                <!-- Signature -->
                <div class="signature-section">
                    <p style="margin: 0 0 4px 0;">Best regards,</p>
                    <div class="signature-name">{{ $executedBy ?? 'System Administrator' }}</div>
                    <div>{{ config('company.name', 'Company Portal') }}</div>
                    @if (config('company.phone'))
                        <div>Phone: {{ config('company.phone') }}</div>
                    @endif
                </div>
            </div>

            <!-- Footer -->
            <div class="footer">
                This is an automated email from {{ config('company.name', 'Company Portal') }}.
            </div>

        </div>
    </div>
</body>

</html>
