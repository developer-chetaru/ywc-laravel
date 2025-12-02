<?php

namespace Database\Seeders;

use App\Models\Restaurant;
use App\Models\RestaurantReview;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RestaurantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Note: Using updateOrCreate to avoid duplicates
        
        $restaurants = [
            [
                'name' => 'The Crew Galley',
                'type' => 'restaurant',
                'description' => 'Crew-friendly restaurant with great food and reasonable prices. Popular with yacht crew.',
                'address' => '123 Harbor Drive',
                'city' => 'Fort Lauderdale',
                'country' => 'United States',
                'phone' => '+1 954-555-7890',
                'email' => 'info@crewgalley.com',
                'website' => 'www.crewgalley.com',
                'cuisine_type' => ['American', 'Seafood'],
                'price_range' => '€€',
                'opening_hours' => ['Mon-Sun: 11:00-23:00'],
                'crew_friendly' => true,
                'crew_discount' => true,
                'crew_discount_details' => '15% discount for yacht crew with ID',
                'is_verified' => true,
            ],
            [
                'name' => 'Le Deck',
                'type' => 'bar',
                'description' => 'Popular crew hangout bar in Antibes. Great atmosphere and crew discounts.',
                'address' => '45 Rue de la République',
                'city' => 'Antibes',
                'country' => 'France',
                'phone' => '+33 4 93 67 45 67',
                'email' => 'info@ledeck.fr',
                'website' => 'www.ledeck.fr',
                'cuisine_type' => ['Bar food', 'Tapas'],
                'price_range' => '€€',
                'opening_hours' => ['Mon-Sun: 18:00-02:00'],
                'crew_friendly' => true,
                'crew_discount' => true,
                'crew_discount_details' => 'Happy hour prices for crew',
                'is_verified' => true,
            ],
            [
                'name' => 'Crew Corner Cafe',
                'type' => 'cafe',
                'description' => 'Cozy cafe popular with crew. Great coffee and breakfast options.',
                'address' => '78 Marina Road',
                'city' => 'Monaco',
                'country' => 'Monaco',
                'phone' => '+377 93 50 78 90',
                'email' => 'hello@crewcorner.mc',
                'website' => 'www.crewcorner.mc',
                'cuisine_type' => ['Cafe', 'Breakfast', 'Light meals'],
                'price_range' => '€',
                'opening_hours' => ['Mon-Sun: 07:00-18:00'],
                'crew_friendly' => true,
                'crew_discount' => false,
                'crew_discount_details' => null,
                'is_verified' => true,
            ],
            [
                'name' => 'Marina Provisions',
                'type' => 'shop',
                'description' => 'Well-stocked shop for crew essentials. Good prices and convenient location.',
                'address' => '12 Harbor Street',
                'city' => 'Palma',
                'country' => 'Spain',
                'phone' => '+34 971 23 45 67',
                'email' => 'info@marinaprovisions.es',
                'website' => 'www.marinaprovisions.es',
                'cuisine_type' => null,
                'price_range' => '€€',
                'opening_hours' => ['Mon-Sat: 09:00-20:00', 'Sun: 10:00-18:00'],
                'crew_friendly' => true,
                'crew_discount' => true,
                'crew_discount_details' => '10% discount for yacht crew',
                'is_verified' => true,
            ],
            [
                'name' => 'Yacht Laundry Service',
                'type' => 'service',
                'description' => 'Professional laundry service for yacht crew. Fast turnaround and reasonable prices.',
                'address' => '56 Service Road',
                'city' => 'Fort Lauderdale',
                'country' => 'United States',
                'phone' => '+1 954-555-3456',
                'email' => 'service@yachtlaundry.com',
                'website' => 'www.yachtlaundry.com',
                'cuisine_type' => null,
                'price_range' => '€€',
                'opening_hours' => ['Mon-Sat: 08:00-18:00'],
                'crew_friendly' => true,
                'crew_discount' => true,
                'crew_discount_details' => 'Bulk discounts for crew',
                'is_verified' => true,
            ],
            [
                'name' => 'Harbor View Restaurant',
                'type' => 'restaurant',
                'description' => 'Upscale restaurant with great views. Popular for crew celebrations.',
                'address' => '89 Waterfront Avenue',
                'city' => 'Antibes',
                'country' => 'France',
                'phone' => '+33 4 93 67 89 01',
                'email' => 'reservations@harborview.fr',
                'website' => 'www.harborview.fr',
                'cuisine_type' => ['French', 'Mediterranean'],
                'price_range' => '€€€',
                'opening_hours' => ['Tue-Sun: 19:00-23:00'],
                'crew_friendly' => false,
                'crew_discount' => false,
                'crew_discount_details' => null,
                'is_verified' => true,
            ],
        ];

        $users = User::take(12)->get();

        foreach ($restaurants as $index => $restaurantData) {
            $restaurant = Restaurant::updateOrCreate(
                ['slug' => Str::slug($restaurantData['name'])],
                [
                'name' => $restaurantData['name'],
                'slug' => Str::slug($restaurantData['name']),
                'type' => $restaurantData['type'],
                'description' => $restaurantData['description'],
                'address' => $restaurantData['address'],
                'city' => $restaurantData['city'],
                'country' => $restaurantData['country'],
                'phone' => $restaurantData['phone'],
                'email' => $restaurantData['email'],
                'website' => $restaurantData['website'],
                'cuisine_type' => $restaurantData['cuisine_type'],
                'price_range' => $restaurantData['price_range'],
                'opening_hours' => $restaurantData['opening_hours'],
                'crew_friendly' => $restaurantData['crew_friendly'],
                'crew_discount' => $restaurantData['crew_discount'],
                'crew_discount_details' => $restaurantData['crew_discount_details'],
                'cover_image' => $this->getRestaurantImage($restaurantData['type'], $index),
                'is_verified' => $restaurantData['is_verified'],
                ]
            );

            // Create 5-10 reviews for each restaurant (only if restaurant is new or has no reviews)
            if ($restaurant->reviews()->count() == 0) {
                $reviewCount = rand(5, 10);
                for ($i = 0; $i < $reviewCount; $i++) {
                    $user = $users->random();
                    $overallRating = rand(3, 5);
                    
                    RestaurantReview::create([
                    'restaurant_id' => $restaurant->id,
                    'user_id' => $user->id,
                    'title' => $this->getRandomReviewTitle($restaurantData['type']),
                    'review' => $this->getRandomReviewText($restaurantData['type']),
                    'overall_rating' => $overallRating,
                    'food_rating' => $restaurantData['type'] === 'restaurant' || $restaurantData['type'] === 'cafe' ? rand($overallRating - 1, 5) : null,
                    'service_rating' => rand($overallRating - 1, 5),
                    'atmosphere_rating' => rand($overallRating - 1, 5),
                    'value_rating' => rand(3, 5),
                    'would_recommend' => rand(0, 10) > 1,
                    'is_anonymous' => rand(0, 10) > 7,
                    'is_verified' => true,
                    'visit_date' => now()->subDays(rand(1, 365)),
                    'crew_tips' => rand(0, 10) > 5 ? $this->getRandomCrewTip() : null,
                    'is_approved' => true,
                    ]);
                }
            }

            // Update restaurant rating stats
            $restaurant->updateRatingStats();
        }
    }

    private function getRandomReviewTitle($type): string
    {
        $titles = [
            'restaurant' => [
                'Great food and service',
                'Excellent meal',
                'Highly recommended',
                'Great value for money',
                'Perfect for crew',
            ],
            'bar' => [
                'Great atmosphere',
                'Perfect crew hangout',
                'Good drinks and vibe',
                'Fun place to unwind',
                'Crew-friendly spot',
            ],
            'cafe' => [
                'Great coffee',
                'Perfect breakfast spot',
                'Cozy and welcoming',
                'Good value',
                'Crew favorite',
            ],
            'shop' => [
                'Well stocked',
                'Good prices',
                'Convenient location',
                'Crew essentials available',
                'Helpful staff',
            ],
            'service' => [
                'Professional service',
                'Fast and reliable',
                'Good value',
                'Highly recommended',
                'Crew-friendly',
            ],
        ];

        $typeTitles = $titles[$type] ?? $titles['restaurant'];
        return $typeTitles[array_rand($typeTitles)];
    }

    private function getRandomReviewText($type): string
    {
        $reviews = [
            'restaurant' => [
                'Excellent food and service. Great atmosphere and reasonable prices. Highly recommended for crew.',
                'Great meal with good value for money. Staff were friendly and service was quick.',
                'Perfect spot for crew meals. Good food and crew discounts available.',
                'Excellent restaurant with great food. Very satisfied with the experience.',
            ],
            'bar' => [
                'Great atmosphere and good drinks. Popular with crew and good prices.',
                'Perfect place to unwind after work. Crew-friendly and fun atmosphere.',
                'Good bar with great vibe. Popular crew hangout spot.',
            ],
            'cafe' => [
                'Great coffee and breakfast options. Perfect for crew mornings.',
                'Cozy cafe with good food and reasonable prices. Crew-friendly.',
                'Excellent coffee and light meals. Good value and convenient location.',
            ],
            'shop' => [
                'Well-stocked shop with all crew essentials. Good prices and convenient location.',
                'Great selection of items. Helpful staff and crew discounts available.',
                'Good shop for crew needs. Reasonable prices and good service.',
            ],
            'service' => [
                'Professional service with fast turnaround. Good value and reliable.',
                'Excellent service. Quick and efficient. Highly recommended for crew.',
                'Great service at reasonable prices. Very satisfied.',
            ],
        ];

        $typeReviews = $reviews[$type] ?? $reviews['restaurant'];
        return $typeReviews[array_rand($typeReviews)];
    }

    private function getRandomCrewTip(): string
    {
        $tips = [
            'Ask for crew discount - they offer 15% off',
            'Best time to visit is during happy hour',
            'Call ahead for large groups',
            'They accept yacht crew ID for discounts',
            'Free WiFi available',
            'Parking available nearby',
            'Reservations recommended on weekends',
        ];

        return $tips[array_rand($tips)];
    }

    private function getRestaurantImage($type, $index): ?string
    {
        // Use placeholder images based on type
        $images = [
            'restaurant' => [
                'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=400&h=300&fit=crop',
                'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=400&h=300&fit=crop',
                'https://images.unsplash.com/photo-1559339352-11d035aa65de?w=400&h=300&fit=crop',
            ],
            'bar' => [
                'https://images.unsplash.com/photo-1551538827-9c037cb4f32a?w=400&h=300&fit=crop',
                'https://images.unsplash.com/photo-1572116469696-31de0f17cc34?w=400&h=300&fit=crop',
            ],
            'cafe' => [
                'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?w=400&h=300&fit=crop',
                'https://images.unsplash.com/photo-1554118811-1e0d58224f24?w=400&h=300&fit=crop',
            ],
            'shop' => [
                'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=400&h=300&fit=crop',
                'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=400&h=300&fit=crop',
            ],
            'service' => [
                'https://images.unsplash.com/photo-1581578731548-c64695cc6952?w=400&h=300&fit=crop',
                'https://images.unsplash.com/photo-1563453392212-326f5e854473?w=400&h=300&fit=crop',
            ],
        ];

        $typeImages = $images[$type] ?? $images['restaurant'];
        return $typeImages[$index % count($typeImages)] ?? null;
    }
}
