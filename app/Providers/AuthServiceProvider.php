<?php

namespace App\Providers;

use App\Models\Property;
use App\Models\Booking;
use App\Policies\PropertyPolicy;
use App\Policies\BookingPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Property::class => PropertyPolicy::class,
        Booking::class => BookingPolicy::class,
    ];

    public function boot(): void
    {
        //
    }
}
