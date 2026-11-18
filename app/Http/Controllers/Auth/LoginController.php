<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class LoginController extends Controller
{
    public function show()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // 1. Check Honeypot
        if ($request->filled('website')) {
            return back()->withErrors(['email' => 'Spam detected.'])->onlyInput('email');
        }

        // 2. Validate standard inputs & Turnstile response
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
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

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('home'));
        }

        return back()
            ->withErrors(['email' => 'These credentials do not match our records.'])
            ->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
