<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TrainingCertificationCategory;

class TrainingCertificationCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Safety',
                'slug' => 'safety',
                'description' => 'Safety certifications including STCW Basic Safety, Advanced Fire Fighting, and other maritime safety requirements.',
                'icon' => 'shield-check',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Medical',
                'slug' => 'medical',
                'description' => 'Medical certifications such as ENG1, Medical Care, First Aid at Sea, and other health-related qualifications.',
                'icon' => 'heart-pulse',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Security',
                'slug' => 'security',
                'description' => 'Security certifications including Ship Security Officer (SSO), Security Awareness, and maritime security training.',
                'icon' => 'lock',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Engineering',
                'slug' => 'engineering',
                'description' => 'Engineering certifications covering engine room operations, electrical systems, and marine engineering.',
                'icon' => 'cog',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Navigation',
                'slug' => 'navigation',
                'description' => 'Navigation certifications including COLREGs, Watchkeeping, Electronic Navigation, and navigation systems.',
                'icon' => 'compass',
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Leadership',
                'slug' => 'leadership',
                'description' => 'Leadership and management certifications including Leadership & Teamwork, Crisis Management, and crew management.',
                'icon' => 'users',
                'sort_order' => 6,
                'is_active' => true,
            ],
            [
                'name' => 'Specialized',
                'slug' => 'specialized',
                'description' => 'Specialized yacht training including wine courses, interior design, yacht-specific training, and luxury service.',
                'icon' => 'star',
                'sort_order' => 7,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            TrainingCertificationCategory::create($category);
        }
    }
}
