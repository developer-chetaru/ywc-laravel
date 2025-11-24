<?php

namespace Database\Seeders;

use App\Models\Yacht;
use App\Models\Marina;
use App\Models\YachtReview;
use App\Models\MarinaReview;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IndustryReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Remove previous data before seeding
        $this->command->info('Removing previous review data...');
        
        // Delete all yacht reviews
        $yachtReviewsDeleted = YachtReview::query()->delete();
        $this->command->info("Deleted {$yachtReviewsDeleted} yacht reviews.");
        
        // Delete all marina reviews
        $marinaReviewsDeleted = MarinaReview::query()->delete();
        $this->command->info("Deleted {$marinaReviewsDeleted} marina reviews.");
        
        // Reset rating statistics for all yachts
        DB::table('yachts')->update([
            'rating_avg' => 0,
            'rating_count' => 0,
            'reviews_count' => 0,
            'recommendation_percentage' => 0,
            'updated_at' => now(),
        ]);
        $this->command->info('Reset yacht rating statistics.');
        
        // Reset rating statistics for all marinas
        DB::table('marinas')->update([
            'rating_avg' => 0,
            'rating_count' => 0,
            'reviews_count' => 0,
            'updated_at' => now(),
        ]);
        $this->command->info('Reset marina rating statistics.');
        
        // Get or create some users for reviews
        $users = User::take(10)->get();
        if ($users->count() < 3) {
            // Create some test users if needed
            $users = User::factory(5)->create();
        }

        // Get yachts and marinas
        $yachts = Yacht::all();
        $marinas = Marina::all();

        if ($yachts->isEmpty() || $marinas->isEmpty()) {
            $this->command->warn('Please run YachtSeeder and MarinaSeeder first!');
            return;
        }

        // Create yacht reviews
        $yachtReviewTemplates = [
            [
                'title' => 'Excellent working conditions and professional crew',
                'review' => 'I had an amazing experience working on this yacht. The management was professional, the crew was supportive, and the working conditions were excellent. The yacht itself is well-maintained and the amenities are top-notch. Highly recommend for anyone looking for a great work environment.',
                'pros' => 'Professional management, supportive crew, excellent amenities, well-maintained vessel, good work-life balance',
                'cons' => 'Long hours during peak season, limited time off during charters',
                'overall_rating' => 5,
                'management_rating' => 5,
                'working_conditions_rating' => 5,
                'compensation_rating' => 4,
                'crew_welfare_rating' => 5,
                'yacht_condition_rating' => 5,
                'career_development_rating' => 4,
                'would_recommend' => true,
                'is_anonymous' => false,
                'position_held' => 'Chief Stewardess',
            ],
            [
                'title' => 'Great yacht but management could improve',
                'review' => 'The yacht is beautiful and well-equipped. The crew is friendly and hardworking. However, management communication could be better, and the schedule can be unpredictable. Overall, it\'s a good place to work if you can handle the occasional chaos.',
                'pros' => 'Beautiful yacht, friendly crew, good location, decent compensation',
                'cons' => 'Poor communication from management, unpredictable schedule, limited training opportunities',
                'overall_rating' => 4,
                'management_rating' => 3,
                'working_conditions_rating' => 4,
                'compensation_rating' => 4,
                'crew_welfare_rating' => 4,
                'yacht_condition_rating' => 5,
                'career_development_rating' => 3,
                'would_recommend' => true,
                'is_anonymous' => false,
                'position_held' => 'Deckhand',
            ],
            [
                'title' => 'Outstanding experience, highly recommended',
                'review' => 'This has been one of the best yachts I\'ve worked on. The owners are respectful, the management is organized, and the crew works well together. The yacht is immaculate and the itinerary is always interesting. I\'ve learned a lot and made great connections.',
                'pros' => 'Respectful owners, organized management, great crew dynamics, interesting itineraries, learning opportunities',
                'cons' => 'None significant',
                'overall_rating' => 5,
                'management_rating' => 5,
                'working_conditions_rating' => 5,
                'compensation_rating' => 5,
                'crew_welfare_rating' => 5,
                'yacht_condition_rating' => 5,
                'career_development_rating' => 5,
                'would_recommend' => true,
                'is_anonymous' => false,
                'position_held' => 'Chef',
            ],
            [
                'title' => 'Good yacht with room for improvement',
                'review' => 'The yacht is nice and the location is great. The crew is okay, but there\'s some tension between departments. Management tries their best but could be more proactive. Compensation is fair for the industry.',
                'pros' => 'Nice yacht, great location, fair compensation',
                'cons' => 'Departmental tension, reactive management, limited advancement',
                'overall_rating' => 3,
                'management_rating' => 3,
                'working_conditions_rating' => 3,
                'compensation_rating' => 3,
                'crew_welfare_rating' => 3,
                'yacht_condition_rating' => 4,
                'career_development_rating' => 2,
                'would_recommend' => false,
                'is_anonymous' => true,
                'position_held' => 'Engineer',
            ],
            [
                'title' => 'Professional operation with excellent standards',
                'review' => 'This yacht operates at a very high standard. The management is professional, the crew is experienced, and the yacht is maintained impeccably. The work can be demanding but it\'s rewarding. Great for career development.',
                'pros' => 'High standards, professional management, experienced crew, excellent maintenance, career development',
                'cons' => 'Demanding workload, high expectations',
                'overall_rating' => 5,
                'management_rating' => 5,
                'working_conditions_rating' => 4,
                'compensation_rating' => 5,
                'crew_welfare_rating' => 4,
                'yacht_condition_rating' => 5,
                'career_development_rating' => 5,
                'would_recommend' => true,
                'is_anonymous' => false,
                'position_held' => 'First Officer',
            ],
        ];

        foreach ($yachts as $yacht) {
            $reviewsToCreate = rand(2, 4);
            for ($i = 0; $i < $reviewsToCreate; $i++) {
                $template = $yachtReviewTemplates[array_rand($yachtReviewTemplates)];
                $user = $users->random();
                
                YachtReview::create([
                    'yacht_id' => $yacht->id,
                    'user_id' => $user->id,
                    'title' => $template['title'],
                    'review' => $template['review'],
                    'pros' => $template['pros'],
                    'cons' => $template['cons'],
                    'overall_rating' => $template['overall_rating'],
                    'management_rating' => $template['management_rating'],
                    'working_conditions_rating' => $template['working_conditions_rating'],
                    'compensation_rating' => $template['compensation_rating'],
                    'crew_welfare_rating' => $template['crew_welfare_rating'],
                    'yacht_condition_rating' => $template['yacht_condition_rating'],
                    'career_development_rating' => $template['career_development_rating'],
                    'would_recommend' => $template['would_recommend'],
                    'is_anonymous' => $template['is_anonymous'],
                    'is_verified' => true,
                    'is_approved' => true,
                    'position_held' => $template['position_held'],
                    'work_start_date' => now()->subMonths(rand(6, 24)),
                    'work_end_date' => now()->subMonths(rand(0, 6)),
                    'helpful_count' => rand(0, 15),
                    'not_helpful_count' => rand(0, 3),
                    'created_at' => now()->subDays(rand(1, 90)),
                ]);
            }
            
            // Update yacht rating stats
            $yacht->updateRatingStats();
        }

        // Create marina reviews
        $marinaReviewTemplates = [
            [
                'title' => 'Excellent facilities and friendly staff',
                'review' => 'This marina has everything you need. The facilities are clean and well-maintained, the staff is helpful and friendly, and the location is perfect. Fuel prices are reasonable and the services are top-notch. Highly recommend stopping here.',
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
                'is_anonymous' => false,
            ],
            [
                'title' => 'Good marina with minor issues',
                'review' => 'Overall a decent marina. The location is good and the basic facilities work. However, the WiFi is spotty and the showers could be cleaner. Staff is friendly but sometimes hard to reach. Price is fair for the area.',
                'tips_tricks' => 'Bring your own WiFi hotspot, shower early in the morning for best water pressure',
                'overall_rating' => 4,
                'fuel_rating' => 4,
                'water_rating' => 4,
                'electricity_rating' => 4,
                'wifi_rating' => 2,
                'showers_rating' => 3,
                'laundry_rating' => 4,
                'maintenance_rating' => 4,
                'provisioning_rating' => 3,
                'staff_rating' => 4,
                'value_rating' => 4,
                'protection_rating' => 4,
                'is_anonymous' => false,
            ],
            [
                'title' => 'Outstanding marina experience',
                'review' => 'One of the best marinas I\'ve visited. Everything is perfect - clean facilities, excellent staff, great location, and reasonable prices. The protection from weather is excellent and the amenities are first-class. Will definitely return.',
                'tips_tricks' => 'Try the local seafood restaurant nearby, the marina office has great local knowledge, free pump-out service',
                'overall_rating' => 5,
                'fuel_rating' => 5,
                'water_rating' => 5,
                'electricity_rating' => 5,
                'wifi_rating' => 5,
                'showers_rating' => 5,
                'laundry_rating' => 5,
                'maintenance_rating' => 5,
                'provisioning_rating' => 5,
                'staff_rating' => 5,
                'value_rating' => 5,
                'protection_rating' => 5,
                'is_anonymous' => false,
            ],
            [
                'title' => 'Average marina, nothing special',
                'review' => 'The marina is okay but nothing exceptional. Facilities are basic and functional. Staff is present but not particularly helpful. Location is convenient. Price is average. Would use again if needed but wouldn\'t go out of my way.',
                'tips_tricks' => 'Check fuel quality before filling, bring cash for some services',
                'overall_rating' => 3,
                'fuel_rating' => 3,
                'water_rating' => 3,
                'electricity_rating' => 3,
                'wifi_rating' => 3,
                'showers_rating' => 3,
                'laundry_rating' => 3,
                'maintenance_rating' => 3,
                'provisioning_rating' => 2,
                'staff_rating' => 3,
                'value_rating' => 3,
                'protection_rating' => 3,
                'is_anonymous' => true,
            ],
            [
                'title' => 'Great value and excellent service',
                'review' => 'This marina offers great value for money. The facilities are clean, the staff is helpful, and the location is convenient. While not the most luxurious, it has everything you need at a fair price. Good protection and easy access.',
                'tips_tricks' => 'Best rates for weekly stays, ask about local fishing spots, grocery store within walking distance',
                'overall_rating' => 4,
                'fuel_rating' => 4,
                'water_rating' => 4,
                'electricity_rating' => 4,
                'wifi_rating' => 3,
                'showers_rating' => 4,
                'laundry_rating' => 4,
                'maintenance_rating' => 4,
                'provisioning_rating' => 4,
                'staff_rating' => 4,
                'value_rating' => 5,
                'protection_rating' => 4,
                'is_anonymous' => false,
            ],
        ];

        foreach ($marinas as $marina) {
            $reviewsToCreate = rand(2, 4);
            for ($i = 0; $i < $reviewsToCreate; $i++) {
                $template = $marinaReviewTemplates[array_rand($marinaReviewTemplates)];
                $user = $users->random();
                
                MarinaReview::create([
                    'marina_id' => $marina->id,
                    'user_id' => $user->id,
                    'title' => $template['title'],
                    'review' => $template['review'],
                    'tips_tricks' => $template['tips_tricks'],
                    'overall_rating' => $template['overall_rating'],
                    'fuel_rating' => $template['fuel_rating'],
                    'water_rating' => $template['water_rating'],
                    'electricity_rating' => $template['electricity_rating'],
                    'wifi_rating' => $template['wifi_rating'],
                    'showers_rating' => $template['showers_rating'],
                    'laundry_rating' => $template['laundry_rating'],
                    'maintenance_rating' => $template['maintenance_rating'],
                    'provisioning_rating' => $template['provisioning_rating'],
                    'staff_rating' => $template['staff_rating'],
                    'value_rating' => $template['value_rating'],
                    'protection_rating' => $template['protection_rating'],
                    'is_anonymous' => $template['is_anonymous'],
                    'is_verified' => true,
                    'is_approved' => true,
                    'visit_date' => now()->subDays(rand(1, 180)),
                    'yacht_length_meters' => rand(10, 50) . 'm',
                    'helpful_count' => rand(0, 12),
                    'not_helpful_count' => rand(0, 2),
                    'created_at' => now()->subDays(rand(1, 90)),
                ]);
            }
            
            // Update marina rating stats
            $marina->updateRatingStats();
        }

        $this->command->info('Industry Review System seeded successfully!');
        $this->command->info('Created reviews for ' . $yachts->count() . ' yachts and ' . $marinas->count() . ' marinas.');
    }
}

