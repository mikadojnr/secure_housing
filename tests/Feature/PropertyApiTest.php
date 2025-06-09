<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Property;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class PropertyApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test user with landlord profile
        $this->landlord = User::factory()->create();
        UserProfile::factory()->create([
            'user_id' => $this->landlord->id,
            'user_type' => 'landlord',
        ]);
        
        $this->student = User::factory()->create();
        UserProfile::factory()->create([
            'user_id' => $this->student->id,
            'user_type' => 'student',
        ]);
    }

    public function test_can_list_properties()
    {
        Property::factory()->count(5)->create([
            'landlord_id' => $this->landlord->id,
            'status' => 'active',
        ]);

        $response = $this->getJson('/api/properties');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'title',
                            'description',
                            'rent_amount',
                            'address',
                            'city',
                            'bedrooms',
                            'bathrooms',
                        ]
                    ],
                    'meta' => [
                        'current_page',
                        'last_page',
                        'per_page',
                        'total',
                    ]
                ]);
    }

    public function test_can_filter_properties_by_city()
    {
        Property::factory()->create([
            'landlord_id' => $this->landlord->id,
            'city' => 'Boston',
            'status' => 'active',
        ]);

        Property::factory()->create([
            'landlord_id' => $this->landlord->id,
            'city' => 'New York',
            'status' => 'active',
        ]);

        $response = $this->getJson('/api/properties?city=Boston');

        $response->assertStatus(200);
        $this->assertEquals(1, count($response->json('data')));
        $this->assertEquals('Boston', $response->json('data.0.city'));
    }

    public function test_can_filter_properties_by_price_range()
    {
        Property::factory()->create([
            'landlord_id' => $this->landlord->id,
            'rent_amount' => 1000,
            'status' => 'active',
        ]);

        Property::factory()->create([
            'landlord_id' => $this->landlord->id,
            'rent_amount' => 2000,
            'status' => 'active',
        ]);

        $response = $this->getJson('/api/properties?min_price=1500&max_price=2500');

        $response->assertStatus(200);
        $this->assertEquals(1, count($response->json('data')));
        $this->assertEquals(2000, $response->json('data.0.rent_amount'));
    }

    public function test_can_show_property_details()
    {
        $property = Property::factory()->create([
            'landlord_id' => $this->landlord->id,
            'status' => 'active',
        ]);

        $response = $this->getJson("/api/properties/{$property->id}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'title',
                        'description',
                        'rent_amount',
                        'landlord',
                        'images',
                    ],
                    'verification_status',
                    'average_rating',
                    'total_reviews',
                ]);
    }

    public function test_authenticated_landlord_can_create_property()
    {
        Sanctum::actingAs($this->landlord);

        $propertyData = [
            'title' => 'Beautiful Apartment',
            'description' => 'A lovely 2-bedroom apartment near campus',
            'property_type' => 'apartment',
            'rent_amount' => 1500,
            'deposit_amount' => 1500,
            'address' => '123 Main St',
            'city' => 'Boston',
            'state' => 'MA',
            'postal_code' => '02101',
            'country' => 'USA',
            'bedrooms' => 2,
            'bathrooms' => 1,
            'max_occupants' => 2,
            'available_from' => now()->addDays(30)->format('Y-m-d'),
            'amenities' => ['wifi', 'laundry', 'parking'],
        ];

        $response = $this->postJson('/api/properties', $propertyData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'title',
                        'landlord_id',
                        'status',
                    ],
                    'message'
                ]);

        $this->assertDatabaseHas('properties', [
            'title' => 'Beautiful Apartment',
            'landlord_id' => $this->landlord->id,
        ]);
    }

    public function test_unauthenticated_user_cannot_create_property()
    {
        $propertyData = [
            'title' => 'Beautiful Apartment',
            'description' => 'A lovely 2-bedroom apartment near campus',
            'property_type' => 'apartment',
            'rent_amount' => 1500,
            'deposit_amount' => 1500,
            'address' => '123 Main St',
            'city' => 'Boston',
            'state' => 'MA',
            'postal_code' => '02101',
            'country' => 'USA',
            'bedrooms' => 2,
            'bathrooms' => 1,
            'max_occupants' => 2,
            'available_from' => now()->addDays(30)->format('Y-m-d'),
        ];

        $response = $this->postJson('/api/properties', $propertyData);

        $response->assertStatus(401);
    }

    public function test_landlord_can_update_own_property()
    {
        Sanctum::actingAs($this->landlord);

        $property = Property::factory()->create([
            'landlord_id' => $this->landlord->id,
        ]);

        $updateData = [
            'title' => 'Updated Property Title',
            'rent_amount' => 1800,
        ];

        $response = $this->putJson("/api/properties/{$property->id}", $updateData);

        $response->assertStatus(200);
        
        $this->assertDatabaseHas('properties', [
            'id' => $property->id,
            'title' => 'Updated Property Title',
            'rent_amount' => 1800,
        ]);
    }

    public function test_landlord_cannot_update_other_landlord_property()
    {
        $otherLandlord = User::factory()->create();
        UserProfile::factory()->create([
            'user_id' => $otherLandlord->id,
            'user_type' => 'landlord',
        ]);

        $property = Property::factory()->create([
            'landlord_id' => $otherLandlord->id,
        ]);

        Sanctum::actingAs($this->landlord);

        $updateData = [
            'title' => 'Updated Property Title',
        ];

        $response = $this->putJson("/api/properties/{$property->id}", $updateData);

        $response->assertStatus(403);
    }

    public function test_can_search_properties_by_location()
    {
        // Create properties at different locations
        Property::factory()->create([
            'landlord_id' => $this->landlord->id,
            'latitude' => 42.3601,
            'longitude' => -71.0589, // Boston
            'status' => 'active',
        ]);

        Property::factory()->create([
            'landlord_id' => $this->landlord->id,
            'latitude' => 40.7128,
            'longitude' => -74.0060, // New York
            'status' => 'active',
        ]);

        // Search within 50km of Boston
        $response = $this->getJson('/api/properties?lat=42.3601&lng=-71.0589&radius=50');

        $response->assertStatus(200);
        $this->assertEquals(1, count($response->json('data')));
    }

    public function test_property_validation_rules()
    {
        Sanctum::actingAs($this->landlord);

        $invalidData = [
            'title' => '', // Required
            'rent_amount' => -100, // Must be positive
            'bedrooms' => -1, // Must be positive
        ];

        $response = $this->postJson('/api/properties', $invalidData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['title', 'rent_amount', 'bedrooms']);
    }
}
