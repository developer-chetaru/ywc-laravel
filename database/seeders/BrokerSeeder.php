<?php

namespace Database\Seeders;

use App\Models\Broker;
use App\Models\BrokerReview;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BrokerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Note: Using updateOrCreate to avoid duplicates
        
        $brokers = [
            [
                'name' => 'Blue Water Crew Solutions',
                'business_name' => 'Blue Water Crew Solutions International',
                'type' => 'crew_placement_agency',
                'description' => 'International yacht crew placement agency specializing in motor yachts 40m+. 15 years of experience.',
                'primary_location' => 'Fort Lauderdale, Florida',
                'office_locations' => ['Fort Lauderdale', 'Antibes', 'Monaco', 'Palma'],
                'phone' => '+1 954-555-1234',
                'email' => 'placements@bluewatercrew.com',
                'website' => 'www.bluewatercrew.com',
                'specialties' => ['Motor yachts 40m+', 'Experienced crew', 'Officer positions', 'Rotational programs'],
                'fee_structure' => 'free_for_crew',
                'regions_served' => ['Mediterranean', 'Caribbean', 'USA East Coast'],
                'years_in_business' => '15',
                'is_myba_member' => true,
                'is_licensed' => true,
                'is_verified' => true,
                'certifications' => ['MYBA Member', 'Licensed agency'],
                'average_placement_time' => '3-6 weeks',
                'positions_per_month' => rand(25, 40),
                'success_rate' => 87.5,
            ],
            [
                'name' => 'Elite Yacht Crew',
                'business_name' => 'Elite Yacht Crew Agency',
                'type' => 'crew_placement_agency',
                'description' => 'Premium crew placement for luxury yachts. Specializing in senior positions and exclusive opportunities.',
                'primary_location' => 'Monaco',
                'office_locations' => ['Monaco', 'Antibes', 'Palma'],
                'phone' => '+377 93 50 56 78',
                'email' => 'info@eliteyachtcrew.com',
                'website' => 'www.eliteyachtcrew.com',
                'specialties' => ['Senior positions', 'Large yachts 50m+', 'Private yachts', 'Career advancement'],
                'fee_structure' => 'free_for_crew',
                'regions_served' => ['Mediterranean', 'Caribbean'],
                'years_in_business' => '12',
                'is_myba_member' => true,
                'is_licensed' => true,
                'is_verified' => true,
                'certifications' => ['MYBA Member'],
                'average_placement_time' => '4-8 weeks',
                'positions_per_month' => rand(15, 30),
                'success_rate' => 85.0,
            ],
            [
                'name' => 'Ocean Placements',
                'business_name' => 'Ocean Placements Ltd',
                'type' => 'crew_placement_agency',
                'description' => 'Full-service crew placement for all yacht types and positions.',
                'primary_location' => 'Southampton, UK',
                'office_locations' => ['Southampton', 'London'],
                'phone' => '+44 23 8022 3456',
                'email' => 'crew@oceanplacements.co.uk',
                'website' => 'www.oceanplacements.co.uk',
                'specialties' => ['All yacht types', 'Entry level', 'Interior positions'],
                'fee_structure' => 'crew_pays',
                'regions_served' => ['UK', 'Europe', 'Mediterranean'],
                'years_in_business' => '8',
                'is_myba_member' => false,
                'is_licensed' => true,
                'is_verified' => true,
                'certifications' => ['Licensed agency'],
                'average_placement_time' => '6-10 weeks',
                'positions_per_month' => rand(20, 35),
                'success_rate' => 75.0,
            ],
            [
                'name' => 'Mediterranean Crew Services',
                'business_name' => 'Mediterranean Crew Services',
                'type' => 'yacht_management',
                'description' => 'Yacht management and crew placement services throughout the Mediterranean.',
                'primary_location' => 'Antibes, France',
                'office_locations' => ['Antibes', 'Monaco'],
                'phone' => '+33 4 93 67 12 34',
                'email' => 'info@medcrewservices.com',
                'website' => 'www.medcrewservices.com',
                'specialties' => ['Yacht management', 'Crew placement', 'Mediterranean expertise'],
                'fee_structure' => 'yacht_pays',
                'regions_served' => ['Mediterranean'],
                'years_in_business' => '10',
                'is_myba_member' => true,
                'is_licensed' => true,
                'is_verified' => true,
                'certifications' => ['MYBA Member'],
                'average_placement_time' => '2-5 weeks',
                'positions_per_month' => rand(30, 50),
                'success_rate' => 82.0,
            ],
        ];

        $users = User::take(15)->get();

        foreach ($brokers as $index => $brokerData) {
            $broker = Broker::updateOrCreate(
                ['slug' => Str::slug($brokerData['name'])],
                [
                'name' => $brokerData['name'],
                'slug' => Str::slug($brokerData['name']),
                'business_name' => $brokerData['business_name'],
                'type' => $brokerData['type'],
                'description' => $brokerData['description'],
                'primary_location' => $brokerData['primary_location'],
                'office_locations' => $brokerData['office_locations'],
                'phone' => $brokerData['phone'],
                'email' => $brokerData['email'],
                'website' => $brokerData['website'],
                'specialties' => $brokerData['specialties'],
                'fee_structure' => $brokerData['fee_structure'],
                'regions_served' => $brokerData['regions_served'],
                'years_in_business' => $brokerData['years_in_business'],
                'is_myba_member' => $brokerData['is_myba_member'],
                'is_licensed' => $brokerData['is_licensed'],
                'is_verified' => $brokerData['is_verified'],
                'certifications' => $brokerData['certifications'],
                'average_placement_time' => $brokerData['average_placement_time'],
                'positions_per_month' => $brokerData['positions_per_month'],
                'success_rate' => $brokerData['success_rate'],
                'logo' => $this->getBrokerLogo($index),
                ]
            );

            // Create 8-15 reviews for each broker (only if broker is new or has no reviews)
            if ($broker->reviews()->count() == 0) {
                $reviewCount = rand(8, 15);
                for ($i = 0; $i < $reviewCount; $i++) {
                    $user = $users->random();
                    $overallRating = rand(3, 5);
                    
                    BrokerReview::create([
                    'broker_id' => $broker->id,
                    'user_id' => $user->id,
                    'title' => $this->getRandomReviewTitle(),
                    'review' => $this->getRandomReviewText(),
                    'overall_rating' => $overallRating,
                    'job_quality_rating' => rand($overallRating - 1, 5),
                    'communication_rating' => rand($overallRating - 1, 5),
                    'professionalism_rating' => rand($overallRating - 1, 5),
                    'fees_transparency_rating' => rand(3, 5),
                    'support_rating' => rand($overallRating - 1, 5),
                    'would_use_again' => rand(0, 10) > 2,
                    'would_recommend' => rand(0, 10) > 1,
                    'is_anonymous' => rand(0, 10) > 7,
                    'is_verified' => true,
                    'placement_date' => now()->subDays(rand(1, 730)),
                    'position_placed' => ['Chief Stew', 'Deckhand', 'Chef', 'Engineer', 'Bosun'][rand(0, 4)],
                    'yacht_name' => 'M/Y ' . ['Serenity', 'Azure', 'Ocean', 'Sea', 'Blue'][rand(0, 4)],
                    'placement_timeframe' => rand(2, 8) . ' weeks',
                    'is_approved' => true,
                    ]);
                }
            }

            // Update broker rating stats
            $broker->updateRatingStats();
        }
    }

    private function getRandomReviewTitle(): string
    {
        $titles = [
            'Excellent service and support',
            'Found me the perfect position',
            'Professional and responsive',
            'Great communication throughout',
            'Highly recommended agency',
            'Quick placement, good support',
            'Very satisfied with service',
            'Top quality agency',
        ];

        return $titles[array_rand($titles)];
    }

    private function getRandomReviewText(): string
    {
        $reviews = [
            'Excellent service from start to finish. The team was professional and found me the perfect position quickly.',
            'Great communication throughout the placement process. Very satisfied with the service provided.',
            'Professional agency with good knowledge of the industry. Would definitely use again.',
            'Quick placement and excellent follow-up support. Highly recommended.',
            'Very responsive and helpful. Found me a great position that matched my experience.',
            'Professional service with good communication. Fair process and good results.',
            'Excellent agency with great industry connections. Highly recommended for crew placement.',
            'Good service and support. Found a position that was a good fit for my career goals.',
        ];

        return $reviews[array_rand($reviews)];
    }

    private function getBrokerLogo($index): ?string
    {
        // Use placeholder images for brokers
        $logos = [
            'https://images.unsplash.com/photo-1556761175-5973dc0f32e7?w=400&h=300&fit=crop',
            'https://images.unsplash.com/photo-1552664730-d307ca884978?w=400&h=300&fit=crop',
            'https://images.unsplash.com/photo-1521791136064-7986c2920216?w=400&h=300&fit=crop',
            'https://images.unsplash.com/photo-1556761175-4b46a572b786?w=400&h=300&fit=crop',
        ];

        return $logos[$index % count($logos)] ?? null;
    }
}
