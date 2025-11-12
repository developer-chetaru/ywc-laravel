<?php

namespace App\Policies;

use App\Models\ItineraryRouteReview;
use App\Models\User;

class ItineraryRouteReviewPolicy
{
    public function create(User $user): bool
    {
        return true; // Any authenticated user can create reviews
    }

    public function update(User $user, ItineraryRouteReview $review): bool
    {
        return $review->user_id === $user->id;
    }

    public function delete(User $user, ItineraryRouteReview $review): bool
    {
        // User can delete their own review, or route owner can delete any review
        if ($review->user_id === $user->id) {
            return true;
        }

        return $review->route->isOwnedBy($user);
    }
}

