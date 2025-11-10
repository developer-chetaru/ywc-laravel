<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use App\Mail\ActivationSuccessMail;
use App\Mail\VerifyUserMail;
use App\Models\User;

class VerificationController extends Controller
{
    public function sendVerificationEmail($id)
    {
        $user = User::findOrFail($id);

        // Create a signed URL valid for 24 hours
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addHours(24),
            ['id' => $user->id]
        );

        Mail::to($user->email)->send(new VerifyUserMail($user, $verificationUrl));

        return response()->json(['message' => 'Verification email sent.']);
    }

    public function verify(Request $request, $id)
    {
        if (! $request->hasValidSignature()) {
            return response("
                <html>
                <head>
                    <title>Link Expired</title>
                </head>
                <body style='text-align:center; padding:50px; font-family:sans-serif;'>
                    <h2 style='color:red;'>❌ Link expired or invalid.</h2>
                    <p>Please request a new activation link.</p>
                </body>
                </html>
            ", 403);
        }

        $user = User::findOrFail($id);

        if (! $user->is_active) {
            $user->is_active = true;
            $user->save();

            // Optional: send success email
            Mail::to($user->email)->send(new ActivationSuccessMail($user));

            return response("
                <html>
                <head>
                    <title>Account Activated</title>
                    <script>
                        // Redirect to login page after 2 seconds
                        setTimeout(function() {
                            window.location.href = '".route('login')."';
                        }, 2000);
                    </script>
                </head>
                <body style='text-align:center; padding:50px; font-family:sans-serif;'>
                    <h2 style='color:blue;'>✅ Your account has been activated successfully!</h2>
                    <p>You can now close this window and login.</p>
                </body>
                </html>
            ");
        }

        return response("
            <html>
            <head><title>Already Activated</title></head>
            <body style='text-align:center; padding:50px; font-family:sans-serif;'>
                <h2 style='color:orange;'>⚠️ This account is already activated.</h2>
                <p>You do not need to take any further action.</p>
            </body>
            </html>
        ");
    }

}
