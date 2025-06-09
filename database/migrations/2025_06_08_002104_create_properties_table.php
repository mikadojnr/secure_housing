<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('landlord_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->enum('property_type', ['apartment', 'house', 'room', 'studio', 'shared']);
            $table->decimal('rent_amount', 10, 2);
            $table->decimal('deposit_amount', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->string('address');
            $table->string('city');
            $table->string('state');
            $table->string('postal_code');
            $table->string('country');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->integer('bedrooms');
            $table->integer('bathrooms');
            $table->integer('max_occupants');
            $table->json('amenities')->nullable();
            $table->json('utilities_included')->nullable();
            $table->json('house_rules')->nullable();
            $table->date('available_from');
            $table->date('available_until')->nullable();
            $table->enum('status', ['draft', 'active', 'rented', 'inactive'])->default('draft');
            $table->boolean('is_verified')->default(false);
            $table->decimal('trust_score', 3, 2)->default(0);
            $table->integer('views_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
