<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $isReset ? 'Password Reset' : 'Welcome' }}</title>
</head>

<body
    style="margin: 0; padding: 0; background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; -webkit-font-smoothing: antialiased;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f4f6f9; padding: 40px 0;">
        <tr>
            <td align="center">
                <table border="0" cellpadding="0" cellspacing="0" width="100%"
                    style="max-width: 600px; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);">

                    {{-- Header --}}
                    <tr>
                        <td style="background-color: #2b3a67; padding: 30px; text-align: center;">
                            @if (config('company.logo'))
                                <img src="{{ asset(config('company.logo')) }}" alt="{{ config('company.name') }}"
                                    height="40" style="margin-bottom: 10px;">
                            @endif
                            <h1
                                style="color: #ffffff; margin: 0; font-size: 22px; font-weight: 600; font-family: Georgia, serif;">
                                {{ config('company.name') }}
                            </h1>
                        </td>
                    </tr>

                    {{-- Content Body --}}
                    <tr>
                        <td style="padding: 40px 30px; color: #333333; line-height: 1.6;">
                            <h2 style="margin-top: 0; color: #2b3a67; font-size: 20px; font-weight: 600;">
                                Hello, {{ $user->userInfo?->first_name ?? 'User' }}!
                            </h2>

                            @if ($isReset)
                                <p style="margin-bottom: 16px; font-size: 15px; color: #4a5568;">
                                    An administrator has issued a password reset for your account on
                                    <strong>{{ config('company.name') }}</strong>.
                                </p>
                                <p style="margin-bottom: 24px; font-size: 15px; color: #4a5568;">
                                    Your previous password has been invalidated. Please use the temporary password below
                                    to sign in and update your security credentials in <strong>My Profile</strong>
                                    immediately.
                                </p>
                            @else
                                <p style="margin-bottom: 16px; font-size: 15px; color: #4a5568;">
                                    Welcome to <strong>{{ config('company.name') }}</strong>! Your user account has been
                                    successfully created by an administrator.
                                </p>
                                <p style="margin-bottom: 24px; font-size: 15px; color: #4a5568;">
                                    You can now log in to the portal using your email address
                                    (<strong>{{ $user->email }}</strong>) and the temporary password provided below.
                                </p>
                            @endif

                            {{-- Password Box --}}
                            <table border="0" cellpadding="0" cellspacing="0" width="100%"
                                style="background-color: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 8px; margin: 28px 0;">
                                <tr>
                                    <td align="center" style="padding: 20px;">
                                        <span
                                            style="font-size: 12px; text-transform: uppercase; tracking: 1px; color: #64748b; font-weight: 600; display: block; margin-bottom: 6px;">
                                            Your Temporary Password
                                        </span>
                                        <span
                                            style="font-family: 'Courier New', Courier, monospace; font-size: 24px; font-weight: bold; color: #2b3a67; letter-spacing: 3px;">
                                            {{ $tempPassword }}
                                        </span>
                                    </td>
                                </tr>
                            </table>

                            {{-- Action Button --}}
                            <table border="0" cellpadding="0" cellspacing="0" width="100%"
                                style="margin-top: 32px;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ route('login') }}"
                                            style="background-color: #2b3a67; color: #ffffff; text-decoration: none; padding: 12px 28px; border-radius: 6px; font-size: 15px; font-weight: 600; display: inline-block;">
                                            Sign In to Portal
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td
                            style="background-color: #f8fafc; padding: 20px 30px; text-align: center; border-top: 1px solid #e2e8f0; font-size: 12px; color: #94a3b8;">
                            <p style="margin: 0 0 6px 0;">If you did not request or expect this account update, please
                                contact your system administrator immediately.</p>
                            <p style="margin: 0;">&copy; {{ date('Y') }} {{ config('company.name') }}. All rights
                                reserved.</p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>

</html>
