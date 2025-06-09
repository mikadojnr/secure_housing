<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BookingPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view their bookings
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Booking $booking): bool
    {
        // Students can only view their own bookings
        if ($user->user_type === 'student') {
            return $booking->student_id === $user->id;
        }

        // Landlords can only view bookings for their properties
        if ($user->user_type === 'landlord') {
            return $booking->property->landlord_id === $user->id;
        }

        // Admins can view all bookings
        return $user->user_type === 'admin';
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->user_type === 'student';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Booking $booking): bool
    {
        // Only landlords can update bookings (confirm/complete)
        if ($user->user_type === 'landlord') {
            return $booking->property->landlord_id === $user->id;
        }

        // Admins can update any booking
        return $user->user_type === 'admin';
    }

    /**
     * Determine whether the user can cancel the model.
     */
    public function cancel(User $user, Booking $booking): bool
    {
        // Students can cancel their own bookings
        if ($user->user_type === 'student') {
            return $booking->student_id === $user->id;
        }

        // Landlords can cancel bookings for their properties
        if ($user->user_type === 'landlord') {
            return $booking->property->landlord_id === $user->id;
        }

        // Admins can cancel any booking
        return $user->user_type === 'admin';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Booking $booking): bool
    {
        // Only admins can delete bookings
        return $user->user_type === 'admin';
    }
}
