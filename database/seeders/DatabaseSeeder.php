<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Master data first (no dependencies)
        $this->call([
            MasterDataSeeder::class,
        ]);

        // Roles before users
        $this->call([
            RoleSeeder::class,
        ]);

        // Users and admins
        $this->call([
            UserSeeder::class,
            AdminSeeder::class,
            AssignRolesToUsersSeeder::class,
        ]);

        // // Core entities (yachts, marinas)
        // $this->call([
        //     YachtSeeder::class,
        //     MarinaSeeder::class,
        // ]);

        // // Business entities
        // $this->call([
        //     BrokerSeeder::class,
        //     ContractorSeeder::class,
        //     RestaurantSeeder::class,
        // ]);

        // // Training and certifications
        // $this->call([
        //     TrainingCertificationCategorySeeder::class,
        //     TrainingCertificationSeeder::class,
        //     TrainingProviderSeeder::class,
        //     TrainingProviderCourseSeeder::class,
        //     TrainingDummyDataSeeder::class,
        // ]);

        // // Mental health resources
        // $this->call([
        //     MentalHealthResourceSeeder::class,
        //     MentalHealthCourseSeeder::class,
        //     MentalHealthTherapistSeeder::class,
        //     MentalHealthSampleDataSeeder::class,
        // ]);

        // // Financial planning
        // $this->call([
        //     FinancialPlanningSeeder::class,
        // ]);

        // // Industry reviews
        // $this->call([
        //     IndustryReviewSeeder::class,
        //     IndustryReviewGallerySeeder::class,
        // ]);

        // // Itineraries and routes
        // $this->call([
        //     ItineraryRouteSeeder::class,
        // ]);

        // // Community
        // $this->call([
        //     MainCommunityThreadSeeder::class,
        // ]);

        // // Work logs
        // $this->call([
        //     WorkLogSeeder::class,
        // ]);

        // // Job board
        // $this->call([
        //     JobBoardSeeder::class,
        // ]);
    }
}
