<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Yacht;
use Illuminate\Auth\Access\Response;

class YachtPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view yachts
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Yacht $yacht): bool
    {
        // All authenticated users can view yachts
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Super Admin: Can add any yacht
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Captains: Can add their own yacht (if it matches their current_yacht)
        if ($user->hasRole('Captain')) {
            return true; // Will be validated in the component
        }

        // Crew Members: Can add yachts they've worked on
        if ($user->hasRole('Crew Member') || $user->hasRole('crew_member')) {
            return true;
        }

        // Admin: Can add any yacht
        if ($user->hasRole('admin')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Yacht $yacht): bool
    {
        // Super Admin: Can edit any yacht
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Admin: Can edit any yacht
        if ($user->hasRole('admin')) {
            return true;
        }

        // Captains: Can edit their own yacht (if they created it or it matches their current_yacht)
        if ($user->hasRole('Captain')) {
            // Check if yacht was created by this captain
            if ($yacht->created_by_user_id === $user->id) {
                return true;
            }
            // Check if yacht name matches captain's current_yacht
            if ($user->current_yacht && trim($user->current_yacht) === trim($yacht->name)) {
                return true;
            }
        }

        // Crew Members: Can edit yachts they added (if they created it)
        if ($user->hasRole('Crew Member') || $user->hasRole('crew_member')) {
            if ($yacht->created_by_user_id === $user->id) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Yacht $yacht): bool
    {
        // Super Admin: Can delete any yacht
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Admin: Can delete any yacht
        if ($user->hasRole('admin')) {
            return true;
        }

        // Captains: Can delete their own yacht (if they created it)
        if ($user->hasRole('Captain')) {
            if ($yacht->created_by_user_id === $user->id) {
                return true;
            }
        }

        // Crew Members: Cannot delete yachts (read-only after creation)
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Yacht $yacht): bool
    {
        // Only Super Admin and Admin can restore
        return $user->hasRole('super_admin') || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Yacht $yacht): bool
    {
        // Only Super Admin can permanently delete
        return $user->hasRole('super_admin');
    }
}
