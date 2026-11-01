<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Mail\ForgotPasswordMail;

class ForgotPasswordController extends Controller
{
    /**
     * Show the forgot password form view.
     */
    public function create()
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle sending the password reset email.
     */
    public function store(Request $request)
    {
        // 1. Validate the incoming email address
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        $user = User::where('email', $request->email)->first();

        // 2. Generate a secure plain token
        $plainToken = Str::random(64);

        // 3. Save the hashed token into your custom table (email, token, created_at)
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => Hash::make($plainToken),
                'created_at' => now(),
            ]
        );

        // 4. Send the custom ForgotPasswordMail
        Mail::to($user->email)->send(new ForgotPasswordMail($user, $plainToken));

        // 5. Redirect back with a success status alert
        return back()->with('status', 'We have emailed your password reset link!');
    }
}
