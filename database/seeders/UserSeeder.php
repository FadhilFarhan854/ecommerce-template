<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Address;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test user
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password123'),
        ]);

        // Create sample addresses for the user
        Address::create([
            'user_id' => $user->id,
            'nama_depan' => 'John',
            'nama_belakang' => 'Doe',
            'alamat' => 'Jl. Sudirman No. 123, RT.05/RW.02',
            'kelurahan' => 'Senayan',
            'kecamatan' => 'Kebayoran Baru',
            'kota' => 'Jakarta Selatan',
            'provinsi' => 'DKI Jakarta',
            'kode_pos' => '12190',
            'hp' => '081234567890',
        ]);

        Address::create([
            'user_id' => $user->id,
            'nama_depan' => 'John',
            'nama_belakang' => 'Doe',
            'alamat' => 'Jl. Gatot Subroto No. 456, Komplek ABC',
            'kelurahan' => 'Kuningan Timur',
            'kecamatan' => 'Setiabudi',
            'kota' => 'Jakarta Selatan',
            'provinsi' => 'DKI Jakarta',
            'kode_pos' => '12950',
            'hp' => '087654321098',
        ]);

        $this->command->info('User and addresses seeded successfully!');
    }
}
