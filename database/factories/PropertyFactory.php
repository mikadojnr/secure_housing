<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PropertyFactory extends Factory
{
    public function definition(): array
    {
        $bedrooms = fake()->numberBetween(1, 4);
        $bathrooms = fake()->numberBetween(1, $bedrooms);

        return [
            'landlord_id' => User::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraphs(3, true),
            'property_type' => fake()->randomElement(['apartment', 'house', 'room', 'studio', 'shared']),
            'rent_amount' => fake()->numberBetween(800, 3500),
            'deposit_amount' => fake()->numberBetween(800, 3500),
            'currency' => 'USD',
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'state' => fake()->stateAbbr(),
            'postal_code' => fake()->postcode(),
            'country' => 'USA',
            'latitude' => fake()->latitude(40, 45),
            'longitude' => fake()->longitude(-75, -70),
            'bedrooms' => $bedrooms,
            'bathrooms' => $bathrooms,
            'max_occupants' => fake()->numberBetween($bedrooms, $bedrooms * 2),
            'amenities' => fake()->randomElements([
                'wifi', 'laundry', 'parking', 'gym', 'pool', 'ac', 'heating',
                'dishwasher', 'microwave', 'furnished', 'pet_friendly', 'balcony'
            ], fake()->numberBetween(3, 8)),
            'utilities_included' => fake()->randomElements([
                'electricity', 'water', 'gas', 'internet', 'cable', 'trash'
            ], fake()->numberBetween(2, 5)),
            'house_rules' => fake()->randomElements([
                'No smoking', 'No pets', 'No parties', 'Quiet hours after 10 PM',
                'Keep common areas clean', 'No overnight guests without permission'
            ], fake()->numberBetween(2, 4)),
            'available_from' => fake()->dateTimeBetween('now', '+3 months'),
            'available_until' => fake()->optional()->dateTimeBetween('+6 months', '+2 years'),
            'status' => fake()->randomElement(['draft', 'active', 'inactive']),
            'is_verified' => fake()->boolean(70),
            'trust_score' => fake()->randomFloat(2, 2.0, 5.0),
            'views_count' => fake()->numberBetween(0, 500),
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_verified' => true,
            'trust_score' => fake()->randomFloat(2, 4.0, 5.0),
        ]);
    }
}
