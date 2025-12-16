<?php

namespace Database\Factories;

use App\Models\CrewAvailability;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CrewAvailabilityFactory extends Factory
{
    protected $model = CrewAvailability::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'status' => $this->faker->randomElement(['available_now', 'available_with_notice', 'not_available']),
            'available_from' => $this->faker->optional()->dateTimeBetween('now', '+30 days'),
            'available_until' => $this->faker->optional()->dateTimeBetween('+30 days', '+180 days'),
            'notice_required' => $this->faker->randomElement(['immediate', '24_hours', '2_3_days']),
            'day_work' => $this->faker->boolean(70),
            'short_contracts' => $this->faker->boolean(60),
            'medium_contracts' => $this->faker->boolean(40),
            'emergency_cover' => $this->faker->boolean(50),
            'available_positions' => $this->faker->randomElements(['2nd Stewardess', '3rd Stewardess', 'Deckhand', 'Bosun'], $this->faker->numberBetween(1, 3)),
            'day_rate_min' => $this->faker->numberBetween(150, 200),
            'day_rate_max' => $this->faker->numberBetween(200, 300),
            'half_day_rate' => $this->faker->numberBetween(100, 150),
            'emergency_rate' => $this->faker->numberBetween(250, 350),
            'rates_negotiable' => $this->faker->boolean(70),
            'search_radius_km' => $this->faker->randomElement([10, 20, 50, 100]),
            'current_location' => $this->faker->randomElement(['Antibes, France', 'Monaco', 'Cannes, France']),
            'latitude' => $this->faker->latitude(43.0, 43.8),
            'longitude' => $this->faker->longitude(7.0, 7.5),
            'auto_update_location' => true,
            'notify_same_day_urgent' => true,
            'notify_24_hour_jobs' => true,
            'profile_visibility' => 'all_verified',
            'total_jobs_completed' => $this->faker->numberBetween(0, 50),
            'average_rating' => $this->faker->randomFloat(2, 3.5, 5.0),
            'completion_rate_percentage' => $this->faker->numberBetween(80, 100),
        ];
    }

    public function availableNow(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'available_now',
            'available_from' => now(),
        ]);
    }
}

