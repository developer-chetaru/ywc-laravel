<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
    /**
     * Determine if the user can view the document.
     */
    public function view(User $user, Document $document): bool
    {
        // User can view their own documents
        if ($document->user_id === $user->id) {
            return true;
        }

        // Super admin and admin can view any document
        if ($user->hasRole(['super_admin', 'admin'])) {
            return true;
        }

        // Check if document is shared with user (future enhancement)
        // For now, only owner and admins can view

        return false;
    }

    /**
     * Determine if the user can update the document.
     */
    public function update(User $user, Document $document): bool
    {
        // User can update their own documents
        if ($document->user_id === $user->id) {
            return true;
        }

        // Super admin and admin can update any document
        if ($user->hasRole(['super_admin', 'admin'])) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can delete the document.
     */
    public function delete(User $user, Document $document): bool
    {
        // User can delete their own documents
        if ($document->user_id === $user->id) {
            return true;
        }

        // Super admin and admin can delete any document
        if ($user->hasRole(['super_admin', 'admin'])) {
            return true;
        }

        return false;
    }
}
