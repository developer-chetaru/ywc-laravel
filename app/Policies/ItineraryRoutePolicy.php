<?php

namespace App\Policies;

use App\Models\ItineraryRoute;
use App\Models\User;

class ItineraryRoutePolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, ItineraryRoute $route): bool
    {
        return $route->visibleTo($user);
    }

    public function create(User $user): bool
    {
        return $user !== null;
    }

    public function update(User $user, ItineraryRoute $route): bool
    {
        if ($route->isOwnedBy($user)) {
            return true;
        }

        return $route->crew()
            ->where('user_id', $user->id)
            ->where('status', 'accepted')
            ->whereIn('role', ['owner', 'editor'])
            ->exists();
    }

    public function delete(User $user, ItineraryRoute $route): bool
    {
        return $route->isOwnedBy($user);
    }

    public function copy(?User $user, ItineraryRoute $route): bool
    {
        if (!$user) {
            return false;
        }

        if ($route->isOwnedBy($user)) {
            return true;
        }

        if ($route->visibility === 'public') {
            return true;
        }

        return $route->crew()
            ->where('user_id', $user->id)
            ->where('status', 'accepted')
            ->exists();
    }

    public function publish(User $user, ItineraryRoute $route): bool
    {
        return $route->isOwnedBy($user);
    }

    public function manageCrew(User $user, ItineraryRoute $route): bool
    {
        if ($route->isOwnedBy($user)) {
            return true;
        }

        return $route->crew()
            ->where('user_id', $user->id)
            ->where('status', 'accepted')
            ->where('role', 'owner')
            ->exists();
    }
}

