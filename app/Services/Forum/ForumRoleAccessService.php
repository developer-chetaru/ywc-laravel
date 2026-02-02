<?php

namespace App\Services\Forum;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class ForumRoleAccessService
{
    /**
     * Check if user has access to a category based on role restrictions
     * 
     * @param User $user
     * @param int $categoryId
     * @return bool
     */
    public function canAccessCategory(User $user, int $categoryId): bool
    {
        // Check if category has any role restrictions
        $restrictedRoles = DB::table('forum_category_roles')
            ->where('category_id', $categoryId)
            ->pluck('role_name')
            ->toArray();

        // If no restrictions, everyone can access
        if (empty($restrictedRoles)) {
            return true;
        }

        // Check if user has any of the required roles
        $userRoles = $user->getRoleNames()->toArray();
        
        // Check if user has any matching role
        foreach ($restrictedRoles as $requiredRole) {
            if (in_array($requiredRole, $userRoles)) {
                return true;
            }
        }

        // User doesn't have any required role
        return false;
    }

    /**
     * Check if user has access to a thread based on role restrictions
     * 
     * @param User $user
     * @param int $threadId
     * @return bool
     */
    public function canAccessThread(User $user, int $threadId): bool
    {
        // Check if thread has any role restrictions
        $restrictedRoles = DB::table('forum_thread_roles')
            ->where('thread_id', $threadId)
            ->pluck('role_name')
            ->toArray();

        // If no restrictions, everyone can access
        if (empty($restrictedRoles)) {
            return true;
        }

        // Check if user has any of the required roles
        $userRoles = $user->getRoleNames()->toArray();
        
        // Check if user has any matching role
        foreach ($restrictedRoles as $requiredRole) {
            if (in_array($requiredRole, $userRoles)) {
                return true;
            }
        }

        // User doesn't have any required role
        return false;
    }

    /**
     * Get all role restrictions for a category
     * 
     * @param int $categoryId
     * @return array
     */
    public function getCategoryRoles(int $categoryId): array
    {
        return DB::table('forum_category_roles')
            ->where('category_id', $categoryId)
            ->pluck('role_name')
            ->toArray();
    }

    /**
     * Get all role restrictions for a thread
     * 
     * @param int $threadId
     * @return array
     */
    public function getThreadRoles(int $threadId): array
    {
        return DB::table('forum_thread_roles')
            ->where('thread_id', $threadId)
            ->pluck('role_name')
            ->toArray();
    }

    /**
     * Set role restrictions for a category
     * 
     * @param int $categoryId
     * @param array $roleNames Array of role names (e.g., ['Captain', 'Chief engineer'])
     * @return void
     */
    public function setCategoryRoles(int $categoryId, array $roleNames): void
    {
        // Remove existing restrictions
        DB::table('forum_category_roles')
            ->where('category_id', $categoryId)
            ->delete();

        // Add new restrictions
        if (!empty($roleNames)) {
            $insertData = [];
            foreach ($roleNames as $roleName) {
                $insertData[] = [
                    'category_id' => $categoryId,
                    'role_name' => $roleName,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            DB::table('forum_category_roles')->insert($insertData);
        }
    }

    /**
     * Set role restrictions for a thread
     * 
     * @param int $threadId
     * @param array $roleNames Array of role names (e.g., ['Captain', 'Chief officer'])
     * @return void
     */
    public function setThreadRoles(int $threadId, array $roleNames): void
    {
        // Remove existing restrictions
        DB::table('forum_thread_roles')
            ->where('thread_id', $threadId)
            ->delete();

        // Add new restrictions
        if (!empty($roleNames)) {
            $insertData = [];
            foreach ($roleNames as $roleName) {
                $insertData[] = [
                    'thread_id' => $threadId,
                    'role_name' => $roleName,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            DB::table('forum_thread_roles')->insert($insertData);
        }
    }

    /**
     * Filter categories based on user's role access
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function filterCategoriesByAccess($query, User $user)
    {
        $userRoles = $user->getRoleNames()->toArray();

        return $query->where(function ($q) use ($userRoles) {
            // Categories with no restrictions (accessible to all)
            $q->whereNotExists(function ($subQuery) {
                $subQuery->select(DB::raw(1))
                    ->from('forum_category_roles')
                    ->whereColumn('forum_category_roles.category_id', 'forum_categories.id');
            })
            // OR categories where user has required role
            ->orWhereExists(function ($subQuery) use ($userRoles) {
                $subQuery->select(DB::raw(1))
                    ->from('forum_category_roles')
                    ->whereColumn('forum_category_roles.category_id', 'forum_categories.id')
                    ->whereIn('forum_category_roles.role_name', $userRoles);
            });
        });
    }

    /**
     * Filter threads based on user's role access
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function filterThreadsByAccess($query, User $user)
    {
        $userRoles = $user->getRoleNames()->toArray();

        return $query->where(function ($q) use ($userRoles) {
            // Threads with no restrictions (accessible to all)
            $q->whereNotExists(function ($subQuery) {
                $subQuery->select(DB::raw(1))
                    ->from('forum_thread_roles')
                    ->whereColumn('forum_thread_roles.thread_id', 'forum_threads.id');
            })
            // OR threads where user has required role
            ->orWhereExists(function ($subQuery) use ($userRoles) {
                $subQuery->select(DB::raw(1))
                    ->from('forum_thread_roles')
                    ->whereColumn('forum_thread_roles.thread_id', 'forum_threads.id')
                    ->whereIn('forum_thread_roles.role_name', $userRoles);
            });
        });
    }

    /**
     * Get all available forum roles
     * These are the roles that can be used for forum restrictions
     * 
     * @return array
     */
    public function getAvailableForumRoles(): array
    {
        // These are the main yacht crew roles from RoleSeeder
        return [
            'Captain',
            'Chief officer',
            'Chief engineer',
            'Chef',
            'Chief stewardess',
            'Bosun',
            'Lead deckhand',
            'Deckhand',
            'Stewardess',
            '2nd stewardess',
            '2nd officer',
            '2nd engineer',
            '3rd officer',
            '3rd engineer',
            'ETO',
            'Deck/engineer',
            'Stew/masseuse',
            'Sous chef',
            'Purser',
            'Nurse',
        ];
    }
}
