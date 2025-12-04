<?php

namespace Database\Seeders;

use App\Models\Yacht;
use App\Models\Marina;
use App\Models\Contractor;
use App\Models\Broker;
use App\Models\Restaurant;
use App\Models\YachtGallery;
use App\Models\MarinaGallery;
use App\Models\ContractorGallery;
use App\Models\BrokerGallery;
use App\Models\RestaurantGallery;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class IndustryReviewGallerySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding gallery images for Industry Review entities...');

        // Seed Yacht Galleries
        $this->seedYachtGalleries();
        
        // Seed Marina Galleries
        $this->seedMarinaGalleries();
        
        // Seed Contractor Galleries
        $this->seedContractorGalleries();
        
        // Seed Broker Galleries
        $this->seedBrokerGalleries();
        
        // Seed Restaurant Galleries
        $this->seedRestaurantGalleries();

        $this->command->info('Gallery images seeded successfully!');
    }

    /**
     * Seed gallery images for Yachts
     */
    private function seedYachtGalleries(): void
    {
        $yachts = Yacht::all();
        $yachtCategories = ['exterior', 'interior', 'deck', 'crew_areas', 'bridge', 'other'];
        
        $yachtImages = [
            'https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=800&h=600&fit=crop', // Yacht exterior
            'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800&h=600&fit=crop', // Yacht deck
            'https://images.unsplash.com/photo-1559827260-dc66d52bef19?w=800&h=600&fit=crop', // Yacht interior
            'https://images.unsplash.com/photo-1567899378494-47b22a2ae96a?w=800&h=600&fit=crop', // Yacht bridge
            'https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=800&h=600&fit=crop', // Yacht crew area
        ];

        foreach ($yachts as $index => $yacht) {
            // Skip if yacht already has gallery images
            if ($yacht->gallery()->count() > 0) {
                continue;
            }

            $imageCount = rand(3, 5);
            for ($i = 0; $i < $imageCount; $i++) {
                $imageUrl = $yachtImages[array_rand($yachtImages)];
                $category = $yachtCategories[array_rand($yachtCategories)];
                
                YachtGallery::create([
                    'yacht_id' => $yacht->id,
                    'image_path' => $imageUrl,
                    'caption' => ucfirst($category) . ' view of ' . $yacht->name,
                    'category' => $category,
                    'order' => $i + 1,
                    'is_primary' => $i === 0,
                ]);
            }
        }

        $this->command->info("Added gallery images for {$yachts->count()} yachts");
    }

    /**
     * Seed gallery images for Marinas
     */
    private function seedMarinaGalleries(): void
    {
        $marinas = Marina::all();
        $marinaCategories = ['facilities', 'berths', 'amenities', 'restaurant', 'shop', 'other'];
        
        $marinaImages = [
            'https://images.unsplash.com/photo-1559827260-dc66d52bef19?w=800&h=600&fit=crop', // Marina berths
            'https://images.unsplash.com/photo-1567899378494-47b22a2ae96a?w=800&h=600&fit=crop', // Marina facilities
            'https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=800&h=600&fit=crop', // Marina view
            'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800&h=600&fit=crop', // Marina amenities
            'https://images.unsplash.com/photo-1559827260-dc66d52bef19?w=800&h=600&fit=crop', // Marina restaurant
        ];

        foreach ($marinas as $index => $marina) {
            // Skip if marina already has gallery images
            if ($marina->gallery()->count() > 0) {
                continue;
            }

            $imageCount = rand(3, 5);
            for ($i = 0; $i < $imageCount; $i++) {
                $imageUrl = $marinaImages[array_rand($marinaImages)];
                $category = $marinaCategories[array_rand($marinaCategories)];
                
                MarinaGallery::create([
                    'marina_id' => $marina->id,
                    'image_path' => $imageUrl,
                    'caption' => ucfirst($category) . ' at ' . $marina->name,
                    'category' => $category,
                    'order' => $i + 1,
                    'is_primary' => $i === 0,
                ]);
            }
        }

        $this->command->info("Added gallery images for {$marinas->count()} marinas");
    }

    /**
     * Seed gallery images for Contractors
     */
    private function seedContractorGalleries(): void
    {
        $contractors = Contractor::all();
        $contractorCategories = ['work_samples', 'equipment', 'team', 'facilities', 'other'];
        
        $contractorImages = [
            'https://images.unsplash.com/photo-1581092160562-40aa08e78837?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1581092160562-40aa08e78837?w=800&h=600&fit=crop',
        ];

        foreach ($contractors as $index => $contractor) {
            // Skip if contractor already has gallery images
            if ($contractor->gallery()->count() > 0) {
                continue;
            }

            $imageCount = rand(3, 5);
            for ($i = 0; $i < $imageCount; $i++) {
                $imageUrl = $contractorImages[array_rand($contractorImages)];
                $category = $contractorCategories[array_rand($contractorCategories)];
                
                ContractorGallery::create([
                    'contractor_id' => $contractor->id,
                    'image_path' => $imageUrl,
                    'caption' => ucfirst(str_replace('_', ' ', $category)) . ' - ' . $contractor->name,
                    'category' => $category,
                    'order' => $i + 1,
                    'is_primary' => $i === 0,
                ]);
            }
        }

        $this->command->info("Added gallery images for {$contractors->count()} contractors");
    }

    /**
     * Seed gallery images for Brokers
     */
    private function seedBrokerGalleries(): void
    {
        $brokers = Broker::all();
        $brokerCategories = ['office', 'team', 'events', 'certifications', 'other'];
        
        $brokerImages = [
            'https://images.unsplash.com/photo-1497366216548-37526070297c?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1521737604893-d14cc237f11d?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1556761175-5973dc0f32e7?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1497366216548-37526070297c?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1521737604893-d14cc237f11d?w=800&h=600&fit=crop',
        ];

        foreach ($brokers as $index => $broker) {
            // Skip if broker already has gallery images
            if ($broker->gallery()->count() > 0) {
                continue;
            }

            $imageCount = rand(3, 5);
            for ($i = 0; $i < $imageCount; $i++) {
                $imageUrl = $brokerImages[array_rand($brokerImages)];
                $category = $brokerCategories[array_rand($brokerCategories)];
                
                BrokerGallery::create([
                    'broker_id' => $broker->id,
                    'image_path' => $imageUrl,
                    'caption' => ucfirst($category) . ' - ' . $broker->name,
                    'category' => $category,
                    'order' => $i + 1,
                    'is_primary' => $i === 0,
                ]);
            }
        }

        $this->command->info("Added gallery images for {$brokers->count()} brokers");
    }

    /**
     * Seed gallery images for Restaurants
     */
    private function seedRestaurantGalleries(): void
    {
        $restaurants = Restaurant::all();
        $restaurantCategories = ['interior', 'exterior', 'food', 'menu', 'atmosphere', 'other'];
        
        $restaurantImages = [
            'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1559339352-11d035aa65de?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=800&h=600&fit=crop',
        ];

        foreach ($restaurants as $index => $restaurant) {
            // Skip if restaurant already has gallery images
            if ($restaurant->gallery()->count() > 0) {
                continue;
            }

            $imageCount = rand(3, 5);
            for ($i = 0; $i < $imageCount; $i++) {
                $imageUrl = $restaurantImages[array_rand($restaurantImages)];
                $category = $restaurantCategories[array_rand($restaurantCategories)];
                
                RestaurantGallery::create([
                    'restaurant_id' => $restaurant->id,
                    'image_path' => $imageUrl,
                    'caption' => ucfirst($category) . ' at ' . $restaurant->name,
                    'category' => $category,
                    'order' => $i + 1,
                    'is_primary' => $i === 0,
                ]);
            }
        }

        $this->command->info("Added gallery images for {$restaurants->count()} restaurants");
    }
}
