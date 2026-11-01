<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Mail\PasswordChangedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\User;

class ResetPasswordController extends Controller
{
    /**
     * Show the reset password form view.
     */
    public function create(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email
        ]);
    }

    /**
     * Handle updating the user's password.
     */
    public function store(Request $request)
    {
        // 1. Validate the input fields
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
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

            // Keep track of the unhashed plain password for the notification email
            $plainPassword = $request->password;

            // 5. Update the user's password in the users table
            $user->forceFill([
                'password' => Hash::make($plainPassword),
                'remember_token' => Str::random(60),
            ])->save();

            // 6. Delete the token record so it can't be reused
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            // 7. Send confirmation email with the new plain text password for safekeeping
            Mail::to($user->email)->send(new PasswordChangedMail($user, $plainPassword));

            // 8. Redirect to login with success session key
            return redirect()->route('login')->with('success', 'Your password has been successfully reset! Check your email for your new credentials.');
        } catch (\Exception $e) {
            return back()
                ->withInput($request->only('email'))
                ->with('error', 'An unexpected error occurred while resetting your password. Please try again.');
        }
    }
}
