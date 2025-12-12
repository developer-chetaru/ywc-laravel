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
            UserSeeder::class, // Add users seeder
            ItineraryRouteSeeder::class,
            YachtSeeder::class,
            MarinaSeeder::class,
            IndustryReviewSeeder::class,
            ContractorSeeder::class,
            BrokerSeeder::class,
            RestaurantSeeder::class,
            IndustryReviewGallerySeeder::class,
            FinancialPlanningSeeder::class, // Financial planning dummy data
            MentalHealthTherapistSeeder::class, // Mental health therapists
            MentalHealthResourceSeeder::class, // Mental health resources
            MentalHealthCourseSeeder::class, // Mental health courses
        ]);
        
    }
}
