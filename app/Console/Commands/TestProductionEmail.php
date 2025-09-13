<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

class TestProductionEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test-production {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test production email sending (verification & forgot password)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Invalid email format!');
            return 1;
        }

        $this->info("📧 Testing PRODUCTION email for: {$email}");
        $this->info("📤 Email Provider: " . config('mail.default'));
        $this->info("🔧 SMTP Host: " . config('mail.mailers.smtp.host'));
        $this->newLine();

        // Check if using log driver
        if (config('mail.default') === 'log') {
            $this->warn('⚠️  Currently using LOG driver - emails won\'t be sent to real inbox');
            $this->info('💡 Use: php artisan email:switch gmail');
            $this->newLine();
        }

        // Test 1: Simple test email
        $this->testSimpleEmail($email);
        $this->newLine();

        // Test 2: Email verification
        $this->testEmailVerification($email);
        $this->newLine();

        // Test 3: Forgot password
        $this->testForgotPassword($email);
        $this->newLine();

        $this->info('🎉 All production email tests completed!');
        
        if (config('mail.default') !== 'log') {
            $this->info("📬 Check your real email inbox: {$email}");
        }

        return 0;
    }

    private function testSimpleEmail($email)
    {
        $this->info('1️⃣ Testing Simple Email...');
        
        try {
            Mail::send('emails.test-production', [
                'email' => $email,
                'timestamp' => now()->format('Y-m-d H:i:s')
            ], function ($message) use ($email) {
                $message->to($email)
                        ->subject('Test Production Email - ' . config('app.name'));
            });
            
            $this->info('✅ Simple email sent successfully');
        } catch (\Exception $e) {
            $this->error('❌ Failed to send simple email: ' . $e->getMessage());
        }
    }

    private function testEmailVerification($email)
    {
        $this->info('2️⃣ Testing Email Verification...');
        
        try {
            // Create or find test user
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => 'Production Test User',
                    'password' => bcrypt('test123'),
                    'role' => 'customer',
                    'email_verified_at' => null
                ]
            );

            // Generate verification URL
            $token = hash('sha256', $user->email . $user->created_at . config('app.key'));
            $verificationUrl = route('manual.verify.email') . '?token=' . $token . '&email=' . urlencode($user->email);

            Mail::send('emails.manual-verify-email', [
                'user' => $user,
                'verificationUrl' => $verificationUrl,
                'token' => $token
            ], function ($message) use ($user) {
                $message->to($user->email, $user->name)
                        ->subject('Verifikasi Email - ' . config('landing.site.name', 'Rama Perfume'));
            });
            
            $this->info('✅ Email verification sent successfully');
            $this->info("   User: {$user->name} (ID: {$user->id})");
        } catch (\Exception $e) {
            $this->error('❌ Failed to send verification: ' . $e->getMessage());
        }
    }

    private function testForgotPassword($email)
    {
        $this->info('3️⃣ Testing Forgot Password...');
        
        try {
            $user = User::where('email', $email)->first();
            
            if (!$user) {
                $user = User::create([
                    'name' => 'Production Test User',
                    'email' => $email,
                    'password' => bcrypt('test123'),
                    'role' => 'customer'
                ]);
            }

            $token = hash('sha256', $user->email . $user->password . now()->timestamp . config('app.key'));

            Mail::send('emails.manual-reset-password', [
                'user' => $user,
                'token' => $token
            ], function ($message) use ($user) {
                $message->to($user->email, $user->name)
                        ->subject('Reset Password - ' . config('landing.site.name', 'Rama Perfume'));
            });
            
            $this->info('✅ Forgot password email sent successfully');
        } catch (\Exception $e) {
            $this->error('❌ Failed to send forgot password: ' . $e->getMessage());
        }
    }
}
