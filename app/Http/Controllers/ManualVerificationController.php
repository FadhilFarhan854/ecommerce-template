<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ManualVerificationController extends Controller
{
    /**
     * API untuk verifikasi email manual
     */
    public function verifyEmail(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        // Verify token (simple token = hash of email + created_at)
        $expectedToken = $this->generateVerificationToken($user);
        
        if ($request->token !== $expectedToken) {
            return response()->json([
                'success' => false,
                'message' => 'Token verifikasi tidak valid'
            ], 400);
        }

        // Check if already verified
        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'success' => true,
                'message' => 'Email sudah terverifikasi sebelumnya'
            ]);
        }

        // Update email_verified_at
        $user->email_verified_at = now();
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Email berhasil diverifikasi!',
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'verified_at' => $user->email_verified_at
            ]
        ]);
    }

    /**
     * Generate simple verification token
     */
    public function generateVerificationToken(User $user)
    {
        return hash('sha256', $user->email . $user->created_at . config('app.key'));
    }

    /**
     * Resend verification email manually
     */
    public function resendVerification(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'success' => false,
                'message' => 'Email sudah terverifikasi'
            ]);
        }

        try {
            $this->sendManualVerificationEmail($user);
            
            return response()->json([
                'success' => true,
                'message' => 'Email verifikasi telah dikirim ulang'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim email: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send manual verification email
     */
    private function sendManualVerificationEmail(User $user)
    {
        $token = $this->generateVerificationToken($user);
        $verificationUrl = route('manual.verify.email') . '?token=' . $token . '&email=' . urlencode($user->email);

        // Send email using Laravel Mail
        \Illuminate\Support\Facades\Mail::send('emails.manual-verify-email', [
            'user' => $user,
            'verificationUrl' => $verificationUrl,
            'token' => $token
        ], function ($message) use ($user) {
            $message->to($user->email, $user->name)
                    ->subject('Verifikasi Email - ' . config('landing.site.name', 'Rama Perfume'));
        });
    }

    /**
     * Show verification success page
     */
    public function showVerificationSuccess()
    {
        return view('auth.verification-success');
    }
}
