<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TrainingProvider;
use App\Models\TrainingProviderCourse;
use App\Models\TrainingCertification;
use App\Models\TrainingCourseLocation;
use App\Models\TrainingCourseSchedule;
use App\Models\TrainingCourseReview;
use App\Models\TrainingUserCertification;
use App\Models\User;
use App\Models\TrainingCourseBundle;
use Carbon\Carbon;

class TrainingDummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating comprehensive dummy data for Training module...');

        $providers = TrainingProvider::all();
        $certifications = TrainingCertification::all();
        $users = User::take(10)->get();

        if ($providers->isEmpty() || $certifications->isEmpty()) {
            $this->command->warn('No providers or certifications found. Please run category, certification, and provider seeders first.');
            return;
        }

        // Create courses for all major certifications
        $this->createCoursesForCertifications($providers, $certifications);
        
        // Create additional locations
        $this->createAdditionalLocations();
        
        // Create more schedules
        $this->createMoreSchedules();
        
        // Create sample reviews
        if ($users->isNotEmpty()) {
            $this->createSampleReviews($users);
        }
        
        // Create sample user certifications
        if ($users->isNotEmpty()) {
            $this->createSampleUserCertifications($users, $certifications);
        }
        
        // Create course bundles
        $this->createCourseBundles($providers);

        $this->command->info('Dummy data created successfully!');
        $this->command->info('Total courses: ' . TrainingProviderCourse::count());
        $this->command->info('Total locations: ' . TrainingCourseLocation::count());
        $this->command->info('Total schedules: ' . TrainingCourseSchedule::count());
        $this->command->info('Total reviews: ' . TrainingCourseReview::count());
        $this->command->info('Total user certifications: ' . TrainingUserCertification::count());
    }

    private function createCoursesForCertifications($providers, $certifications)
    {
        $courseData = [
            'stcw-basic-safety-training' => [
                'price_range' => [500, 950],
                'duration' => 5,
                'formats' => ['in-person'],
            ],
            'advanced-fire-fighting' => [
                'price_range' => [800, 1200],
                'duration' => 3,
                'formats' => ['in-person'],
            ],
            'eng1-medical-certificate' => [
                'price_range' => [100, 200],
                'duration' => 1,
                'formats' => ['in-person'],
            ],
            'ship-security-officer-sso' => [
                'price_range' => [1000, 1500],
                'duration' => 6,
                'formats' => ['in-person', 'hybrid'],
            ],
            'security-awareness' => [
                'price_range' => [200, 400],
                'duration' => 1,
                'formats' => ['in-person', 'online'],
            ],
            'colregs-collision-avoidance' => [
                'price_range' => [300, 600],
                'duration' => 2,
                'formats' => ['in-person', 'online', 'hybrid'],
            ],
            'yachtmaster-offshore' => [
                'price_range' => [1500, 2500],
                'duration' => 7,
                'formats' => ['in-person'],
            ],
            'leadership-teamwork' => [
                'price_range' => [400, 800],
                'duration' => 3,
                'formats' => ['in-person', 'hybrid'],
            ],
            'food-safety-hygiene' => [
                'price_range' => [150, 300],
                'duration' => 1,
                'formats' => ['in-person', 'online'],
            ],
            'wine-service-sommelier' => [
                'price_range' => [600, 1200],
                'duration' => 3,
                'formats' => ['in-person'],
            ],
        ];

        foreach ($certifications as $cert) {
            $slug = $cert->slug;
            if (!isset($courseData[$slug])) {
                continue;
            }

            $data = $courseData[$slug];
            $providerCount = rand(2, min(4, $providers->count()));

            foreach ($providers->random($providerCount) as $provider) {
                // Check if course already exists
                $existing = TrainingProviderCourse::where('certification_id', $cert->id)
                    ->where('provider_id', $provider->id)
                    ->first();

                if ($existing) {
                    continue;
                }

                $price = rand($data['price_range'][0], $data['price_range'][1]);
                $format = $data['formats'][array_rand($data['formats'])];

                $course = TrainingProviderCourse::create([
                    'certification_id' => $cert->id,
                    'provider_id' => $provider->id,
                    'price' => $price,
                    'ywc_discount_percentage' => 20,
                    'duration_days' => $data['duration'],
                    'duration_hours' => $data['duration'] * 8,
                    'class_size_max' => rand(8, 16),
                    'language_of_instruction' => 'English',
                    'format' => $format,
                    'course_structure' => $this->getCourseStructure($cert->name, $data['duration']),
                    'daily_schedule' => $this->getDailySchedule($data['duration']),
                    'learning_outcomes' => $this->getLearningOutcomes($cert->name),
                    'assessment_methods' => ['Written exam', 'Practical assessment', 'Continuous evaluation'],
                    'materials_included' => ['Course manual', 'Certificate', 'Lunch', 'Refreshments'],
                    'accommodation_included' => rand(0, 1) == 1,
                    'meals_included' => true,
                    'meals_details' => 'Lunch and refreshments included',
                    'parking_included' => rand(0, 1) == 1,
                    'transport_included' => false,
                    're_sits_included' => rand(0, 1) == 1,
                    'special_features' => $this->getSpecialFeatures($cert->name),
                    'booking_url' => 'https://example.com/book/' . $provider->id . '/' . $cert->id,
                    'ywc_tracking_code' => 'YWC' . $provider->id . '-' . $cert->id . '-' . time(),
                    'is_active' => true,
                ]);

                // Create location for this course
                $this->createLocationForCourse($course, $provider);
            }
        }
    }

    private function createLocationForCourse($course, $provider)
    {
        $locations = [
            ['city' => 'Southampton', 'country' => 'United Kingdom', 'region' => 'UK'],
            ['city' => 'Fort Lauderdale', 'country' => 'United States', 'region' => 'Caribbean'],
            ['city' => 'Antibes', 'country' => 'France', 'region' => 'Mediterranean'],
            ['city' => 'Monaco', 'country' => 'Monaco', 'region' => 'Mediterranean'],
            ['city' => 'Palma', 'country' => 'Spain', 'region' => 'Mediterranean'],
            ['city' => 'London', 'country' => 'United Kingdom', 'region' => 'UK'],
            ['city' => 'Miami', 'country' => 'United States', 'region' => 'Caribbean'],
        ];

        $location = $locations[array_rand($locations)];

        TrainingCourseLocation::create([
            'provider_course_id' => $course->id,
            'name' => $provider->name . ' - ' . $location['city'],
            'city' => $location['city'],
            'country' => $location['country'],
            'region' => $location['region'],
            'address' => rand(100, 999) . ' Maritime Training Street',
            'postal_code' => rand(10000, 99999),
            'is_primary' => true,
        ]);
    }

    private function createAdditionalLocations()
    {
        $courses = TrainingProviderCourse::whereDoesntHave('locations')->get();

        foreach ($courses->take(10) as $course) {
            $provider = $course->provider;
            $this->createLocationForCourse($course, $provider);
        }
    }

    private function createMoreSchedules()
    {
        $courses = TrainingProviderCourse::with('locations')->get();

        foreach ($courses as $course) {
            $location = $course->locations->first();
            if (!$location) {
                continue;
            }

            // Create 4-6 upcoming schedules
            $scheduleCount = rand(4, 6);
            for ($i = 1; $i <= $scheduleCount; $i++) {
                $startDate = Carbon::now()->addWeeks($i * 2 + rand(0, 2));
                $endDate = $startDate->copy()->addDays($course->duration_days - 1);

                TrainingCourseSchedule::create([
                    'provider_course_id' => $course->id,
                    'location_id' => $location->id,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'start_time' => Carbon::createFromTime(9, 0),
                    'end_time' => Carbon::createFromTime(17, 0),
                    'available_spots' => $course->class_size_max ?? 12,
                    'booked_spots' => rand(0, min(5, ($course->class_size_max ?? 12) - 2)),
                    'is_full' => false,
                    'is_cancelled' => false,
                    'group_booking_available' => rand(0, 1) == 1,
                    'group_min_size' => rand(3, 5),
                    'group_discount_percentage' => rand(10, 25),
                ]);
            }
        }
    }

    private function createSampleReviews($users)
    {
        $courses = TrainingProviderCourse::all();

        foreach ($courses->take(15) as $course) {
            $reviewCount = rand(3, 8);
            $reviewUsers = $users->random(min($reviewCount, $users->count()));

            foreach ($reviewUsers as $user) {
                // Check if user already reviewed this course
                $existing = TrainingCourseReview::where('user_id', $user->id)
                    ->where('provider_course_id', $course->id)
                    ->first();

                if ($existing) {
                    continue;
                }

                $overallRating = rand(4, 5); // Mostly positive reviews

                TrainingCourseReview::create([
                    'user_id' => $user->id,
                    'provider_course_id' => $course->id,
                    'schedule_id' => $course->schedules->random()->id ?? null,
                    'rating_overall' => $overallRating,
                    'rating_content' => rand(4, 5),
                    'rating_instructor' => rand(4, 5),
                    'rating_facilities' => rand(3, 5),
                    'rating_value' => rand(3, 5),
                    'rating_administration' => rand(4, 5),
                    'would_recommend' => $overallRating >= 4,
                    'review_text' => $this->getReviewText($overallRating, $course->certification->name),
                    'liked_most' => $this->getLikedMost(),
                    'areas_for_improvement' => $overallRating < 5 ? $this->getAreasForImprovement() : null,
                    'tips_for_students' => $this->getTipsForStudents(),
                    'date_attended' => Carbon::now()->subDays(rand(1, 90)),
                    'is_verified_student' => true,
                    'is_approved' => true,
                ]);

                // Update course rating
                $this->updateCourseRating($course);
            }
        }
    }

    private function updateCourseRating($course)
    {
        $reviews = $course->reviews()->where('is_approved', true);
        $course->rating_avg = $reviews->avg('rating_overall') ?? 0;
        $course->review_count = $reviews->count();
        $course->save();

        // Update provider rating
        $provider = $course->provider;
        $providerReviews = TrainingCourseReview::whereHas('providerCourse', function ($q) use ($provider) {
            $q->where('provider_id', $provider->id);
        })->where('is_approved', true);
        $provider->rating_avg = $providerReviews->avg('rating_overall') ?? 0;
        $provider->total_reviews = $providerReviews->count();
        $provider->save();
    }

    private function createSampleUserCertifications($users, $certifications)
    {
        $commonCerts = $certifications->whereIn('slug', [
            'stcw-basic-safety-training',
            'eng1-medical-certificate',
            'security-awareness',
            'elementary-first-aid',
        ]);

        foreach ($users->take(8) as $user) {
            $certCount = rand(2, 4);
            $userCerts = $commonCerts->random(min($certCount, $commonCerts->count()));

            foreach ($userCerts as $cert) {
                $issueDate = Carbon::now()->subMonths(rand(6, 48));
                $validityMonths = $cert->validity_period_months ?? 60;
                $expiryDate = $issueDate->copy()->addMonths($validityMonths);

                TrainingUserCertification::create([
                    'user_id' => $user->id,
                    'certification_id' => $cert->id,
                    'provider_course_id' => TrainingProviderCourse::where('certification_id', $cert->id)
                        ->inRandomOrder()
                        ->first()?->id,
                    'issue_date' => $issueDate,
                    'expiry_date' => $expiryDate,
                    'status' => $expiryDate->isFuture() ? ($expiryDate->diffInMonths(now()) <= 3 ? 'expiring_soon' : 'valid') : 'expired',
                    'certificate_number' => 'CERT-' . strtoupper(substr(md5($user->id . $cert->id . time()), 0, 10)),
                    'issuing_authority' => TrainingProvider::inRandomOrder()->first()->name,
                    'notes' => rand(0, 1) == 1 ? 'Completed successfully' : null,
                ]);
            }
        }
    }

    private function createCourseBundles($providers)
    {
        foreach ($providers->take(3) as $provider) {
            $stcwCourse = TrainingProviderCourse::where('provider_id', $provider->id)
                ->whereHas('certification', function ($q) {
                    $q->where('slug', 'stcw-basic-safety-training');
                })
                ->first();

            $eng1Course = TrainingProviderCourse::where('provider_id', $provider->id)
                ->whereHas('certification', function ($q) {
                    $q->where('slug', 'eng1-medical-certificate');
                })
                ->first();

            if ($stcwCourse && $eng1Course) {
                $totalPrice = $stcwCourse->price + $eng1Course->price;
                $bundlePrice = $totalPrice * 0.85; // 15% bundle discount

                TrainingCourseBundle::create([
                    'provider_id' => $provider->id,
                    'name' => 'STCW + ENG1 Complete Package',
                    'description' => 'Complete entry-level certification package including STCW Basic Safety Training and ENG1 Medical Certificate. Perfect for new yacht crew members.',
                    'course_ids' => [$stcwCourse->id, $eng1Course->id],
                    'bundle_price' => $bundlePrice,
                    'bundle_discount_percentage' => 15,
                    'is_active' => true,
                ]);
            }
        }
    }

    // Helper methods for generating dummy content
    private function getCourseStructure($certName, $days)
    {
        return "Comprehensive {$days}-day course covering all aspects of {$certName}. The course combines theoretical knowledge with practical hands-on training to ensure you're fully prepared for your maritime career.";
    }

    private function getDailySchedule($days)
    {
        $schedule = [];
        for ($i = 1; $i <= $days; $i++) {
            $schedule["Day {$i}"] = "Day {$i} content covering key topics and practical exercises (8 hours)";
        }
        return $schedule;
    }

    private function getLearningOutcomes($certName)
    {
        return [
            "Understand all key concepts of {$certName}",
            "Demonstrate practical skills in real-world scenarios",
            "Apply knowledge in emergency situations",
            "Meet all regulatory requirements",
        ];
    }

    private function getSpecialFeatures($certName)
    {
        $features = [
            "State-of-the-art training facilities",
            "Experienced instructors with real-world experience",
            "Small class sizes for personalized attention",
            "Hands-on practical training",
            "Modern equipment and simulators",
        ];
        return implode(', ', array_slice($features, 0, rand(2, 4)));
    }

    private function getReviewText($rating, $certName)
    {
        $positiveReviews = [
            "Excellent course! The instructors were knowledgeable and the facilities were top-notch.",
            "Great experience overall. I learned a lot and felt well-prepared after completing this course.",
            "Highly recommend this {$certName} course. The practical training was especially valuable.",
            "Professional training with excellent support throughout. Worth every penny!",
            "Outstanding course content and delivery. The instructors made complex topics easy to understand.",
        ];

        $neutralReviews = [
            "Good course overall. Some areas could be improved but met my expectations.",
            "Decent training. The facilities were adequate and instructors were helpful.",
            "Satisfactory course. Covered all the necessary topics for {$certName}.",
        ];

        if ($rating >= 4) {
            return $positiveReviews[array_rand($positiveReviews)];
        }
        return $neutralReviews[array_rand($neutralReviews)];
    }

    private function getLikedMost()
    {
        $likes = [
            "The practical hands-on training sessions",
            "Knowledgeable and friendly instructors",
            "Well-organized course structure",
            "Modern training facilities and equipment",
            "Real-world scenarios and case studies",
        ];
        return $likes[array_rand($likes)];
    }

    private function getAreasForImprovement()
    {
        $improvements = [
            "Could use more practical exercises",
            "Some topics could be covered in more detail",
            "Better refreshment options during breaks",
            "More flexible scheduling options",
        ];
        return $improvements[array_rand($improvements)];
    }

    private function getTipsForStudents()
    {
        $tips = [
            "Come prepared and review the pre-course materials",
            "Take notes during practical sessions",
            "Ask questions - instructors are very helpful",
            "Practice the skills regularly after the course",
            "Bring comfortable clothing for practical exercises",
        ];
        return $tips[array_rand($tips)];
    }
}
