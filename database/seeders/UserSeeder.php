<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's users.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Dojo Admin',
            'email' => 'admin@dojo.com',
            'password' => 'testPass',
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Dojo Staff',
            'email' => 'staff@dojo.com',
            'password' => 'testPass',
            'role' => 'staff',
            'email_verified_at' => now(),
        ]);
    }
}
