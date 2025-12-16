<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Yacht;
use App\Models\JobPost;
use App\Models\TemporaryWorkBooking;
use App\Models\CrewAvailability;
use App\Models\VesselVerification;
use App\Models\JobApplication;
use App\Models\PreferredCrewList;
use Illuminate\Support\Facades\Hash;

class JobBoardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create captains (verified)
        $captain1 = User::create([
            'first_name' => 'James',
            'last_name' => 'Morrison',
            'email' => 'captain.morrison@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'phone' => '+33 6 12 34 56 78',
            'nationality' => 'British',
            'looking_for_work' => false,
        ]);

        $captain2 = User::create([
            'first_name' => 'Sarah',
            'last_name' => 'Larsen',
            'email' => 'captain.larsen@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'phone' => '+33 6 98 76 54 32',
            'nationality' => 'Norwegian',
            'looking_for_work' => false,
        ]);

        // Create yachts
        $yacht1 = Yacht::create([
            'name' => 'M/Y Azure Dreams',
            'slug' => 'm-y-azure-dreams',
            'type' => 'motor_yacht',
            'length_meters' => 58,
            'flag_registry' => 'Cayman Islands',
            'home_port' => 'Antibes, France',
            'status' => 'private',
            'created_by_user_id' => $captain1->id,
        ]);

        $yacht2 = Yacht::create([
            'name' => 'M/Y Serenity',
            'slug' => 'm-y-serenity',
            'type' => 'motor_yacht',
            'length_meters' => 62,
            'flag_registry' => 'Malta',
            'home_port' => 'Monaco',
            'status' => 'private',
            'created_by_user_id' => $captain2->id,
        ]);

        // Verify captains
        VesselVerification::create([
            'user_id' => $captain1->id,
            'yacht_id' => $yacht1->id,
            'verification_method' => 'captain',
            'vessel_name' => 'M/Y Azure Dreams',
            'role_on_vessel' => 'Captain',
            'authority_description' => 'Captain and authorized to hire crew',
            'status' => 'verified',
            'verified_at' => now()->subDays(30),
            'email_verified' => true,
            'phone_verified' => true,
        ]);

        VesselVerification::create([
            'user_id' => $captain2->id,
            'yacht_id' => $yacht2->id,
            'verification_method' => 'captain',
            'vessel_name' => 'M/Y Serenity',
            'role_on_vessel' => 'Captain',
            'authority_description' => 'Captain and authorized to hire crew',
            'status' => 'verified',
            'verified_at' => now()->subDays(60),
            'email_verified' => true,
            'phone_verified' => true,
        ]);

        // Create crew members
        $crew1 = User::create([
            'first_name' => 'Sarah',
            'last_name' => 'Mitchell',
            'email' => 'sarah.mitchell@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'phone' => '+33 6 11 22 33 44',
            'nationality' => 'British',
            'years_experience' => 3,
            'looking_for_work' => true,
            'latitude' => 43.5804,
            'longitude' => 7.1221,
            'location_name' => 'Antibes, France',
            'certifications' => ['STCW Basic Safety Training', 'ENG1 Medical Certificate', 'Interior Training'],
            'specializations' => ['2nd Stewardess', '3rd Stewardess'],
            'languages' => ['English', 'French'],
            'rating' => 5.0,
            'total_reviews' => 12,
        ]);

        $crew2 = User::create([
            'first_name' => 'Marcus',
            'last_name' => 'Thompson',
            'email' => 'marcus.thompson@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'phone' => '+33 6 55 66 77 88',
            'nationality' => 'South African',
            'years_experience' => 5,
            'looking_for_work' => true,
            'latitude' => 43.7384,
            'longitude' => 7.4246,
            'location_name' => 'Monaco',
            'certifications' => ['STCW Basic Safety Training', 'ENG1 Medical Certificate', 'PWC License'],
            'specializations' => ['Deckhand', 'Bosun'],
            'languages' => ['English', 'Spanish'],
            'rating' => 5.0,
            'total_reviews' => 28,
        ]);

        $crew3 = User::create([
            'first_name' => 'Emma',
            'last_name' => 'Rodriguez',
            'email' => 'emma.rodriguez@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'phone' => '+33 6 99 88 77 66',
            'nationality' => 'Spanish',
            'years_experience' => 4,
            'looking_for_work' => true,
            'latitude' => 43.5528,
            'longitude' => 7.0174,
            'location_name' => 'Cannes, France',
            'certifications' => ['STCW Basic Safety Training', 'ENG1 Medical Certificate', 'Chef Training'],
            'specializations' => ['Chef', '2nd Stewardess'],
            'languages' => ['English', 'Spanish', 'French'],
            'rating' => 4.9,
            'total_reviews' => 18,
        ]);

        // Create crew availability
        CrewAvailability::create([
            'user_id' => $crew1->id,
            'status' => 'available_now',
            'available_from' => now(),
            'available_until' => now()->addMonths(6),
            'notice_required' => 'immediate',
            'day_work' => true,
            'short_contracts' => true,
            'emergency_cover' => true,
            'available_positions' => ['2nd Stewardess', '3rd Stewardess'],
            'day_rate_min' => 150,
            'day_rate_max' => 200,
            'search_radius_km' => 20,
            'current_location' => 'Antibes, France',
            'latitude' => 43.5804,
            'longitude' => 7.1221,
            'total_jobs_completed' => 12,
            'average_rating' => 5.0,
            'completion_rate_percentage' => 100,
        ]);

        CrewAvailability::create([
            'user_id' => $crew2->id,
            'status' => 'available_now',
            'available_from' => now(),
            'available_until' => now()->addMonths(4),
            'notice_required' => 'immediate',
            'day_work' => true,
            'short_contracts' => true,
            'emergency_cover' => true,
            'available_positions' => ['Deckhand', 'Bosun'],
            'day_rate_min' => 180,
            'day_rate_max' => 220,
            'search_radius_km' => 20,
            'current_location' => 'Monaco',
            'latitude' => 43.7384,
            'longitude' => 7.4246,
            'total_jobs_completed' => 28,
            'average_rating' => 5.0,
            'completion_rate_percentage' => 100,
        ]);

        // Create job posts
        $job1 = JobPost::create([
            'user_id' => $captain1->id,
            'yacht_id' => $yacht1->id,
            'job_type' => 'permanent',
            'position_title' => '2nd Stewardess',
            'department' => 'interior',
            'position_level' => '2nd',
            'vessel_type' => 'motor_yacht',
            'vessel_size' => 58,
            'flag' => 'Cayman Islands',
            'program_type' => 'private',
            'cruising_regions' => 'Med summers / Caribbean winters',
            'contract_type' => 'permanent_dual_season',
            'start_date' => now()->addDays(30),
            'location' => 'Antibes, France',
            'latitude' => 43.5804,
            'longitude' => 7.1221,
            'salary_min' => 3500,
            'salary_max' => 4000,
            'salary_currency' => 'EUR',
            'required_certifications' => ['STCW Basic Safety Training', 'ENG1 Medical Certificate'],
            'min_years_experience' => 2,
            'min_vessel_size_experience' => 45,
            'about_position' => "We're seeking an experienced 2nd Stewardess to join our professional and friendly crew of 12. M/Y Azure Dreams is a well-maintained 58m motor yacht with family-oriented owners who value quality service and crew development.",
            'about_vessel_program' => 'Dual season program: Mediterranean May-October, Caribbean November-April. Private vessel with regular family usage.',
            'responsibilities' => "Support Chief Stew with all interior operations, manage laundry and cabin service, serve during guest meals and events, maintain high standards of presentation.",
            'crew_size' => 12,
            'status' => 'active',
            'published_at' => now()->subHours(2),
            'public_post' => true,
            'notify_matching_crew' => true,
            'views_count' => 124,
            'applications_count' => 7,
        ]);

        $job2 = JobPost::create([
            'user_id' => $captain2->id,
            'yacht_id' => $yacht2->id,
            'job_type' => 'temporary',
            'temporary_work_type' => 'day_work',
            'position_title' => 'Deckhand',
            'department' => 'deck',
            'urgency_level' => 'urgent',
            'work_start_date' => now()->addDay(),
            'work_end_date' => now()->addDay(),
            'work_start_time' => now()->addDay()->setTime(8, 0, 0),
            'work_end_time' => now()->addDay()->setTime(18, 0, 0),
            'total_hours' => 10,
            'location' => 'Monaco',
            'latitude' => 43.7384,
            'longitude' => 7.4246,
            'berth_details' => 'Berth A24',
            'day_rate_min' => 180,
            'day_rate_max' => 180,
            'about_position' => 'Vessel in refit, need experienced deckhand for varnishing work on aft deck teak. Weather forecast shows rain tomorrow afternoon, so we need to complete today.',
            'required_certifications' => ['STCW Basic Safety Training', 'ENG1 Medical Certificate'],
            'contact_name' => 'Bosun Mike Chen',
            'contact_phone' => '+33 6 12 34 56 78',
            'whatsapp_available' => true,
            'payment_method' => 'cash',
            'payment_timing' => 'End of day',
            'status' => 'active',
            'published_at' => now()->subMinutes(15),
            'public_post' => true,
            'notify_matching_crew' => true,
        ]);

        // Create job applications
        JobApplication::create([
            'job_post_id' => $job1->id,
            'user_id' => $crew1->id,
            'status' => 'submitted',
            'match_score' => 98.0,
            'cover_message' => "Dear Captain Morrison, I'm excited to apply for the 2nd Stewardess position on M/Y Azure Dreams. Your dual-season program and focus on crew development are exactly what I'm seeking.",
            'submitted_at' => now()->subMinutes(15),
        ]);

        // Create temporary work bookings
        $booking1 = TemporaryWorkBooking::create([
            'job_post_id' => $job2->id,
            'user_id' => $crew2->id,
            'booked_by_user_id' => $captain2->id,
            'status' => 'completed',
            'work_date' => now()->subDays(7),
            'start_time' => '08:00:00',
            'end_time' => '18:00:00',
            'total_hours' => 10,
            'work_description' => 'Varnishing aft deck teak',
            'location' => 'Monaco',
            'berth_details' => 'Berth A24',
            'day_rate' => 180,
            'total_payment' => 180,
            'payment_currency' => 'EUR',
            'payment_method' => 'cash',
            'payment_timing' => 'End of day',
            'payment_received' => true,
            'payment_received_at' => now()->subDays(7),
            'contact_name' => 'Bosun Mike Chen',
            'contact_phone' => '+33 6 12 34 56 78',
            'whatsapp_available' => true,
            'confirmed_at' => now()->subDays(8),
            'completed_at' => now()->subDays(7),
        ]);

        $booking2 = TemporaryWorkBooking::create([
            'job_post_id' => $job2->id,
            'user_id' => $crew2->id,
            'booked_by_user_id' => $captain2->id,
            'status' => 'completed',
            'work_date' => now()->subDays(3),
            'start_time' => '08:00:00',
            'end_time' => '18:00:00',
            'total_hours' => 10,
            'work_description' => 'Deck maintenance and polishing',
            'location' => 'Monaco',
            'day_rate' => 180,
            'total_payment' => 180,
            'payment_currency' => 'EUR',
            'payment_method' => 'cash',
            'payment_received' => false, // Pending payment
            'contact_name' => 'Bosun Mike Chen',
            'confirmed_at' => now()->subDays(4),
            'completed_at' => now()->subDays(3),
        ]);

        // Create preferred crew list
        PreferredCrewList::create([
            'user_id' => $captain2->id,
            'crew_user_id' => $crew2->id,
            'yacht_id' => $yacht2->id,
            'times_worked_together' => 7,
            'captain_rating' => 5,
            'notes' => 'Best temp deckhand. Always reliable.',
            'is_favorite' => true,
            'first_hired_at' => now()->subMonths(3),
            'last_hired_at' => now()->subDays(3),
            'last_worked_at' => now()->subDays(3),
        ]);

        $this->command->info('Job Board test data seeded successfully!');
        $this->command->info('Created:');
        $this->command->info('- 2 Captains (verified)');
        $this->command->info('- 2 Yachts');
        $this->command->info('- 3 Crew Members');
        $this->command->info('- 2 Job Posts (1 permanent, 1 temporary)');
        $this->command->info('- 1 Job Application');
        $this->command->info('- 2 Temporary Work Bookings (1 paid, 1 pending payment)');
        $this->command->info('- 1 Preferred Crew List entry');
    }
}

