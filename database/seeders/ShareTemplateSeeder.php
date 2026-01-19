<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ShareTemplate;
use App\Models\User;

class ShareTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default templates for all users (or a system user)
        // For now, we'll create them when users first access the feature
        // This seeder can be used to create default templates for specific users
        
        // Example: Create default templates for first user (if exists)
        $firstUser = User::first();
        if ($firstUser) {
            $defaultTemplates = [
                [
                    'name' => 'Job Application',
                    'description' => 'Quick share template for job applications',
                    'expiry_duration_days' => 30,
                    'default_message' => 'Please find attached my documents for your review.',
                    'is_default' => false,
                ],
                [
                    'name' => 'Compliance Check',
                    'description' => 'Share documents for compliance verification',
                    'expiry_duration_days' => 90,
                    'default_message' => 'Compliance documents as requested.',
                    'is_default' => false,
                ],
                [
                    'name' => 'Quick Share',
                    'description' => 'Quick share with short expiry',
                    'expiry_duration_days' => 7,
                    'default_message' => 'Shared documents for your reference.',
                    'is_default' => true,
                ],
            ];

            foreach ($defaultTemplates as $template) {
                ShareTemplate::updateOrCreate(
                    [
                        'user_id' => $firstUser->id,
                        'name' => $template['name'],
                    ],
                    array_merge($template, [
                        'user_id' => $firstUser->id,
                        'document_criteria' => [],
                        'permissions' => [],
                    ])
                );
            }
        }
    }
}
