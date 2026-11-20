@php
    if (isset($template_viewing) && $template_viewing):
        $inquiry =
            $inquiry ??
            (object) [
                'reference_id' => 'INQ-20261121-ABC12',
                'name' => 'Valued Client',
                'service_type' => 'Sample Service',
            ];
        $executedBy = $executedBy ?? 'Customer Support Team';
        $generalRemarks = $generalRemarks ?? null;
    endif;
@endphp

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inquiry Confirmation - {{ $inquiry->reference_id ?? 'SAMPLE' }}</title>
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

        .email-body {
            padding: 30px;
        }

        .email-body p {
            margin-top: 0;
            margin-bottom: 16px;
            color: #475569;
        }

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

        .remarks-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 12px 16px;
            border-radius: 6px;
            font-size: 13px;
            margin: 15px 0;
            color: #334155;
        }

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

        .footer {
            background-color: #f8fafc;
            font-size: 11.5px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding: 20px 30px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="email-wrapper">
        <div class="email-content">
            <div class="email-header">
                <h1>{{ config('company.app_name', 'Company Portal') }}</h1>
                <p>{{ config('company.tagline', 'Your Tagline') }}</p>
            </div>

            <div class="email-body">
                <p>Dear <strong>{{ $inquiry->name ?? 'Valued Client' }}</strong>,</p>

                <p>Thank you for reaching out to us! We have successfully received your inquiry and our team is
                    currently reviewing your request.</p>

                <div class="project-box">
                    <table>
                        <tr>
                            <td class="label">Inquiry Ref:</td>
                            <td class="value" style="color: #0f3460;">{{ $inquiry->reference_id ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Service Requested:</td>
                            <td class="value">{{ $inquiry->service_type ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Handled By:</td>
                            <td class="value">{{ $executedBy ?? 'Customer Support Team' }}</td>
                        </tr>
                    </table>
                </div>

                <p>We will get back to you shortly via phone or email using the contact details you provided.</p>

                @if (!empty($generalRemarks))
                    <div class="remarks-box">
                        <strong>Your Notes / Message:</strong><br>
                        <em>{{ $generalRemarks }}</em>
                    </div>
                @endif

                <p>We appreciate your interest in our services!</p>

                <div class="signature-section">
                    <p style="margin: 0 0 4px 0;">Best regards,</p>
                    <div class="signature-name">{{ $executedBy ?? 'Customer Support Team' }}</div>
                    <div>{{ config('company.name', 'Company Portal') }}</div>
                    @if (config('company.phone'))
                        <div>Phone: {{ config('company.phone') }}</div>
                    @endif
                </div>
            </div>

            <div class="footer">
                This is an automated confirmation email from {{ config('company.app_name', 'Company Portal') }}.
            </div>
        </div>
    </div>
</body>

</html>
