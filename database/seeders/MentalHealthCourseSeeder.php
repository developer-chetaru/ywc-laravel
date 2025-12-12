<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MentalHealthCourse;
use App\Models\MentalHealthCourseLesson;
use App\Models\User;
use Faker\Factory as Faker;

class MentalHealthCourseSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $admin = User::where('email', 'superadmin@mailinator.com')->first();

        $courses = [
            [
                'title' => 'Introduction to Mental Wellness',
                'category' => 'mental_health_literacy',
                'description' => 'A comprehensive introduction to mental health and wellness basics.',
                'difficulty_level' => 'beginner',
                'modules' => [
                    ['title' => 'Understanding Mental Health', 'lessons' => 3],
                    ['title' => 'Common Mental Health Conditions', 'lessons' => 4],
                    ['title' => 'Self-Care Strategies', 'lessons' => 3],
                ],
            ],
            [
                'title' => 'Managing Stress and Anxiety',
                'category' => 'self_care',
                'description' => 'Learn practical techniques to manage stress and anxiety in your daily life.',
                'difficulty_level' => 'intermediate',
                'modules' => [
                    ['title' => 'Understanding Stress', 'lessons' => 2],
                    ['title' => 'Anxiety Management Techniques', 'lessons' => 5],
                    ['title' => 'Building Resilience', 'lessons' => 3],
                ],
            ],
            [
                'title' => 'Mindfulness and Meditation',
                'category' => 'self_care',
                'description' => 'Master mindfulness practices and meditation techniques for better mental health.',
                'difficulty_level' => 'beginner',
                'modules' => [
                    ['title' => 'Introduction to Mindfulness', 'lessons' => 2],
                    ['title' => 'Meditation Practices', 'lessons' => 4],
                    ['title' => 'Applying Mindfulness Daily', 'lessons' => 3],
                ],
            ],
        ];

        foreach ($courses as $courseData) {
            $course = MentalHealthCourse::create([
                'title' => $courseData['title'],
                'description' => $courseData['description'],
                'category' => $courseData['category'],
                'modules' => $courseData['modules'],
                'total_duration_minutes' => array_sum(array_column($courseData['modules'], 'lessons')) * 15,
                'difficulty_level' => $courseData['difficulty_level'],
                'certificate_available' => true,
                'status' => 'published',
                'created_by' => $admin->id ?? null,
                'enrollment_count' => $faker->numberBetween(10, 200),
                'average_rating' => $faker->randomFloat(2, 4.0, 5.0),
                'rating_count' => $faker->numberBetween(5, 50),
            ]);

            $lessonOrder = 1;
            foreach ($courseData['modules'] as $module) {
                for ($i = 1; $i <= $module['lessons']; $i++) {
                    MentalHealthCourseLesson::create([
                        'course_id' => $course->id,
                        'title' => $module['title'] . ' - Lesson ' . $i,
                        'description' => $faker->sentence,
                        'order' => $lessonOrder++,
                        'lesson_type' => $faker->randomElement(['text', 'video', 'quiz']),
                        'content' => $faker->paragraphs(5, true),
                        'duration_minutes' => 15,
                    ]);
                }
            }
        }

        $this->command->info('✅ Created 3 mental health courses with lessons');
    }
}
