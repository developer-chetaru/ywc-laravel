<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MentalHealthResource;
use App\Models\User;
use Faker\Factory as Faker;

class MentalHealthResourceSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $admin = User::where('email', 'superadmin@mailinator.com')->first();

        $resources = [
            [
                'title' => 'Understanding Anxiety: A Comprehensive Guide',
                'category' => 'anxiety',
                'resource_type' => 'article',
                'description' => 'Learn about anxiety disorders, symptoms, and evidence-based coping strategies.',
                'reading_time_minutes' => 15,
                'difficulty_level' => 'beginner',
            ],
            [
                'title' => 'Mindfulness Meditation for Stress Relief',
                'category' => 'stress',
                'resource_type' => 'video',
                'description' => 'A guided 20-minute meditation session to help reduce stress and improve focus.',
                'reading_time_minutes' => 20,
                'difficulty_level' => 'beginner',
            ],
            [
                'title' => 'CBT Thought Record Worksheet',
                'category' => 'depression',
                'resource_type' => 'worksheet',
                'description' => 'Downloadable worksheet to track negative thoughts and challenge cognitive distortions.',
                'reading_time_minutes' => 10,
                'difficulty_level' => 'intermediate',
            ],
            [
                'title' => 'Building Resilience: Strategies for Tough Times',
                'category' => 'stress',
                'resource_type' => 'article',
                'description' => 'Evidence-based techniques to build emotional resilience and bounce back from adversity.',
                'reading_time_minutes' => 12,
                'difficulty_level' => 'intermediate',
            ],
            [
                'title' => 'Sleep Hygiene: Improving Your Sleep Quality',
                'category' => 'stress',
                'resource_type' => 'article',
                'description' => 'Practical tips and strategies for improving sleep quality and establishing healthy sleep patterns.',
                'reading_time_minutes' => 8,
                'difficulty_level' => 'beginner',
            ],
            [
                'title' => 'Managing Work-Life Balance at Sea',
                'category' => 'career',
                'resource_type' => 'article',
                'description' => 'Specialized guide for yacht crew members on maintaining work-life balance while working at sea.',
                'reading_time_minutes' => 18,
                'difficulty_level' => 'intermediate',
            ],
            [
                'title' => 'Coping with Isolation: A Guide for Maritime Workers',
                'category' => 'relationships',
                'resource_type' => 'article',
                'description' => 'Strategies for managing feelings of isolation and maintaining connections while working on yachts.',
                'reading_time_minutes' => 14,
                'difficulty_level' => 'beginner',
            ],
            [
                'title' => 'Progressive Muscle Relaxation Audio Guide',
                'category' => 'stress',
                'resource_type' => 'audio',
                'description' => 'Audio guide for progressive muscle relaxation to reduce physical tension and anxiety.',
                'reading_time_minutes' => 25,
                'difficulty_level' => 'beginner',
            ],
            [
                'title' => 'Understanding Depression: Signs and Symptoms',
                'category' => 'depression',
                'resource_type' => 'article',
                'description' => 'Comprehensive guide to understanding depression, its symptoms, and when to seek help.',
                'reading_time_minutes' => 16,
                'difficulty_level' => 'beginner',
            ],
            [
                'title' => 'Communication Skills for Better Relationships',
                'category' => 'relationships',
                'resource_type' => 'article',
                'description' => 'Learn effective communication techniques to improve relationships with colleagues and loved ones.',
                'reading_time_minutes' => 12,
                'difficulty_level' => 'intermediate',
            ],
            [
                'title' => 'Trauma Recovery: First Steps',
                'category' => 'trauma',
                'resource_type' => 'article',
                'description' => 'Understanding trauma and initial steps toward recovery and healing.',
                'reading_time_minutes' => 20,
                'difficulty_level' => 'advanced',
            ],
            [
                'title' => 'Breathing Exercises for Anxiety Relief',
                'category' => 'anxiety',
                'resource_type' => 'video',
                'description' => 'Video demonstration of breathing techniques to quickly reduce anxiety symptoms.',
                'reading_time_minutes' => 5,
                'difficulty_level' => 'beginner',
            ],
        ];

        foreach ($resources as $resourceData) {
            MentalHealthResource::create([
                'title' => $resourceData['title'],
                'description' => $resourceData['description'],
                'category' => $resourceData['category'],
                'resource_type' => $resourceData['resource_type'],
                'content' => $resourceData['resource_type'] === 'article' ? $faker->paragraphs(10, true) : null,
                'tags' => [$resourceData['category'], $resourceData['resource_type'], 'self-help'],
                'target_audience' => ['all_crew'],
                'reading_time_minutes' => $resourceData['reading_time_minutes'],
                'difficulty_level' => $resourceData['difficulty_level'],
                'author' => $faker->name,
                'publication_date' => $faker->dateTimeBetween('-1 year', 'now'),
                'status' => 'published',
                'view_count' => $faker->numberBetween(10, 500),
                'download_count' => $resourceData['resource_type'] === 'worksheet' ? $faker->numberBetween(5, 200) : 0,
                'bookmark_count' => $faker->numberBetween(0, 50),
                'average_rating' => $faker->randomFloat(2, 3.5, 5.0),
                'rating_count' => $faker->numberBetween(5, 30),
                'created_by' => $admin->id ?? null,
            ]);
        }

        $this->command->info('✅ Created 12 mental health resources');
    }
}
