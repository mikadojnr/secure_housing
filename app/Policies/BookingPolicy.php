<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function view(User $user, Booking $booking): bool
    {
        return $user->id === $booking->student_id || $user->id === $booking->property->landlord_id;
    }

    public function update(User $user, Booking $booking): bool
    {
        return $user->id === $booking->student_id || $user->id === $booking->property->landlord_id;
    }

    public function cancel(User $user, Booking $booking): bool
    {
        return $user->id === $booking->student_id || $user->id === $booking->property->landlord_id;
    }
}
