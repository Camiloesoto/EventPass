<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Log the seeder execution
        Log::info('Starting AdminUserSeeder execution');

        // Create admin user
        $admin = User::create([
            'name' => 'Admin EventPass',
            'email' => 'admin@eventpass.com',
            'password' => Hash::make('admin123'),
            'is_admin' => true,
            'email_verified_at' => now(),
        ]);

        // Log successful creation
        Log::info('Admin user created successfully', [
            'admin_id' => $admin->getId(),
            'admin_name' => $admin->getName(),
            'admin_email' => $admin->getEmail(),
            'is_admin' => $admin->getIsAdmin()
        ]);

        // Create regular user for testing
        $user = User::create([
            'name' => 'Test User',
            'email' => 'user@eventpass.com',
            'password' => Hash::make('user123'),
            'is_admin' => false,
            'email_verified_at' => now(),
        ]);

        Log::info('Test user created successfully', [
            'user_id' => $user->getId(),
            'user_name' => $user->getName(),
            'user_email' => $user->getEmail(),
            'is_admin' => $user->getIsAdmin()
        ]);

        $this->command->info('Admin users created successfully!');
        $this->command->info('Admin: admin@eventpass.com / admin123');
        $this->command->info('User: user@eventpass.com / user123');
    }
}