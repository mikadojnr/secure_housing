<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;

class ReviewPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Review $review): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->user_type === 'student';
    }

    public function update(User $user, Review $review): bool
    {
        return $user->id === $review->student_id;
    }

    public function delete(User $user, Review $review): bool
    {
        return $user->id === $review->student_id || $user->user_type === 'admin';
    }
}
