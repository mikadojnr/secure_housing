<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\Property;
use App\Models\Verification;
use App\Models\Booking;
use App\Models\Message;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@securehousing.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        UserProfile::create([
            'user_id' => $admin->id,
            'user_type' => 'admin',
            'phone' => '+1-555-0100',
        ]);

        // Create test landlord
        $landlord = User::create([
            'name' => 'John Landlord',
            'email' => 'landlord@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        UserProfile::create([
            'user_id' => $landlord->id,
            'user_type' => 'landlord',
            'phone' => '+1-555-0101',
            'bio' => 'Experienced landlord with verified properties near top universities.',
        ]);

        // Create verified identity for landlord
        Verification::create([
            'user_id' => $landlord->id,
            'verification_type' => 'identity',
            'status' => 'verified',
            'provider' => 'jumio',
            'verified_at' => now(),
            'expires_at' => now()->addYears(2),
        ]);

        // Create test student
        $student = User::create([
            'name' => 'Jane Student',
            'email' => 'student@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        UserProfile::create([
            'user_id' => $student->id,
            'user_type' => 'student',
            'phone' => '+1-555-0102',
            'university' => 'Harvard University',
            'student_id' => 'H123456789',
            'bio' => 'Graduate student looking for safe and affordable housing.',
        ]);

        // Create verified identity and student status
        Verification::create([
            'user_id' => $student->id,
            'verification_type' => 'identity',
            'status' => 'verified',
            'provider' => 'jumio',
            'verified_at' => now(),
            'expires_at' => now()->addYears(2),
        ]);

        Verification::create([
            'user_id' => $student->id,
            'verification_type' => 'student',
            'status' => 'verified',
            'provider' => 'manual',
            'verified_at' => now(),
            'expires_at' => now()->addYear(),
        ]);

        // Create sample properties
        $properties = Property::factory(20)->create([
            'landlord_id' => $landlord->id,
            'status' => 'active',
            'is_verified' => true,
        ]);

        // Create additional landlords and properties
        $landlords = User::factory(5)
            ->has(UserProfile::factory()->landlord())
            ->has(Verification::factory()->verified()->state(['verification_type' => 'identity']))
            ->create();

        foreach ($landlords as $landlordUser) {
            Property::factory(rand(2, 8))->create([
                'landlord_id' => $landlordUser->id,
                'status' => 'active',
                'is_verified' => true,
            ]);
        }

        // Create additional students
        $students = User::factory(10)
            ->has(UserProfile::factory()->student())
            ->has(Verification::factory()->verified()->state(['verification_type' => 'identity']))
            ->create();

        // Create some bookings
        foreach ($students->take(5) as $studentUser) {
            $randomProperty = Property::where('status', 'active')->inRandomOrder()->first();

            Booking::create([
                'property_id' => $randomProperty->id,
                'student_id' => $studentUser->id,
                'move_in_date' => now()->addDays(rand(30, 90)),
                'move_out_date' => now()->addDays(rand(365, 730)),
                'total_amount' => $randomProperty->rent_amount * 12,
                'deposit_amount' => $randomProperty->deposit_amount,
                'status' => fake()->randomElement(['pending', 'confirmed', 'active']),
                'payment_status' => 'pending',
            ]);
        }

        // Create some messages
        foreach ($students->take(3) as $studentUser) {
            $randomProperty = Property::where('status', 'active')->inRandomOrder()->first();

            Message::create([
                'sender_id' => $studentUser->id,
                'recipient_id' => $randomProperty->landlord_id,
                'property_id' => $randomProperty->id,
                'content' => 'Hi, I\'m interested in your property. Is it still available?',
            ]);

            Message::create([
                'sender_id' => $randomProperty->landlord_id,
                'recipient_id' => $studentUser->id,
                'property_id' => $randomProperty->id,
                'content' => 'Yes, it\'s still available! Would you like to schedule a viewing?',
            ]);
        }

        $this->command->info('Database seeded successfully!');
        $this->command->info('Test accounts created:');
        $this->command->info('Admin: admin@securehousing.com / password');
        $this->command->info('Landlord: landlord@example.com / password');
        $this->command->info('Student: student@example.com / password');
    }
}
