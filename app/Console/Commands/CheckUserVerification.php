<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class CheckUserVerification extends Command
{
    protected $signature = 'check:user {email}';
    protected $description = 'Check user verification status';

    public function handle()
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("User not found!");
            return;
        }
        
        $this->info("User: {$user->name}");
        $this->info("Email: {$user->email}");
        $this->info("Verified: " . ($user->email_verified_at ? "YES ({$user->email_verified_at})" : "NO"));
        $this->info("Token: " . ($user->email_verification_token ? "EXISTS" : "NULL"));
        
        if ($user->email_verification_token) {
            $url = route('verify.email', [
                'token' => $user->email_verification_token,
                'email' => $user->email
            ]);
            $this->info("Verification URL: {$url}");
        }
    }
}
