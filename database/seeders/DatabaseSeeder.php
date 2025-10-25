<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\AdminUserSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // keep existing test user for now
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // seed admin user
        $this->call(AdminUserSeeder::class);
    }
}
