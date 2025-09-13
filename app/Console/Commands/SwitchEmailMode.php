<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SwitchEmailMode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:switch {mode : development, production, gmail}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Switch email configuration between development and production modes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $mode = $this->argument('mode');
        $envPath = base_path('.env');

        if (!File::exists($envPath)) {
            $this->error('.env file not found!');
            return 1;
        }

        $envContent = File::get($envPath);

        switch (strtolower($mode)) {
            case 'development':
            case 'dev':
            case 'log':
                $this->switchToDevelopment($envContent, $envPath);
                break;

            case 'production':
            case 'prod':
            case 'gmail':
                $this->switchToGmail($envContent, $envPath);
                break;

            case 'sendgrid':
                $this->switchToSendGrid($envContent, $envPath);
                break;

            default:
                $this->error('Invalid mode! Use: development, production, gmail, or sendgrid');
                $this->info('Examples:');
                $this->line('  php artisan email:switch development  # For testing (log)');
                $this->line('  php artisan email:switch gmail        # For production (Gmail)');
                $this->line('  php artisan email:switch sendgrid     # For production (SendGrid)');
                return 1;
        }

        return 0;
    }

    private function switchToDevelopment($envContent, $envPath)
    {
        $this->info('🔧 Switching to DEVELOPMENT mode (Log-based)...');

        $newConfig = $this->replaceEmailConfig($envContent, [
            'MAIL_MAILER=log',
            'MAIL_FROM_ADDRESS="admin@ramaperfume.com"',
            'MAIL_FROM_NAME="Admin Rama Perfume"',
            '',
            '# Development mode - emails saved to storage/logs/laravel.log'
        ]);

        File::put($envPath, $newConfig);
        
        $this->info('✅ Email switched to DEVELOPMENT mode');
        $this->info('📧 Emails will be saved to: storage/logs/laravel.log');
        $this->warn('⚠️  Run: php artisan config:clear');
    }

    private function switchToGmail($envContent, $envPath)
    {
        $this->info('📧 Switching to GMAIL SMTP (Production)...');

        $email = $this->ask('Gmail address', 'your-email@gmail.com');
        $password = $this->secret('Gmail App Password (16 characters)');
        $fromName = $this->ask('From Name', 'Rama Perfume');

        $newConfig = $this->replaceEmailConfig($envContent, [
            'MAIL_MAILER=smtp',
            'MAIL_HOST=smtp.gmail.com',
            'MAIL_PORT=587',
            "MAIL_USERNAME={$email}",
            "MAIL_PASSWORD={$password}",
            "MAIL_FROM_ADDRESS=\"{$email}\"",
            "MAIL_FROM_NAME=\"{$fromName}\"",
            'MAIL_ENCRYPTION=tls',
            '',
            '# Gmail SMTP - Get App Password from: https://myaccount.google.com/apppasswords'
        ]);

        File::put($envPath, $newConfig);
        
        $this->info('✅ Email switched to GMAIL SMTP');
        $this->info("📧 From: {$fromName} <{$email}>");
        $this->warn('⚠️  Run: php artisan config:clear');
        $this->warn('💡 Make sure 2FA is enabled and you use App Password!');
    }

    private function switchToSendGrid($envContent, $envPath)
    {
        $this->info('🌟 Switching to SENDGRID SMTP...');

        $apiKey = $this->secret('SendGrid API Key');
        $fromEmail = $this->ask('From Email', 'noreply@ramaperfume.com');
        $fromName = $this->ask('From Name', 'Rama Perfume');

        $newConfig = $this->replaceEmailConfig($envContent, [
            'MAIL_MAILER=smtp',
            'MAIL_HOST=smtp.sendgrid.net',
            'MAIL_PORT=587',
            'MAIL_USERNAME=apikey',
            "MAIL_PASSWORD={$apiKey}",
            "MAIL_FROM_ADDRESS=\"{$fromEmail}\"",
            "MAIL_FROM_NAME=\"{$fromName}\"",
            'MAIL_ENCRYPTION=tls',
            '',
            '# SendGrid SMTP - Get API Key from SendGrid dashboard'
        ]);

        File::put($envPath, $newConfig);
        
        $this->info('✅ Email switched to SENDGRID');
        $this->info("📧 From: {$fromName} <{$fromEmail}>");
        $this->warn('⚠️  Run: php artisan config:clear');
    }

    private function replaceEmailConfig($envContent, $newMailConfig)
    {
        // Find email configuration section
        $pattern = '/# Email Configuration.*?(?=\n[A-Z_]+=|$)/s';
        
        $newConfigBlock = "# Email Configuration (Manual System)\n" . implode("\n", $newMailConfig) . "\n";
        
        if (preg_match($pattern, $envContent)) {
            return preg_replace($pattern, $newConfigBlock, $envContent);
        } else {
            // If not found, find MAIL_ configuration
            $pattern = '/MAIL_.*?(?=\n[A-Z_]+=|$)/s';
            if (preg_match($pattern, $envContent)) {
                return preg_replace($pattern, $newConfigBlock, $envContent);
            } else {
                // Add at the end if no mail config found
                return $envContent . "\n" . $newConfigBlock;
            }
        }
    }
}
