<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AssignRolesToUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * This seeder assigns all available roles (except super_admin) to users.
     * It distributes roles randomly among users who don't have the super_admin role.
     */
    public function run(): void
    {
        // Get all roles except super_admin
        $roles = Role::where('name', '!=', 'super_admin')
            ->where('guard_name', 'api') // Use 'api' guard as per User model
            ->get();

        if ($roles->isEmpty()) {
            $this->command->warn('⚠️  No roles found (excluding super_admin). Please run RoleSeeder first.');
            return;
        }

        // Get all users who don't have super_admin role
        $users = User::whereDoesntHave('roles', function ($query) {
            $query->where('name', 'super_admin');
        })->get();

        if ($users->isEmpty()) {
            $this->command->warn('⚠️  No users found (excluding super_admin).');
            return;
        }

        $this->command->info("📋 Found {$roles->count()} roles and {$users->count()} users to assign roles to.");

        $assignedCount = 0;
        $roleArray = $roles->pluck('name')->toArray();

        // Assign roles to users
        foreach ($users as $user) {
            // Remove any existing roles first (optional - comment out if you want to keep existing roles)
            $user->syncRoles([]);

            // Randomly assign 1-2 roles to each user
            $numberOfRoles = rand(1, min(2, count($roleArray)));
            
            // Shuffle and take random roles
            $shuffledRoles = $roleArray;
            shuffle($shuffledRoles);
            $selectedRoles = array_slice($shuffledRoles, 0, $numberOfRoles);

            // Assign roles to user
            foreach ($selectedRoles as $roleName) {
                $user->assignRole($roleName);
            }

            $assignedCount++;
        }

        $this->command->info("✅ Successfully assigned roles to {$assignedCount} users!");
        $this->command->info("📊 Roles assigned: " . implode(', ', $roleArray));
    }
}

