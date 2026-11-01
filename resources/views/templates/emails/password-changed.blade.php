<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Updated - {{ config('company.name') }}</title>
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

        .password-box {
            background-color: #f8fafc;
            border-left: 3px solid #0f3460;
            border-top: 1px solid #e2e8f0;
            border-right: 1px solid #e2e8f0;
            border-bottom: 1px solid #e2e8f0;
            padding: 20px;
            margin: 20px 0;
            border-radius: 0 6px 6px 0;
        }

        .btn-container {
            margin: 25px 0;
            text-align: center;
        }

        .btn {
            background-color: #0f3460;
            color: #ffffff !important;
            text-decoration: none;
            padding: 12px 28px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            display: inline-block;
        }

        .btn:hover {
            background-color: #0c2b4e;
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

            <!-- Header -->
            <div class="email-header">
                <h1>{{ config('company.name') }}</h1>
                <p>Security & Account Management</p>
            </div>

            <!-- Body -->
            <div class="email-body">
                <p>Hello <strong>{{ $user->userInfo?->first_name ?? ($user->name ?? 'User') }}</strong>,</p>

                <p>Your password for <strong>{{ config('company.name') }}</strong> has been successfully updated.</p>
                <p>For your records and safekeeping, your new account credentials are provided below:</p>

                <!-- Password Box -->
                <div class="password-box">
                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                            <td align="center">
                                <span
                                    style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; font-weight: 700; display: block; margin-bottom: 6px;">
                                    Your New Password
                                </span>
                                <span
                                    style="font-family: 'Courier New', Courier, monospace; font-size: 20px; font-weight: bold; color: #0f3460; letter-spacing: 1px;">
                                    {{ $plainPassword }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Action Button -->
                <div class="btn-container">
                    <a href="{{ route('login') }}" class="btn">
                        Sign In Now
                    </a>
                </div>

                <p style="font-size: 12.5px; color: #64748b; margin-top: 20px;">
                    If you did not make this change, please contact your system administrator immediately.
                </p>
            </div>

            <!-- Footer -->
            <div class="footer">
                This is an automated email from {{ config('company.name') }}. All rights reserved.
            </div>

        </div>
    </div>
</body>

</html>
