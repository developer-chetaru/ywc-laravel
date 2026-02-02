<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ForumBadgeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $badges = [
            // Achievement Badges
            [
                'name' => 'First Mate',
                'description' => 'Started 10+ helpful discussions',
                'icon' => 'first-mate',
                'type' => 'achievement',
                'criteria' => json_encode(['threads' => 10]),
                'sort_order' => 1,
            ],
            [
                'name' => 'Problem Solver',
                'description' => '50+ best answers selected',
                'icon' => 'problem-solver',
                'type' => 'achievement',
                'criteria' => json_encode(['best_answers' => 50]),
                'sort_order' => 2,
            ],
            [
                'name' => 'Department Expert',
                'description' => '100+ posts in department category',
                'icon' => 'department-expert',
                'type' => 'achievement',
                'criteria' => json_encode(['posts' => 100]),
                'sort_order' => 3,
            ],
            [
                'name' => 'Helpful Crewmate',
                'description' => '200+ helpful reactions received',
                'icon' => 'helpful-crewmate',
                'type' => 'achievement',
                'criteria' => json_encode(['helpful_reactions' => 200]),
                'sort_order' => 4,
            ],
            [
                'name' => 'Veteran',
                'description' => '2+ years active membership',
                'icon' => 'veteran',
                'type' => 'achievement',
                'criteria' => json_encode(['years' => 2]),
                'sort_order' => 5,
            ],
            [
                'name' => 'Active Member',
                'description' => '50+ reputation points',
                'icon' => 'active-member',
                'type' => 'achievement',
                'criteria' => json_encode(['reputation' => 50]),
                'sort_order' => 6,
            ],
            [
                'name' => 'Senior Member',
                'description' => '200+ reputation points',
                'icon' => 'senior-member',
                'type' => 'achievement',
                'criteria' => json_encode(['reputation' => 200]),
                'sort_order' => 7,
            ],
            [
                'name' => 'Expert',
                'description' => '500+ reputation points',
                'icon' => 'expert',
                'type' => 'achievement',
                'criteria' => json_encode(['reputation' => 500]),
                'sort_order' => 8,
            ],
        ];

        foreach ($badges as $badge) {
            DB::table('forum_badges')->updateOrInsert(
                ['name' => $badge['name']],
                array_merge($badge, [
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        $this->command->info('✅ Forum badges seeded successfully!');
    }
}
