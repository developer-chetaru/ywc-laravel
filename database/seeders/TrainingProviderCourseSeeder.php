<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TrainingProvider;
use App\Models\TrainingProviderCourse;
use App\Models\TrainingCertification;
use App\Models\TrainingCourseLocation;
use App\Models\TrainingCourseSchedule;
use Carbon\Carbon;

class TrainingProviderCourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $providers = TrainingProvider::all();
        $certifications = TrainingCertification::all();

        if ($providers->isEmpty() || $certifications->isEmpty()) {
            $this->command->warn('No providers or certifications found. Please run category, certification, and provider seeders first.');
            return;
        }

        // Create courses for STCW Basic Safety (most common)
        $stcwCert = TrainingCertification::where('slug', 'stcw-basic-safety-training')->first();
        if ($stcwCert) {
            foreach ($providers->take(3) as $provider) {
                $course = TrainingProviderCourse::firstOrCreate(
                    [
                        'certification_id' => $stcwCert->id,
                        'provider_id' => $provider->id,
                    ],
                    [
                    'price' => rand(500, 950),
                    'ywc_discount_percentage' => 20,
                    'duration_days' => 5,
                    'duration_hours' => 40,
                    'class_size_max' => 12,
                    'language_of_instruction' => 'English',
                    'format' => 'in-person',
                    'course_structure' => 'Comprehensive 5-day course covering all four STCW Basic Safety modules with emphasis on practical training.',
                    'daily_schedule' => [
                        'Day 1' => 'Personal Survival Techniques (8 hours)',
                        'Day 2' => 'Fire Prevention and Fire Fighting (8 hours)',
                        'Day 3' => 'Elementary First Aid (8 hours)',
                        'Day 4' => 'Personal Safety and Social Responsibilities (6 hours)',
                        'Day 5' => 'Assessment and Certification (4 hours)',
                    ],
                    'learning_outcomes' => [
                        'Understand personal survival techniques',
                        'Demonstrate fire fighting procedures',
                        'Apply first aid in maritime emergencies',
                        'Understand safety responsibilities',
                    ],
                    'assessment_methods' => ['Written exam', 'Practical demonstrations', 'Scenario-based assessments'],
                    'materials_included' => ['Course manual', 'Certificate', 'Lunch daily'],
                    'accommodation_included' => false,
                    'meals_included' => true,
                    'meals_details' => 'Lunch and refreshments included',
                    'parking_included' => true,
                    'booking_url' => 'https://example.com/book',
                    'ywc_tracking_code' => 'YWC' . $provider->id . '-' . $stcwCert->id,
                    'is_active' => true,
                    ]
                );

                // Add location
                $location = TrainingCourseLocation::create([
                    'provider_course_id' => $course->id,
                    'name' => $provider->name . ' Training Center',
                    'city' => $provider->id == 1 ? 'Southampton' : ($provider->id == 2 ? 'Fort Lauderdale' : 'London'),
                    'country' => $provider->id == 1 ? 'United Kingdom' : ($provider->id == 2 ? 'United States' : 'United Kingdom'),
                    'region' => $provider->id == 1 ? 'UK' : ($provider->id == 2 ? 'Caribbean' : 'UK'),
                    'is_primary' => true,
                ]);

                // Add upcoming schedules
                for ($i = 1; $i <= 3; $i++) {
                    TrainingCourseSchedule::create([
                        'provider_course_id' => $course->id,
                        'location_id' => $location->id,
                        'start_date' => Carbon::now()->addWeeks($i * 2),
                        'end_date' => Carbon::now()->addWeeks($i * 2)->addDays(4),
                        'available_spots' => 12,
                        'booked_spots' => rand(0, 5),
                        'is_full' => false,
                        'is_cancelled' => false,
                    ]);
                }
            }
        }

        // Create ENG1 course
        $eng1Cert = TrainingCertification::where('slug', 'eng1-medical-certificate')->first();
        if ($eng1Cert) {
            foreach ($providers->take(2) as $provider) {
                TrainingProviderCourse::firstOrCreate(
                    [
                        'certification_id' => $eng1Cert->id,
                        'provider_id' => $provider->id,
                    ],
                    [
                    'price' => rand(100, 200),
                    'ywc_discount_percentage' => 20,
                    'duration_days' => 1,
                    'language_of_instruction' => 'English',
                    'format' => 'in-person',
                    'course_structure' => 'Medical examination with approved doctor',
                    'materials_included' => ['Medical certificate'],
                    'booking_url' => 'https://example.com/book',
                    'is_active' => true,
                    ]
                );
            }
        }

        // Create SSO course
        $ssoCert = TrainingCertification::where('slug', 'ship-security-officer-sso')->first();
        if ($ssoCert) {
            $provider = $providers->first();
            TrainingProviderCourse::firstOrCreate(
                [
                    'certification_id' => $ssoCert->id,
                    'provider_id' => $provider->id,
                ],
                [
                'price' => 1200,
                'ywc_discount_percentage' => 20,
                'duration_days' => 6,
                'language_of_instruction' => 'English',
                'format' => 'in-person',
                'course_structure' => 'Comprehensive 6-day security officer training',
                'materials_included' => ['Course manual', 'Certificate', 'Lunch'],
                'meals_included' => true,
                'booking_url' => 'https://example.com/book',
                'is_active' => true,
                ]
            );
        }

        $this->command->info('Sample provider courses created successfully!');
    }
}
