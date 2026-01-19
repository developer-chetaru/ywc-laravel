<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class EmployerRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create employer role if it doesn't exist
        $role = Role::firstOrCreate(['name' => 'employer', 'guard_name' => 'api']);

        // Create permissions for employer
        $permissions = [
            'view crew members',
            'add crew members',
            'update crew members',
            'remove crew members',
            'view crew documents',
            'view compliance reports',
            'export compliance reports',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'api']);
        }

        // Assign permissions to role
        $role->syncPermissions($permissions);

        $this->command->info('Employer role and permissions created successfully!');
    }
}
