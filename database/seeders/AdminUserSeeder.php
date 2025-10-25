<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Creates a default admin account for development.
        // NOTE: change these credentials in production.
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => 'password', // User model casts 'password' => 'hashed'
                'is_admin' => true,
            ]
        );
    }
}
