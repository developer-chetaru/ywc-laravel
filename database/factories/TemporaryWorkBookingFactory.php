<?php

namespace Database\Factories;

use App\Models\TemporaryWorkBooking;
use App\Models\JobPost;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TemporaryWorkBookingFactory extends Factory
{
    protected $model = TemporaryWorkBooking::class;

    public function definition(): array
    {
        $workDate = $this->faker->dateTimeBetween('now', '+30 days');
        $status = $this->faker->randomElement(['pending', 'confirmed', 'completed', 'cancelled']);
        
        return [
            'job_post_id' => JobPost::factory()->temporary(),
            'user_id' => User::factory(),
            'booked_by_user_id' => User::factory(),
            'status' => $status,
            'work_date' => $workDate,
            'start_time' => '08:00:00',
            'end_time' => '18:00:00',
            'total_hours' => 10,
            'work_description' => $this->faker->sentence(10),
            'location' => $this->faker->randomElement(['Antibes, France', 'Monaco', 'Cannes, France']),
            'berth_details' => 'Berth ' . $this->faker->randomLetter() . $this->faker->numberBetween(1, 30),
            'day_rate' => $this->faker->numberBetween(150, 300),
            'total_payment' => $this->faker->numberBetween(150, 300),
            'payment_currency' => 'EUR',
            'payment_method' => $this->faker->randomElement(['cash', 'bank_transfer']),
            'payment_timing' => 'End of day',
            'payment_received' => $status === 'completed' ? $this->faker->boolean(80) : false,
            'payment_received_at' => $status === 'completed' && $this->faker->boolean(80) ? $this->faker->dateTimeBetween($workDate, 'now') : null,
            'contact_name' => $this->faker->name(),
            'contact_phone' => $this->faker->phoneNumber(),
            'whatsapp_available' => true,
            'confirmed_at' => $status !== 'pending' ? $this->faker->dateTimeBetween('-7 days', $workDate) : null,
            'completed_at' => $status === 'completed' ? $this->faker->dateTimeBetween($workDate, 'now') : null,
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'completed_at' => now()->subDays($this->faker->numberBetween(1, 30)),
        ]);
    }

    public function pendingPayment(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'payment_received' => false,
            'completed_at' => now()->subDays($this->faker->numberBetween(1, 7)),
        ]);
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_received' => true,
            'payment_received_at' => now()->subDays($this->faker->numberBetween(1, 7)),
        ]);
    }
}

