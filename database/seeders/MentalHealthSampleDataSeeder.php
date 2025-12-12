<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\MentalHealthTherapist;
use App\Models\MentalHealthSessionBooking;
use App\Models\MentalHealthMoodTracking;
use App\Models\MentalHealthGoal;
use Carbon\Carbon;
use Faker\Factory as Faker;

class MentalHealthSampleDataSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        
        // Get a regular user (not therapist)
        $therapistUserIds = \App\Models\MentalHealthTherapist::pluck('user_id')->toArray();
        $user = User::whereNotIn('id', $therapistUserIds)->first();
        
        if (!$user) {
            $this->command->warn('No regular user found. Creating one...');
            $user = User::create([
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'user@example.com',
                'password' => \Hash::make('password'),
                'status' => 'active',
                'is_active' => true,
            ]);
        }

        // Get approved therapists
        $therapists = MentalHealthTherapist::where('application_status', 'approved')
            ->where('is_active', true)
            ->limit(5)
            ->get();

        if ($therapists->isEmpty()) {
            $this->command->warn('No approved therapists found. Please run MentalHealthTherapistSeeder first.');
            return;
        }

        // Create some mood tracking entries
        for ($i = 0; $i < 14; $i++) {
            MentalHealthMoodTracking::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'tracked_date' => Carbon::now()->subDays($i)->toDateString(),
                ],
                [
                    'mood_rating' => $faker->numberBetween(4, 9),
                    'primary_mood' => $faker->randomElement(['happy', 'calm', 'anxious', 'content', 'tired']),
                    'energy_level' => $faker->numberBetween(3, 9),
                    'sleep_quality' => $faker->numberBetween(4, 10),
                    'stress_level' => $faker->numberBetween(2, 7),
                ]
            );
        }

        // Create some goals
        $goals = [
            [
                'title' => 'Improve Sleep Quality',
                'description' => 'Get at least 7 hours of sleep per night',
                'category' => 'mental_health',
                'target_date' => Carbon::now()->addMonths(2),
                'progress_percentage' => 45,
            ],
            [
                'title' => 'Practice Mindfulness Daily',
                'description' => 'Meditate for 10 minutes every morning',
                'category' => 'mental_health',
                'target_date' => Carbon::now()->addMonths(1),
                'progress_percentage' => 60,
            ],
            [
                'title' => 'Reduce Work Stress',
                'description' => 'Implement better work-life balance strategies',
                'category' => 'career',
                'target_date' => Carbon::now()->addMonths(3),
                'progress_percentage' => 30,
            ],
        ];

        foreach ($goals as $goalData) {
            MentalHealthGoal::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'title' => $goalData['title'],
                ],
                array_merge($goalData, [
                    'status' => 'active',
                ])
            );
        }

        // Create some upcoming sessions
        $therapist = $therapists->random();
        for ($i = 1; $i <= 3; $i++) {
            MentalHealthSessionBooking::create([
                'user_id' => $user->id,
                'therapist_id' => $therapist->id,
                'session_type' => $faker->randomElement(['video', 'voice', 'chat']),
                'duration_minutes' => $faker->randomElement([30, 60, 90]),
                'scheduled_at' => Carbon::now()->addDays($i)->setTime(10, 0),
                'timezone' => config('app.timezone'),
                'status' => $i === 1 ? 'confirmed' : 'pending',
                'session_cost' => $therapist->base_hourly_rate * ($faker->randomElement([30, 60, 90]) / 60),
                'credits_used' => 0,
                'amount_paid' => 0,
            ]);
        }

        // Update user credit balance
        $user->update([
            'mental_health_credit_balance' => $faker->randomFloat(2, 50, 200),
        ]);

        $this->command->info('✅ Created sample data for user: ' . $user->email);
        $this->command->info('   - 14 mood tracking entries');
        $this->command->info('   - 3 active goals');
        $this->command->info('   - 3 upcoming sessions');
        $this->command->info('   - Credit balance: £' . number_format($user->mental_health_credit_balance, 2));
    }
}
