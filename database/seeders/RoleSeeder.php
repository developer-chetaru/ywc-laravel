<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
public function run(): void
    {
        // 🔹 Disable foreign key checks to allow truncation safely
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 🔹 Clear old roles
        Role::truncate();

        // 🔹 Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 🔹 Define guards and roles
        $guards = ['web', 'api'];
        $roles = [
            'super_admin', 'user', 'Stewardess', 'Stew/masseuse', 'Sous chef', 'Purser', 'Nurse',
            'Lead deckhand', 'ETO', 'Deckhand', 'Deck/engineer', 'Chief stewardess',
            'Chief officer', 'Chief engineer', 'Chef', 'Captain', 'Bosun',
            '3rd officer', '3rd engineer', '2nd stewardess', '2nd officer', '2nd engineer'
        ];

        // 🔹 Insert roles for both guards
        foreach ($guards as $guard) {
            foreach ($roles as $role) {
                Role::create([
                    'name' => $role,
                    'guard_name' => $guard,
                ]);
            }
        }

        $this->command->info('✅ Roles truncated and re-seeded for both web and api guards.');
    }
}
