<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::firstOrCreate([
            'email' => 'admin@sfm.com',
        ], [
            'name' => 'System Administrator',
            'email_verified_at' => now(),
            'password' => Hash::make('admin123'),
        ]);

        // Create test user
        User::firstOrCreate([
            'email' => 'test@sfm.com',
        ], [
            'name' => 'Test User',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);

        // Create manager user
        User::firstOrCreate([
            'email' => 'manager@sfm.com',
        ], [
            'name' => 'Farm Manager',
            'email_verified_at' => now(),
            'password' => Hash::make('manager123'),
        ]);

        // Create additional users using factory
        User::factory(5)->create([
            'email_verified_at' => now(),
        ]);

        // Create unverified users for testing email verification
        User::factory(3)->create([
            'email_verified_at' => null,
        ]);

        $this->command->info('Users seeded successfully!');
        $this->command->info('Admin: admin@sfm.com / admin123');
        $this->command->info('Test: test@sfm.com / password');
        $this->command->info('Manager: manager@sfm.com / manager123');
    }
}
