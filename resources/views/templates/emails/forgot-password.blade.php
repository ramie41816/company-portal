@php
    if (isset($template_viewing) && $template_viewing):
        // Self-contained fallback defaults so this template handles empty data gracefully
        $user =
            $user ??
            (object) [
                'name' => 'Sample User',
                'email' => 'sample@example.com',
                'userInfo' => (object) ['first_name' => 'Sample'],
            ];
        $token = $token ?? 'sample-reset-token-placeholder';

        // Safely generate reset URL if the route exists
        $resetUrl =
            Route::has('password.reset') && isset($token)
                ? route('password.reset', ['token' => $token, 'email' => $user->email ?? 'sample@example.com'])
                : '#';
    endif;
@endphp

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset - {{ config('company.name', 'Company Portal') }}</title>
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

        /* Clean Summary Box / Callout Box */
        .info-box {
            background-color: #f8fafc;
            border-left: 3px solid #0f3460;
            padding: 16px 20px;
            margin: 20px 0;
            font-size: 13.5px;
            border-radius: 0 6px 6px 0;
            word-break: break-all;
        }

        /* Centered Button styling */
        .btn-container {
            margin: 30px 0;
            text-align: center;
        }

        .btn {
            background-color: #0f3460;
            color: #ffffff !important;
            text-decoration: none;
            padding: 12px 32px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            display: inline-block;
        }

        .btn:hover {
            background-color: #0c2b4e;
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
                <h1>{{ config('company.name', 'Company Portal') }}</h1>
                @if (config('company.tagline'))
                    <p>{{ config('company.tagline') }}</p>
                @else
                    <p>Security & Account Management</p>
                @endif
            </div>

            <!-- Body -->
            <div class="email-body">
                <p>Hello <strong>{{ $user->userInfo?->first_name ?? ($user->name ?? 'User') }}</strong>,</p>

                <p>You are receiving this email because we received a password reset request for your account.</p>

                <!-- Centered Action Button -->
                <div class="btn-container">
                    <a href="{{ $resetUrl }}" class="btn">
                        Reset Password
                    </a>
                </div>

                <p>If you did not request a password reset, no further action is required.</p>

                <!-- Fallback Link Box -->
                <div class="info-box">
                    <span
                        style="display: block; color: #64748b; font-size: 12px; margin-bottom: 4px; font-weight: 600;">
                        Having trouble clicking the button? Copy and paste this URL into your browser:
                    </span>
                    <span style="color: #0f3460; font-size: 12px;">{{ $resetUrl }}</span>
                </div>
            </div>

            <!-- Footer -->
            <div class="footer">
                This is an automated email from {{ config('company.name', 'Company Portal') }}. All rights reserved.
            </div>

        </div>
    </div>
</body>

</html>
