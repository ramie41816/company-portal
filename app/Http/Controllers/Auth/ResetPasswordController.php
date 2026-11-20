<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordChangedMail;
use App\Models\User;
use App\Rules\ValidTurnstileToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    public function create(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    public function store(Request $request)
    {
        if ($request->filled('website')) {
            return back()->withInput($request->only('email'))->with('error', 'Spam detected.');
        }

        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'cf-turnstile-response' => [new ValidTurnstileToken()],
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
