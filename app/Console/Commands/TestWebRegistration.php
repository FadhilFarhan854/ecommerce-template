<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;

class TestWebRegistration extends Command
{
    protected $signature = 'test:web-registration {email} {name}';
    protected $description = 'Test web registration flow';

    public function handle()
    {
        $email = $this->argument('email');
        $name = $this->argument('name');
        
        $this->info("🧪 Testing web registration flow for: {$name} ({$email})");
        
        // Check if user exists
        $existingUser = User::where('email', $email)->first();
        if ($existingUser) {
            if ($this->confirm("User already exists. Delete and recreate?")) {
                $existingUser->delete();
                $this->info("🗑️ Existing user deleted!");
            } else {
                return;
            }
        }
        
        // Simulate web registration request
        $request = new Request([
            'name' => $name,
            'email' => $email,
            'phone' => '+62812345678',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);
        
        // Add AJAX headers to simulate frontend request
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');
        $request->headers->set('Accept', 'application/json');
        
        try {
            $this->info("📝 Creating user via webRegister method...");
            
            $controller = new AuthController();
            $response = $controller->webRegister($request);
            
            $this->info("✅ Registration response received!");
            
            if ($response->getStatusCode() === 201) {
                $data = json_decode($response->getContent(), true);
                $this->info("📧 Email: " . $data['data']['email']);
                $this->info("✅ Verification sent: " . ($data['data']['verification_sent'] ? 'YES' : 'NO'));
                $this->info("💬 Message: " . $data['message']);
                
                // Check user in database
                $user = User::where('email', $email)->first();
                if ($user) {
                    $this->info("👤 User created in database: {$user->name}");
                    $this->info("🔑 Token exists: " . ($user->email_verification_token ? 'YES' : 'NO'));
                    $this->info("✅ Verified: " . ($user->email_verified_at ? 'YES' : 'NO'));
                } else {
                    $this->error("❌ User not found in database!");
                }
            } else {
                $this->error("❌ Registration failed with status: " . $response->getStatusCode());
                $this->error("Response: " . $response->getContent());
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Registration failed with exception: " . $e->getMessage());
            $this->error("File: " . $e->getFile() . " Line: " . $e->getLine());
        }
        
        $this->info("🎉 Test completed!");
    }
}
