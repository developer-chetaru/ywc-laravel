<?php

namespace App\Services\Documents;

use App\Models\DocumentShare;
use App\Models\Document;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\DocumentShareMail;
use Carbon\Carbon;

class DocumentShareService
{
    /**
     * Create a new document share
     */
    public function createShare(
        User $user,
        array $documentIds,
        ?string $recipientEmail = null,
        ?string $recipientName = null,
        ?string $personalMessage = null,
        ?Carbon $expiresAt = null
    ): DocumentShare {
        // Validate that user owns all documents
        $documents = Document::whereIn('id', $documentIds)
            ->where('user_id', $user->id)
            ->get();

        if ($documents->count() !== count($documentIds)) {
            throw new \Exception('Some documents not found or not owned by user');
        }

        // Create share
        $share = DocumentShare::create([
            'user_id' => $user->id,
            'share_token' => DocumentShare::generateToken(),
            'recipient_email' => $recipientEmail,
            'recipient_name' => $recipientName,
            'personal_message' => $personalMessage,
            'expires_at' => $expiresAt,
            'is_active' => true,
        ]);

        // Attach documents
        $share->documents()->attach($documentIds);

        // Send email if recipient email provided
        if ($recipientEmail) {
            Mail::to($recipientEmail)->send(new DocumentShareMail($share, $user));
        }

        return $share;
    }

    /**
     * Revoke a share
     */
    public function revokeShare(DocumentShare $share): bool
    {
        return $share->update(['is_active' => false]);
    }

    /**
     * Get share by token
     */
    public function getShareByToken(string $token): ?DocumentShare
    {
        return DocumentShare::where('share_token', $token)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Record access to a share
     */
    public function recordAccess(DocumentShare $share): void
    {
        if ($share->isValid()) {
            $share->recordAccess();
        }
    }

    /**
     * Get all active shares for a user
     */
    public function getUserShares(User $user)
    {
        return DocumentShare::where('user_id', $user->id)
            ->with('documents')
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
