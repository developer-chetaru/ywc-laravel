<?php

namespace Database\Factories;

use App\Models\JobPost;
use App\Models\User;
use App\Models\Yacht;
use Illuminate\Database\Eloquent\Factories\Factory;

class JobPostFactory extends Factory
{
    protected $model = JobPost::class;

    public function definition(): array
    {
        $jobType = $this->faker->randomElement(['permanent', 'temporary']);
        
        return [
            'user_id' => User::factory(),
            'yacht_id' => Yacht::factory(),
            'job_type' => $jobType,
            'temporary_work_type' => $jobType === 'temporary' ? $this->faker->randomElement(['day_work', 'short_contract', 'emergency_cover']) : null,
            'position_title' => $this->faker->randomElement(['2nd Stewardess', 'Deckhand', 'Sous Chef', 'Engineer', 'Bosun']),
            'department' => $this->faker->randomElement(['deck', 'interior', 'engine', 'galley']),
            'position_level' => $this->faker->randomElement(['2nd', '3rd', 'Sous', 'Junior', 'Senior']),
            'vessel_type' => $this->faker->randomElement(['motor_yacht', 'sailing_yacht']),
            'vessel_size' => $this->faker->numberBetween(30, 120),
            'flag' => $this->faker->randomElement(['Cayman Islands', 'Malta', 'Marshall Islands', 'Bermuda']),
            'program_type' => $this->faker->randomElement(['private', 'charter', 'both']),
            'cruising_regions' => $this->faker->randomElement(['Mediterranean', 'Caribbean', 'Med/Caribbean', 'Pacific']),
            'contract_type' => $jobType === 'permanent' ? $this->faker->randomElement(['permanent_liveaboard', 'permanent_dual_season', 'rotation']) : null,
            'rotation_schedule' => $this->faker->randomElement(['2:1', '3:1', '4:2', 'None']),
            'start_date' => $jobType === 'permanent' ? $this->faker->dateTimeBetween('now', '+3 months') : null,
            'work_start_date' => $jobType === 'temporary' ? $this->faker->dateTimeBetween('now', '+7 days') : null,
            'work_end_date' => $jobType === 'temporary' ? $this->faker->dateTimeBetween('+1 days', '+14 days') : null,
            'location' => $this->faker->randomElement(['Antibes, France', 'Monaco', 'Cannes, France', 'Palma, Spain', 'Barcelona, Spain']),
            'latitude' => $this->faker->latitude(43.0, 43.8),
            'longitude' => $this->faker->longitude(7.0, 7.5),
            'salary_min' => $jobType === 'permanent' ? $this->faker->numberBetween(3000, 5000) : null,
            'salary_max' => $jobType === 'permanent' ? $this->faker->numberBetween(5000, 8000) : null,
            'day_rate_min' => $jobType === 'temporary' ? $this->faker->numberBetween(150, 250) : null,
            'day_rate_max' => $jobType === 'temporary' ? $this->faker->numberBetween(250, 400) : null,
            'required_certifications' => ['STCW Basic Safety Training', 'ENG1 Medical Certificate'],
            'min_years_experience' => $this->faker->numberBetween(1, 5),
            'about_position' => $this->faker->paragraph(3),
            'about_vessel_program' => $this->faker->paragraph(2),
            'responsibilities' => $this->faker->paragraph(4),
            'status' => $this->faker->randomElement(['active', 'draft']),
            'published_at' => $this->faker->optional(0.8)->dateTimeBetween('-30 days', 'now'),
            'public_post' => true,
            'notify_matching_crew' => true,
        ];
    }

    public function permanent(): static
    {
        return $this->state(fn (array $attributes) => [
            'job_type' => 'permanent',
            'temporary_work_type' => null,
        ]);
    }

    public function temporary(): static
    {
        return $this->state(fn (array $attributes) => [
            'job_type' => 'temporary',
            'contract_type' => null,
            'salary_min' => null,
            'salary_max' => null,
        ]);
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'published_at' => now(),
        ]);
    }
}

