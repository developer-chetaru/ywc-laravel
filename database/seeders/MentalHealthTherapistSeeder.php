<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\MentalHealthTherapist;
use App\Models\MentalHealthTherapistAvailability;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class MentalHealthTherapistSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // Create therapist users
        $therapistUsers = [];
        for ($i = 0; $i < 15; $i++) {
            $therapistUsers[] = User::firstOrCreate(
                ['email' => 'therapist' . ($i + 1) . '@example.com'],
                [
                    'first_name' => $faker->firstName,
                    'last_name' => $faker->lastName,
                    'password' => Hash::make('password'),
                    'status' => 'active',
                    'is_active' => true,
                    'phone' => $faker->phoneNumber,
                    'gender' => $faker->randomElement(['Male', 'Female']),
                    'nationality' => $faker->randomElement(['American', 'British', 'Canadian', 'Australian']),
                ]
            );
        }

        $specializations = [
            ['anxiety', 'stress_management', 'workplace_issues'],
            ['depression', 'mood_disorders', 'self_esteem'],
            ['trauma', 'ptsd', 'crisis_intervention'],
            ['relationships', 'couples_therapy', 'family_therapy'],
            ['addiction', 'substance_abuse', 'recovery'],
            ['grief', 'loss', 'bereavement'],
            ['career_stress', 'work_life_balance', 'burnout'],
            ['sleep_disorders', 'insomnia', 'relaxation'],
            ['eating_disorders', 'body_image', 'nutrition'],
            ['anger_management', 'emotional_regulation', 'mindfulness'],
        ];

        $languages = [
            ['English', 'Spanish'],
            ['English', 'French'],
            ['English'],
            ['English', 'German'],
            ['English', 'Italian'],
            ['English', 'Portuguese'],
            ['English', 'Spanish', 'French'],
            ['English'],
            ['English', 'Dutch'],
            ['English', 'Swedish'],
        ];

        $approaches = [
            ['CBT', 'DBT', 'Mindfulness'],
            ['Psychodynamic', 'Humanistic', 'Person-Centered'],
            ['EMDR', 'Trauma-Informed', 'Somatic'],
            ['Solution-Focused', 'Brief Therapy', 'Narrative'],
            ['Gestalt', 'Existential', 'Integrative'],
        ];

        foreach ($therapistUsers as $index => $user) {
            // Delete existing therapist record if exists
            MentalHealthTherapist::where('user_id', $user->id)->delete();
            
            $therapist = MentalHealthTherapist::create([
                'user_id' => $user->id,
                'application_status' => $index < 12 ? 'approved' : ($index < 14 ? 'pending' : 'rejected'),
                'biography' => $faker->paragraph(5),
                'specializations' => $specializations[$index % count($specializations)],
                'languages_spoken' => $languages[$index % count($languages)],
                'therapeutic_approaches' => $approaches[$index % count($approaches)],
                'years_experience' => $faker->numberBetween(3, 25),
                'education_history' => [
                    [
                        'degree' => $faker->randomElement(['PhD in Psychology', 'MA in Counseling', 'MSW', 'PsyD']),
                        'institution' => $faker->company . ' University',
                        'year' => $faker->year('2010', '2020'),
                    ],
                ],
                'certifications' => [
                    'Licensed Professional Counselor',
                    'Certified Trauma Specialist',
                ],
                'timezone' => $faker->timezone,
                'license_numbers' => [
                    [
                        'number' => 'LIC-' . $faker->numerify('######'),
                        'jurisdiction' => $faker->state,
                        'expiry_date' => now()->addYears(2)->format('Y-m-d'),
                    ],
                ],
                'insurance_information' => 'Professional liability insurance active',
                'base_hourly_rate' => $faker->randomFloat(2, 80, 200),
                'session_type_pricing' => [
                    'video' => 100,
                    'voice' => 85,
                    'chat' => 70,
                    'email' => 50,
                ],
                'duration_pricing' => [
                    '30' => 50,
                    '60' => 100,
                    '90' => 150,
                ],
                'sliding_scale_available' => $faker->boolean(30),
                'is_active' => $index < 12,
                'is_featured' => $index < 3,
                'rating' => $faker->randomFloat(2, 4.0, 5.0),
                'total_sessions' => $faker->numberBetween(50, 500),
                'total_reviews' => $faker->numberBetween(10, 100),
                'session_completion_rate' => $faker->randomFloat(2, 85, 100),
                'average_response_time_minutes' => $faker->numberBetween(15, 120),
                'no_show_rate' => $faker->randomFloat(2, 0, 10),
                'continuing_education_hours' => $faker->numberBetween(0, 40),
                'professional_philosophy' => $faker->paragraph(3),
                'areas_of_focus' => $specializations[$index % count($specializations)],
            ]);

            // Create availability for approved therapists
            if ($therapist->is_active) {
                $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
                foreach ($days as $day) {
                    MentalHealthTherapistAvailability::create([
                        'therapist_id' => $therapist->id,
                        'day_of_week' => $day,
                        'start_time' => '09:00:00',
                        'end_time' => '17:00:00',
                        'is_recurring' => true,
                        'session_durations' => [30, 60, 90],
                        'buffer_minutes' => 15,
                        'max_daily_sessions' => 8,
                        'max_weekly_sessions' => 40,
                        'is_active' => true,
                    ]);
                }
            }
        }

        $this->command->info('✅ Created 15 therapists (12 approved, 2 pending, 1 rejected)');
    }
}
