<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VerificationLevel;

class VerificationLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $levels = [
            [
                'name' => 'Self-Verified',
                'level' => 1,
                'description' => 'Document uploaded and verified by the user themselves',
                'badge_icon' => 'fas fa-user-check',
                'badge_color' => 'gray',
                'is_active' => true,
            ],
            [
                'name' => 'Peer-Verified',
                'level' => 2,
                'description' => 'Verified by another crew member or peer',
                'badge_icon' => 'fas fa-users',
                'badge_color' => 'blue',
                'is_active' => true,
            ],
            [
                'name' => 'Employer-Verified',
                'level' => 3,
                'description' => 'Verified by employer or yacht management',
                'badge_icon' => 'fas fa-building',
                'badge_color' => 'green',
                'is_active' => true,
            ],
            [
                'name' => 'Training Provider-Verified',
                'level' => 4,
                'description' => 'Verified by certified training provider or institution',
                'badge_icon' => 'fas fa-graduation-cap',
                'badge_color' => 'purple',
                'is_active' => true,
            ],
            [
                'name' => 'Official-Verified',
                'level' => 5,
                'description' => 'Verified by official authority (government, maritime authority, etc.)',
                'badge_icon' => 'fas fa-shield-alt',
                'badge_color' => 'gold',
                'is_active' => true,
            ],
        ];

        foreach ($levels as $level) {
            VerificationLevel::updateOrCreate(
                ['level' => $level['level']],
                $level
            );
        }
    }
}
