<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ForgotPasswordMail;
use App\Models\User;
use App\Rules\ValidTurnstileToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    public function create()
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request)
    {
        if ($request->filled('website')) {
            return back()->withInput($request->only('email'))->with('error', 'Spam detected.');
        }

        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'cf-turnstile-response' => [new ValidTurnstileToken()],
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
