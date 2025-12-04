<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
         $this->call([
            RoleSeeder::class,
            AdminSeeder::class,
            ItineraryRouteSeeder::class,
            YachtSeeder::class,
            MarinaSeeder::class,
            IndustryReviewSeeder::class,
            ContractorSeeder::class,
            BrokerSeeder::class,
            RestaurantSeeder::class,
            IndustryReviewGallerySeeder::class,
        ]);
        
    }
}
