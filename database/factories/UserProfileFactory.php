<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserProfileFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'user_type' => fake()->randomElement(['student', 'landlord']),
            'phone' => fake()->phoneNumber(),
            'date_of_birth' => fake()->dateTimeBetween('-30 years', '-18 years'),
            'university' => fake()->randomElement([
                'Harvard University',
                'MIT',
                'Stanford University',
                'UC Berkeley',
                'NYU',
                'Columbia University',
                'Yale University',
                'Princeton University'
            ]),
            'student_id' => fake()->bothify('??######'),
            'bio' => fake()->paragraph(),
            'preferences' => [
                'budget_max' => fake()->numberBetween(500, 3000),
                'preferred_location' => fake()->city(),
                'amenities' => fake()->randomElements(['wifi', 'laundry', 'parking', 'gym'], 2),
            ],
        ];
    }

    public function student(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_type' => 'student',
        ]);
    }

    public function landlord(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_type' => 'landlord',
            'university' => null,
            'student_id' => null,
        ]);
    }
}
