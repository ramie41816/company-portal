@php
    if (isset($template_viewing) && $template_viewing):
        // Self-contained fallback defaults so this template handles empty data gracefully
        $isReset = $isReset ?? false;
        $user =
            $user ??
            (object) [
                'name' => 'Sample User',
                'email' => 'sample@example.com',
                'userInfo' => (object) ['first_name' => 'Sample'],
            ];
        $tempPassword = $tempPassword ?? 'TempPass123!';
    endif;
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $isReset ? 'Password Reset' : 'Welcome' }} - {{ config('company.name', 'Company Portal') }}</title>
    <style>
        body {
            background-color: #f8fafc;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #334155;
            line-height: 1.6;
            font-size: 14px;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
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

        /* Clean Minimal Header matching quotation brand accent */
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

        /* Password / Summary Box matching quotation box */
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

        /* Centered Button styling */
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

            {{-- Header --}}
            <div class="email-header">
                <h1>{{ config('company.name', 'Company Portal') }}</h1>
                @if (config('company.tagline'))
                    <p>{{ config('company.tagline') }}</p>
                @else
                    <p>{{ $isReset ? 'Account Security' : 'Welcome Portal' }}</p>
                @endif
            </div>

            {{-- Content Body --}}
            <div class="email-body">
                <p>Hello <strong>{{ $user->userInfo?->first_name ?? ($user->name ?? 'User') }}</strong>,</p>

                @if ($isReset)
                    <p>
                        An administrator has issued a password reset for your account on
                        <strong>{{ config('company.name', 'Company Portal') }}</strong>.
                    </p>
                    <p>
                        Your previous password has been invalidated. Please use the temporary password below to sign in
                        and update your security credentials in <strong>My Profile</strong> immediately.
                    </p>
                @else
                    <p>
                        Welcome to <strong>{{ config('company.name', 'Company Portal') }}</strong>! Your user account
                        has been
                        successfully created by an administrator.
                    </p>
                    <p>
                        You can now log in to the portal using your email address
                        (<strong>{{ $user->email ?? 'sample@example.com' }}</strong>)
                        and the temporary password provided below.
                    </p>
                @endif

                {{-- Password Box --}}
                <div class="password-box">
                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                            <td align="center">
                                <span
                                    style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; font-weight: 700; display: block; margin-bottom: 6px;">
                                    Your Temporary Password
                                </span>
                                <span
                                    style="font-family: 'Courier New', Courier, monospace; font-size: 22px; font-weight: bold; color: #0f3460; letter-spacing: 2px;">
                                    {{ $tempPassword }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>

                {{-- Action Button --}}
                <div class="btn-container">
                    <a href="{{ Route::has('login') ? route('login') : '#' }}" class="btn">
                        Sign In to Portal
                    </a>
                </div>
            </div>

            {{-- Footer --}}
            <div class="footer">
                <p style="margin: 0 0 6px 0;">If you did not request or expect this account update, please contact your
                    system administrator immediately.</p>
                <p style="margin: 0;">&copy; {{ date('Y') }} {{ config('company.name', 'Company Portal') }}. All
                    rights reserved.
                </p>
            </div>

        </div>
    </div>
</body>

</html>
