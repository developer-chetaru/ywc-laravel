<?php

namespace App\Services\Documents;

use App\Models\DocumentShare;
use App\Models\Document;
use App\Models\User;
use App\Models\ShareAuditLog;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
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
        ?Carbon $expiresAt = null,
        ?array $permissions = null
    ): DocumentShare {
        // Validate that user owns all documents
        $documents = Document::whereIn('id', $documentIds)
            ->where('user_id', $user->id)
            ->get();

        if ($documents->count() !== count($documentIds)) {
            throw new \Exception('Some documents not found or not owned by user');
        }

        // Generate secure token
        $token = DocumentShare::generateToken();
        $tokenHash = DocumentShare::hashToken($token);

        // Create share
        $share = DocumentShare::create([
            'user_id' => $user->id,
            'share_token' => $token,
            'token_hash' => $tokenHash,
            'recipient_email' => $recipientEmail,
            'recipient_name' => $recipientName,
            'personal_message' => $personalMessage,
            'expires_at' => $expiresAt,
            'is_active' => true,
        ]);

        // Log share creation
        ShareAuditLog::create([
            'shareable_type' => DocumentShare::class,
            'shareable_id' => $share->id,
            'share_type' => 'document',
            'action' => 'created',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
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
        $result = $share->update(['is_active' => false]);

        // Log revocation
        ShareAuditLog::create([
            'shareable_type' => DocumentShare::class,
            'shareable_id' => $share->id,
            'share_type' => 'document',
            'action' => 'revoked',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return $result;
    }

    /**
     * Get share by token (with timing attack prevention)
     */
    public function getShareByToken(string $token): ?DocumentShare
    {
        // Rate limiting to prevent brute force
        $key = 'share-access:' . request()->ip();
        if (RateLimiter::tooManyAttempts($key, 10)) {
            abort(429, 'Too many requests. Please try again later.');
        }
        RateLimiter::hit($key, 60); // 10 attempts per minute

        // Try to find by token (for backward compatibility)
        $share = DocumentShare::where('share_token', $token)
            ->where('is_active', true)
            ->first();

        // If found, verify token hash for security
        if ($share && $share->token_hash) {
            if (!DocumentShare::verifyToken($token, $share->token_hash)) {
                // Log failed attempt
                ShareAuditLog::create([
                    'shareable_type' => DocumentShare::class,
                    'shareable_id' => $share->id ?? null,
                    'share_type' => 'document',
                    'action' => 'failed_access',
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'details' => ['reason' => 'token_mismatch'],
                ]);
                return null;
            }
        }

        return $share;
    }

    /**
     * Record access to a share
     */
    public function recordAccess(DocumentShare $share): void
    {
        if ($share->isValid()) {
            $share->recordAccess();

            // Log access
            ShareAuditLog::create([
                'shareable_type' => DocumentShare::class,
                'shareable_id' => $share->id,
                'share_type' => 'document',
                'action' => 'accessed',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }
    }

    /**
     * Record download
     */
    public function recordDownload(DocumentShare $share): void
    {
        if ($share->isValid()) {
            $share->recordDownload();

            // Log download
            ShareAuditLog::create([
                'shareable_type' => DocumentShare::class,
                'shareable_id' => $share->id,
                'share_type' => 'document',
                'action' => 'downloaded',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }
    }

    /**
     * Report abuse
     */
    public function reportAbuse(DocumentShare $share, ?string $reason = null): void
    {
        $share->reportAbuse();

        // Log abuse report
        ShareAuditLog::create([
            'shareable_type' => DocumentShare::class,
            'shareable_id' => $share->id,
            'share_type' => 'document',
            'action' => 'abuse_reported',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'details' => ['reason' => $reason],
        ]);
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

    /**
     * Create share from template
     */
    public function createShareFromTemplate(
        User $user,
        string $templateName,
        array $documentIds,
        ?string $recipientEmail = null,
        ?string $recipientName = null,
        ?string $personalMessage = null
    ): DocumentShare {
        $templates = [
            'job_application' => [
                'message' => 'Please find attached my documents for your review.',
                'expires_in_days' => 30,
            ],
            'compliance' => [
                'message' => 'Compliance documents as requested.',
                'expires_in_days' => 90,
            ],
            'quick_share' => [
                'message' => 'Shared documents for your reference.',
                'expires_in_days' => 7,
            ],
            'long_term' => [
                'message' => 'Documents shared for long-term reference.',
                'expires_in_days' => 365,
            ],
        ];

        $template = $templates[$templateName] ?? $templates['quick_share'];
        
        $expiresAt = Carbon::now()->addDays($template['expires_in_days']);
        $message = $personalMessage ?? $template['message'];

        return $this->createShare(
            $user,
            $documentIds,
            $recipientEmail,
            $recipientName,
            $message,
            $expiresAt
        );
    }
}
