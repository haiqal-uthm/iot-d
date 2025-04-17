<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash; // Add this line

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin123'), // Now will work!
            'email_verified_at' => now(),
            'role' => 'admin',
        ]);

        echo "✅ Admin Created | Email: admin@gmail.com | Password: admin123\n";
    }
}
