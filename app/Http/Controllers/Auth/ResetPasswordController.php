<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use App\Mail\PasswordChangedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\User;

class ResetPasswordController extends Controller
{
    public function create(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email
        ]);
    }

    public function store(Request $request)
    {
        // 1. Check Honeypot
        if ($request->filled('website')) {
            return back()->withInput($request->only('email'))->with('error', 'Spam detected.');
        }

        // 2. Validate input fields and Turnstile
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'cf-turnstile-response' => ['required', function ($attribute, $value, $fail) use ($request) {
                $response = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                    'secret' => config('services.turnstile.secret_key'),
                    'response' => $value,
                    'remoteip' => $request->ip(),
                ]);

                if (! $response->json('success')) {
                    $fail('The security captcha verification failed. Please try again.');
                }
            }],
        ]);

        try {
            $record = DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->first();

            if (! $record) {
                return back()->withInput($request->only('email'))
                    ->withErrors(['email' => 'Invalid password reset token.']);
            }

            if (! Hash::check($request->token, $record->token)) {
                return back()->withInput($request->only('email'))
                    ->withErrors(['email' => 'This password reset token is invalid or has expired.']);
            }

            $user = User::where('email', $request->email)->first();
            $plainPassword = $request->password;

            $user->forceFill([
                'password' => Hash::make($plainPassword),
                'remember_token' => Str::random(60),
            ])->save();

            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            Mail::to($user->email)->send(new PasswordChangedMail($user, $plainPassword));

            return redirect()->route('login')->with('success', 'Your password has been successfully reset! Check your email for your new credentials.');
        } catch (\Exception $e) {
            return back()
                ->withInput($request->only('email'))
                ->with('error', 'An unexpected error occurred while resetting your password. Please try again.');
        }
    }
}
