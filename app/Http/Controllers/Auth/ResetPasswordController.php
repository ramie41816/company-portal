<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
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
            'password' => ['required', 'string', 'min:8', 'confirmed'], // expects password_confirmation field
        ]);

        // 2. Retrieve the record from your custom table
        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        // 3. Check if token exists
        if (! $record) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'Invalid password reset token.']);
        }

        // 4. Verify the token matches the hashed token in your table
        if (! Hash::check($request->token, $record->token)) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'This password reset token is invalid or has expired.']);
        }

        // 5. Update the user's password in the users table
        $user = User::where('email', $request->email)->first();

        $user->forceFill([
            'password' => Hash::make($request->password),
            'remember_token' => Str::random(60),
        ])->save();

        // 6. Delete the token record so it can't be reused
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        // 7. Redirect to login with success status
        return redirect()->route('login')->with('status', 'Your password has been successfully reset!');
    }
}
