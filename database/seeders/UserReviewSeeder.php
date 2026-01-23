<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Yacht;
use App\Models\Marina;
use App\Models\Restaurant;
use App\Models\Contractor;
use App\Models\YachtReview;
use App\Models\MarinaReview;
use App\Models\RestaurantReview;
use App\Models\ContractorReview;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = 'llewellyn.goldner@mailinator.com';
        
        // Get or create the user
        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'first_name' => 'Llewellyn',
                'last_name' => 'Goldner',
                'email_verified_at' => now(),
                'password' => bcrypt('password'),
            ]
        );

        $this->command->info("Using user: {$user->email} (ID: {$user->id})");

        // Get available entities
        $yachts = Yacht::all();
        $marinas = Marina::all();
        $restaurants = Restaurant::all();
        $contractors = Contractor::all();

        if ($yachts->isEmpty() && $marinas->isEmpty() && $restaurants->isEmpty() && $contractors->isEmpty()) {
            $this->command->warn('No yachts, marinas, restaurants, or contractors found. Please run their seeders first!');
            return;
        }

        $reviewsCreated = 0;
        $reviewTypes = [];

        // Create 3-4 Yacht Reviews
        if ($yachts->isNotEmpty()) {
            $yachtCount = min(4, $yachts->count());
            $selectedYachts = $yachts->random($yachtCount);
            
            $yachtTemplates = [
                [
                    'title' => 'Amazing experience on this beautiful yacht',
                    'review' => 'I had an incredible time working on this yacht. The management team was professional and supportive, the crew was like family, and the working conditions were excellent. The yacht itself is stunning and well-maintained. The compensation was fair and the work-life balance was better than expected.',
                    'pros' => 'Professional management, supportive crew, excellent amenities, well-maintained vessel, good work-life balance, fair compensation',
                    'cons' => 'Long hours during peak season, limited time off during charters',
                    'overall_rating' => 5,
                    'yacht_quality_rating' => 5,
                    'crew_culture_rating' => 5,
                    'management_rating' => 5,
                    'benefits_rating' => 4,
                    'working_conditions_rating' => 5,
                    'compensation_rating' => 4,
                    'crew_welfare_rating' => 5,
                    'yacht_condition_rating' => 5,
                    'career_development_rating' => 4,
                    'would_recommend' => true,
                    'position_held' => 'Chief Stewardess',
                ],
                [
                    'title' => 'Great yacht with excellent crew culture',
                    'review' => 'This yacht has a wonderful crew culture. Everyone works together as a team and supports each other. The yacht is beautiful and the owners are respectful. Management is organized and communication is clear. The only downside is the occasional long hours, but that\'s expected in this industry.',
                    'pros' => 'Excellent crew culture, beautiful yacht, respectful owners, organized management, clear communication',
                    'cons' => 'Occasional long hours, unpredictable schedule sometimes',
                    'overall_rating' => 4,
                    'yacht_quality_rating' => 4,
                    'crew_culture_rating' => 5,
                    'management_rating' => 4,
                    'benefits_rating' => 4,
                    'working_conditions_rating' => 4,
                    'compensation_rating' => 4,
                    'crew_welfare_rating' => 5,
                    'yacht_condition_rating' => 4,
                    'career_development_rating' => 3,
                    'would_recommend' => true,
                    'position_held' => 'Deckhand',
                ],
                [
                    'title' => 'Professional operation with high standards',
                    'review' => 'This yacht operates at a very high standard. The management is professional and experienced, the crew is well-trained, and the yacht is maintained impeccably. The work can be demanding but it\'s rewarding and you learn a lot. Great for career development.',
                    'pros' => 'High standards, professional management, well-trained crew, excellent maintenance, career development opportunities',
                    'cons' => 'Demanding workload, high expectations',
                    'overall_rating' => 5,
                    'yacht_quality_rating' => 5,
                    'crew_culture_rating' => 4,
                    'management_rating' => 5,
                    'benefits_rating' => 5,
                    'working_conditions_rating' => 4,
                    'compensation_rating' => 5,
                    'crew_welfare_rating' => 4,
                    'yacht_condition_rating' => 5,
                    'career_development_rating' => 5,
                    'would_recommend' => true,
                    'position_held' => 'First Officer',
                ],
            ];

            foreach ($selectedYachts as $yacht) {
                $template = $yachtTemplates[array_rand($yachtTemplates)];
                $workStartDate = now()->subMonths(rand(6, 24))->subDays(rand(0, 30));
                
                YachtReview::firstOrCreate(
                    [
                        'yacht_id' => $yacht->id,
                        'user_id' => $user->id,
                        'work_start_date' => $workStartDate,
                    ],
                    array_merge($template, [
                        'is_anonymous' => false,
                        'is_verified' => true,
                        'is_approved' => true,
                        'work_end_date' => now()->subMonths(rand(0, 6)),
                        'helpful_count' => rand(2, 20),
                        'not_helpful_count' => rand(0, 3),
                        'created_at' => now()->subDays(rand(1, 90)),
                    ])
                );
                $reviewsCreated++;
                $reviewTypes[] = 'Yacht';
                $yacht->updateRatingStats();
            }
        }

        // Create 2-3 Marina Reviews
        if ($marinas->isNotEmpty() && $reviewsCreated < 10) {
            $marinaCount = min(3, $marinas->count(), 10 - $reviewsCreated);
            $selectedMarinas = $marinas->random($marinaCount);
            
            $marinaTemplates = [
                [
                    'title' => 'Excellent marina with top-notch facilities',
                    'review' => 'This marina has everything you need and more. The facilities are clean and well-maintained, the staff is helpful and friendly, and the location is perfect. Fuel prices are reasonable and all services work perfectly. Highly recommend stopping here.',
                    'tips_tricks' => 'Book ahead during peak season, the restaurant on-site is excellent, ask about local weather patterns',
                    'overall_rating' => 5,
                    'fuel_rating' => 5,
                    'water_rating' => 5,
                    'electricity_rating' => 5,
                    'wifi_rating' => 4,
                    'showers_rating' => 5,
                    'laundry_rating' => 5,
                    'maintenance_rating' => 5,
                    'provisioning_rating' => 4,
                    'staff_rating' => 5,
                    'value_rating' => 4,
                    'protection_rating' => 5,
                ],
                [
                    'title' => 'Good marina with reliable services',
                    'review' => 'Overall a decent marina. The location is good and the basic facilities work well. The WiFi is okay but could be better. Staff is friendly and helpful. Price is fair for the area. Would use again.',
                    'tips_tricks' => 'Bring your own WiFi hotspot for better connection, shower early for best water pressure',
                    'overall_rating' => 4,
                    'fuel_rating' => 4,
                    'water_rating' => 4,
                    'electricity_rating' => 4,
                    'wifi_rating' => 3,
                    'showers_rating' => 4,
                    'laundry_rating' => 4,
                    'maintenance_rating' => 4,
                    'provisioning_rating' => 3,
                    'staff_rating' => 4,
                    'value_rating' => 4,
                    'protection_rating' => 4,
                ],
            ];

            foreach ($selectedMarinas as $marina) {
                if ($reviewsCreated >= 10) break;
                
                $template = $marinaTemplates[array_rand($marinaTemplates)];
                $visitDate = now()->subDays(rand(1, 180));
                
                MarinaReview::firstOrCreate(
                    [
                        'marina_id' => $marina->id,
                        'user_id' => $user->id,
                        'visit_date' => $visitDate,
                    ],
                    array_merge($template, [
                        'is_anonymous' => false,
                        'is_verified' => true,
                        'is_approved' => true,
                        'yacht_length_meters' => rand(15, 50) . 'm',
                        'helpful_count' => rand(1, 15),
                        'not_helpful_count' => rand(0, 2),
                        'created_at' => now()->subDays(rand(1, 90)),
                    ])
                );
                $reviewsCreated++;
                $reviewTypes[] = 'Marina';
                $marina->updateRatingStats();
            }
        }

        // Create 2-3 Restaurant Reviews
        if ($restaurants->isNotEmpty() && $reviewsCreated < 10) {
            $restaurantCount = min(3, $restaurants->count(), 10 - $reviewsCreated);
            $selectedRestaurants = $restaurants->random($restaurantCount);
            
            $restaurantTemplates = [
                [
                    'title' => 'Amazing food and great service',
                    'review' => 'This restaurant is fantastic! The food is delicious, the service is excellent, and the atmosphere is perfect. Great place for crew to unwind after work. The prices are reasonable and the portions are generous. Highly recommend!',
                    'crew_tips' => 'Try the seafood special, great happy hour deals, ask for crew discount',
                    'overall_rating' => 5,
                    'food_rating' => 5,
                    'service_rating' => 5,
                    'atmosphere_rating' => 5,
                    'value_rating' => 4,
                    'would_recommend' => true,
                ],
                [
                    'title' => 'Good food with friendly staff',
                    'review' => 'Nice restaurant with good food and friendly staff. The atmosphere is pleasant and the prices are fair. Service can be a bit slow during peak hours but overall a good experience. Would visit again.',
                    'crew_tips' => 'Best to visit during off-peak hours, try the local specialties',
                    'overall_rating' => 4,
                    'food_rating' => 4,
                    'service_rating' => 3,
                    'atmosphere_rating' => 4,
                    'value_rating' => 4,
                    'would_recommend' => true,
                ],
            ];

            foreach ($selectedRestaurants as $restaurant) {
                if ($reviewsCreated >= 10) break;
                
                $template = $restaurantTemplates[array_rand($restaurantTemplates)];
                $visitDate = now()->subDays(rand(1, 120));
                
                RestaurantReview::firstOrCreate(
                    [
                        'restaurant_id' => $restaurant->id,
                        'user_id' => $user->id,
                        'visit_date' => $visitDate,
                    ],
                    array_merge($template, [
                        'is_anonymous' => false,
                        'is_verified' => true,
                        'is_approved' => true,
                        'helpful_count' => rand(1, 12),
                        'not_helpful_count' => rand(0, 2),
                        'created_at' => now()->subDays(rand(1, 90)),
                    ])
                );
                $reviewsCreated++;
                $reviewTypes[] = 'Restaurant';
                $restaurant->updateRatingStats();
            }
        }

        // Create 1-2 Contractor Reviews
        if ($contractors->isNotEmpty() && $reviewsCreated < 10) {
            $contractorCount = min(2, $contractors->count(), 10 - $reviewsCreated);
            $selectedContractors = $contractors->random($contractorCount);
            
            $contractorTemplates = [
                [
                    'title' => 'Professional service and excellent work',
                    'review' => 'This contractor provided excellent service. They were professional, timely, and the quality of work was outstanding. The pricing was fair and they communicated well throughout the project. Would definitely hire again.',
                    'service_type' => 'Maintenance',
                    'service_cost' => rand(500, 5000),
                    'timeframe' => rand(1, 7) . ' days',
                    'overall_rating' => 5,
                    'quality_rating' => 5,
                    'professionalism_rating' => 5,
                    'pricing_rating' => 4,
                    'timeliness_rating' => 5,
                    'would_recommend' => true,
                    'would_hire_again' => true,
                ],
                [
                    'title' => 'Good contractor with reliable service',
                    'review' => 'Good contractor who did the job well. They were professional and the work quality was good. The pricing was reasonable. Would recommend for similar projects.',
                    'service_type' => 'Repair',
                    'service_cost' => rand(300, 3000),
                    'timeframe' => rand(1, 5) . ' days',
                    'overall_rating' => 4,
                    'quality_rating' => 4,
                    'professionalism_rating' => 4,
                    'pricing_rating' => 4,
                    'timeliness_rating' => 4,
                    'would_recommend' => true,
                    'would_hire_again' => true,
                ],
            ];

            foreach ($selectedContractors as $contractor) {
                if ($reviewsCreated >= 10) break;
                
                $template = $contractorTemplates[array_rand($contractorTemplates)];
                $serviceDate = now()->subDays(rand(1, 180));
                
                ContractorReview::firstOrCreate(
                    [
                        'contractor_id' => $contractor->id,
                        'user_id' => $user->id,
                        'service_date' => $serviceDate,
                    ],
                    array_merge($template, [
                        'is_anonymous' => false,
                        'is_verified' => true,
                        'is_approved' => true,
                        'yacht_name' => 'M/Y ' . Str::random(8),
                        'helpful_count' => rand(1, 10),
                        'not_helpful_count' => rand(0, 2),
                        'created_at' => now()->subDays(rand(1, 90)),
                    ])
                );
                $reviewsCreated++;
                $reviewTypes[] = 'Contractor';
                $contractor->updateRatingStats();
            }
        }

        $this->command->info("Successfully created {$reviewsCreated} reviews for user: {$email}");
        $this->command->info("Review types: " . implode(', ', array_count_values($reviewTypes)));
    }
}
