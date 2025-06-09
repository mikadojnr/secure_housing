<?php

namespace Database\Factories;

use App\Models\Verification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class VerificationFactory extends Factory
{
    protected $model = Verification::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'verification_type' => fake()->randomElement(['identity', 'student', 'landlord']),
            'status' => fake()->randomElement(['pending', 'verified', 'rejected', 'expired']),
            'provider' => fake()->randomElement(['jumio', 'onfido', 'manual']),
            'external_id' => fake()->uuid(),
            'verification_data' => [
                'document_type' => fake()->randomElement(['passport', 'drivers_license', 'id_card']),
                'country' => 'USA',
                'confidence_score' => fake()->randomFloat(2, 0.7, 1.0),
            ],
            'verified_at' => fake()->optional(0.7)->dateTimeBetween('-1 year', 'now'),
            'expires_at' => fake()->optional(0.7)->dateTimeBetween('now', '+2 years'),
            'rejection_reason' => fake()->optional(0.1)->sentence(),
        ];
    }

    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'verified',
            'verified_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'expires_at' => fake()->dateTimeBetween('now', '+2 years'),
            'rejection_reason' => null,
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'verified_at' => null,
            'expires_at' => null,
            'rejection_reason' => null,
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'verified_at' => null,
            'expires_at' => null,
            'rejection_reason' => fake()->sentence(),
        ]);
    }
}
