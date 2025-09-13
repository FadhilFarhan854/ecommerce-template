<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Http\Controllers\ManualVerificationController;

class TestManualVerification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:manual-verification {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test manual email verification system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email') ?? $this->ask('Enter email to test');
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Invalid email format!');
            return 1;
        }

        $this->info("🧪 Testing Manual Verification System for: $email");
        $this->newLine();

        // Step 1: Create test user
        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => 'Test User',
                'password' => bcrypt('password123'),
                'role' => 'customer',
                'email_verified_at' => null
            ]
        );

        $this->info("👤 User: {$user->name} (ID: {$user->id})");
        $this->info("📧 Email: {$user->email}");
        $this->info("✅ Verified: " . ($user->hasVerifiedEmail() ? 'Yes' : 'No'));
        $this->newLine();

        // Step 2: Generate verification token & URL
        $controller = new ManualVerificationController();
        $token = $controller->generateVerificationToken($user);
        $verificationUrl = route('manual.verify.email') . '?token=' . $token . '&email=' . urlencode($user->email);

        $this->info("🔑 Generated Token: " . substr($token, 0, 16) . '...');
        $this->info("🔗 Verification URL:");
        $this->line($verificationUrl);
        $this->newLine();

        // Step 3: Try to send email
        if ($this->confirm('Send verification email?', true)) {
            try {
                \Illuminate\Support\Facades\Mail::send('emails.manual-verify-email', [
                    'user' => $user,
                    'verificationUrl' => $verificationUrl,
                    'token' => $token
                ], function ($message) use ($user) {
                    $message->to($user->email, $user->name)
                            ->subject('Verifikasi Email - ' . config('landing.site.name', 'Rama Perfume'));
                });
                
                $this->info("✅ Verification email sent successfully!");
                $this->info("📬 Check your email inbox");
            } catch (\Exception $e) {
                $this->error("❌ Failed to send email: " . $e->getMessage());
            }
        }

        $this->newLine();
        $this->info("🔧 Manual Test Options:");
        $this->line("1. Open URL in browser to verify");
        $this->line("2. Use API: GET " . route('manual.verify.email') . "?token={$token}&email=" . urlencode($email));
        $this->line("3. Check verification status: User ID {$user->id}");

        return 0;
    }
}
