<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => $request->role ?? 'customer',
            'email_verification_token' => Str::random(64),
        ]);

        // Send verification email
        $this->sendVerificationEmail($user);

        return response()->json([
            'success' => true,
            'message' => 'Email verifikasi telah dikirim! Periksa inbox atau folder spam Anda.',
            'data' => [
                'user' => $user->makeHidden(['email_verification_token']),
                'verification_sent' => true,
                'email' => $user->email
            ]
        ], 201);
    }

    /**
     * Login user
     */
    public function login(LoginRequest $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer',
            ]
        ]);
    }

    /**
     * Get authenticated user profile
     */
    public function profile(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'user' => $request->user()
            ]
        ]);
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'sometimes|nullable|string|max:20',
            'current_password' => 'required_with:password|string',
            'password' => 'sometimes|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check current password if updating password
        if ($request->has('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect'
                ], 422);
            }
        }

        // Update user data
        $updateData = $request->only(['name', 'email', 'phone']);
        
        if ($request->has('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => [
                'user' => $user->fresh()
            ]
        ]);
    }

    /**
     * Change password
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect'
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully'
        ]);
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }

    /**
     * Logout from all devices
     */
    public function logoutAll(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out from all devices successfully'
        ]);
    }

    /**
     * Web Login
     */
    public function webLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();
            if($user->role === 'admin') {
                return redirect()->intended(route('admin.dashboard'));
            }
            return redirect()->intended(route('home'));
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    /**
     * Web Register
     */
    public function webRegister(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'customer',
            'email_verification_token' => Str::random(64),
        ]);

        // Send verification email
        $this->sendVerificationEmail($user);

        // Return JSON for AJAX request
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Email verifikasi telah dikirim! Periksa inbox atau folder spam Anda.',
                'data' => [
                    'user' => $user->makeHidden(['email_verification_token']),
                    'verification_sent' => true,
                    'email' => $user->email
                ]
            ], 201);
        }

        // Traditional form redirect
        return redirect('/login')->with('success', 'Akun berhasil dibuat! Email verifikasi telah dikirim. Periksa inbox atau folder spam Anda.');
    }

    /**
     * Send manual verification email
     */
    private function sendManualVerificationEmail($user)
    {
        $token = $this->generateVerificationToken($user);
        $verificationUrl = route('manual.verify.email') . '?token=' . $token . '&email=' . urlencode($user->email);

        try {
            \Illuminate\Support\Facades\Mail::send('emails.manual-verify-email', [
                'user' => $user,
                'verificationUrl' => $verificationUrl,
                'token' => $token
            ], function ($message) use ($user) {
                $message->to($user->email, $user->name)
                        ->subject('Verifikasi Email - ' . config('landing.site.name', 'Rama Perfume'));
            });
        } catch (\Exception $e) {
            \Log::error('Failed to send verification email: ' . $e->getMessage());
        }
    }

    /**
     * Generate simple verification token
     */
    private function generateVerificationToken($user)
    {
        return hash('sha256', $user->email . $user->created_at . config('app.key'));
    }

    /**
     * Web Logout
     */
    public function webLogout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }

    // ============ SIMPLE FORGOT PASSWORD (Manual) ============

    /**
     * Show forgot password form
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password-simple');
    }

    /**
     * Send simple reset instructions
     */
    public function sendResetInstructions(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Email tidak ditemukan dalam sistem kami.']);
        }

        // Generate simple reset token
        $token = $this->generatePasswordResetToken($user);
        $resetUrl = route('manual.reset.password') . '?token=' . $token . '&email=' . urlencode($user->email);

        try {
            \Illuminate\Support\Facades\Mail::send('emails.manual-reset-password', [
                'user' => $user,
                'resetUrl' => $resetUrl,
                'token' => $token
            ], function ($message) use ($user) {
                $message->to($user->email, $user->name)
                        ->subject('Reset Password - ' . config('landing.site.name', 'Rama Perfume'));
            });

            return back()->with('status', 'Instruksi reset password telah dikirim ke email Anda!');
        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'Gagal mengirim email reset password.']);
        }
    }

    /**
     * Generate simple password reset token
     */
    private function generatePasswordResetToken($user)
    {
        return hash('sha256', $user->email . $user->password . now()->timestamp . config('app.key'));
    }

    /**
     * Send verification email
     */
    private function sendVerificationEmail($user)
    {
        $verificationUrl = route('verify.email', [
            'token' => $user->email_verification_token,
            'email' => $user->email
        ]);

        try {
            Mail::send(['html' => 'emails.verify-email', 'text' => 'emails.verify-email-text'], [
                'user' => $user,
                'verificationUrl' => $verificationUrl
            ], function ($message) use ($user) {
                $message->to($user->email, $user->name)
                        ->subject('Verifikasi Email - ' . config('landing.site.name', 'Rama Perfume'))
                        ->replyTo(config('mail.from.address'), config('landing.site.name', 'Rama Perfume'));
            });
        } catch (\Exception $e) {
            \Log::error('Failed to send verification email: ' . $e->getMessage());
        }
    }

    /**
     * Verify email with token
     */
    public function verifyEmail(Request $request)
    {
        $token = $request->get('token');
        $email = $request->get('email');

        if (!$token || !$email) {
            return redirect('/login')->with('error', 'Link verifikasi tidak valid.');
        }

        $user = User::where('email', $email)
                   ->where('email_verification_token', $token)
                   ->first();

        if (!$user) {
            return redirect('/login')->with('error', 'Link verifikasi tidak valid atau sudah expired.');
        }

        if ($user->email_verified_at) {
            return redirect('/login')->with('success', 'Email sudah terverifikasi sebelumnya. Silakan login.');
        }

        // Update verified_at
        $user->update([
            'email_verified_at' => now(),
            'email_verification_token' => null
        ]);

        return redirect('/login')->with('success', 'Email berhasil diverifikasi! Silakan login dengan akun Anda.');
    }

    /**
     * Resend verification email
     */
    public function resendVerification(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Email tidak ditemukan.'
            ], 404);
        }

        if ($user->email_verified_at) {
            return response()->json([
                'success' => false,
                'message' => 'Email sudah terverifikasi.'
            ], 400);
        }

        // Generate new token
        $user->update(['email_verification_token' => Str::random(64)]);
        
        // Send email
        $this->sendVerificationEmail($user);

        return response()->json([
            'success' => true,
            'message' => 'Email verifikasi telah dikirim ulang! Periksa inbox atau folder spam Anda.'
        ]);
    }
}
