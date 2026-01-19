<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RecruitmentAgencyRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create recruitment agency role if it doesn't exist
        $role = Role::firstOrCreate(['name' => 'recruitment_agency', 'guard_name' => 'api']);

        // Create permissions for recruitment agency
        $permissions = [
            'view candidates',
            'add candidates',
            'update candidates',
            'remove candidates',
            'view candidate documents',
            'create job postings',
            'update job postings',
            'delete job postings',
            'match candidates to jobs',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'api']);
        }

        // Assign permissions to role
        $role->syncPermissions($permissions);

        $this->command->info('Recruitment Agency role and permissions created successfully!');
    }
}
