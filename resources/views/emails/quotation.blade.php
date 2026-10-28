<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            background-color: #f7fafc;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1a202c;
            line-height: 1.6;
            font-size: 14px;
            margin: 0;
            padding: 0;
        }

        .email-wrapper {
            width: 100%;
            background-color: #f7fafc;
            padding: 30px 0;
        }

        .email-content {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
        }

        .footer {
            margin-top: 30px;
            font-size: 11.5px;
            color: #718096;
            border-top: 1px solid #edf2f7;
            padding-top: 15px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="email-wrapper">
        <div class="email-content">
            <p>Dear <strong>{{ $quotation->attention ?? $quotation->quoted_to }}</strong>,</p>

            <p>Thank you for the opportunity to quote. Please find attached the official quotation
                (<strong>{{ $quotation->quotation_number }}</strong>) for your review.</p>

            @if ($quotation->reference_project)
                <p><strong>Reference / Project:</strong> {{ $quotation->reference_project }}</p>
            @endif

            <p>If you have any questions or require modifications, please feel free to reach out to us.</p>

            <p style="margin-top: 25px;">
                Best regards,<br>
                <strong>{{ $quotation->prepared_by ?? config('company.name') }}</strong><br>
                <span style="color: #4a5568; font-weight: 600;">{{ config('company.name') }}</span><br>
                @if (config('company.tagline'))
                    <span
                        style="color: #718096; font-size: 12px; font-style: italic;">{{ config('company.tagline') }}</span><br>
                @endif
                @if (config('company.address'))
                    <span style="color: #4a5568; font-size: 12.5px;">{{ config('company.address') }}</span><br>
                @endif
                @if (config('company.phone'))
                    <span style="color: #4a5568; font-size: 12.5px;">Phone: {{ config('company.phone') }}</span><br>
                @endif
                @if (config('company.email'))
                    <span style="color: #4a5568; font-size: 12.5px;">Email: {{ config('company.email') }}</span>
                @endif
            </p>

            <div class="footer">
                This is an automated message from {{ config('company.name') }} portal.
            </div>
        </div>
    </div>
</body>

</html>
