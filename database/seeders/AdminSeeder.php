<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Make sure role exists for 'api' guard (User model default guard)
        $role = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'api']);

        // Check if Super Admin user already exists
        $user = User::where('email', 'superadmin@example.com')->first();

        if (!$user) {
            // Create Super Admin user
            $user = User::create([
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'email' => 'superadmin@example.com',
                'password' => Hash::make('password123'),
                'status' => 'active',
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
        }

        // Assign Role if not already assigned
        if (!$user->hasRole($role)) {
            $user->assignRole($role);
        }
    }
}
