<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use App\Mail\ForgotPasswordMail;

class ForgotPasswordController extends Controller
{
    public function create()
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request)
    {
        // 1. Check Honeypot
        if ($request->filled('website')) {
            return back()->withInput($request->only('email'))->with('error', 'Spam detected.');
        }

        // 2. Validate email and Turnstile
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
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
            $user = User::where('email', $request->email)->first();
            $plainToken = Str::random(64);

            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $request->email],
                [
                    'token' => Hash::make($plainToken),
                    'created_at' => now(),
                ]
            );

            Mail::to($user->email)->send(new ForgotPasswordMail($user, $plainToken));

            return redirect()->route('login')->with('success', 'We have emailed your password reset link!');
        } catch (\Exception $e) {
            return back()
                ->withInput($request->only('email'))
                ->with('error', 'Failed to send password reset email. Please try again later.');
        }
    }
}
