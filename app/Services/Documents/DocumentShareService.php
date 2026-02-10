<?php

namespace App\Services\Documents;

use App\Models\DocumentShare;
use App\Models\Document;
use App\Models\User;
use App\Models\ShareAuditLog;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Mail\DocumentShareMail;
use Carbon\Carbon;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class DocumentShareService
{
    /**
     * Create a new document share with enhanced features
     */
    public function createShare(
        User $user,
        array $documentIds,
        ?string $recipientEmail = null,
        ?string $recipientName = null,
        ?string $personalMessage = null,
        ?Carbon $expiresAt = null,
        ?array $permissions = null,
        ?string $password = null,
        ?string $restrictToEmail = null,
        bool $generateQrCode = false
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

        // Prepare share data
        $shareData = [
            'user_id' => $user->id,
            'share_token' => $token,
            'token_hash' => $tokenHash,
            'recipient_email' => $recipientEmail,
            'recipient_name' => $recipientName,
            'personal_message' => $personalMessage,
            'expires_at' => $expiresAt,
            'is_active' => true,
        ];

        // Add password protection if provided
        if ($password) {
            $shareData['password_hash'] = Hash::make($password);
        }

        // Add email restriction if provided
        if ($restrictToEmail) {
            $shareData['restrict_to_email'] = $restrictToEmail;
        }

        // Add permissions if provided
        if ($permissions) {
            $shareData = array_merge($shareData, [
                'can_download' => $permissions['can_download'] ?? true,
                'can_print' => $permissions['can_print'] ?? true,
                'can_share' => $permissions['can_share'] ?? false,
                'can_comment' => $permissions['can_comment'] ?? false,
                'is_one_time' => $permissions['is_one_time'] ?? false,
                'max_views' => $permissions['max_views'] ?? null,
                'require_watermark' => $permissions['require_watermark'] ?? false,
            ]);
        }

        // Create share
        $share = DocumentShare::create($shareData);

        // Generate QR code if requested
        if ($generateQrCode) {
            \Log::info('Attempting to generate QR code for share', [
                'share_id' => $share->id,
                'share_token' => $share->share_token,
            ]);
            
            try {
                $qrCodePath = $this->generateQrCode($share);
                \Log::info('QR code generated successfully', [
                    'share_id' => $share->id,
                    'qr_code_path' => $qrCodePath,
                ]);
                
                $updated = $share->update(['qr_code_path' => $qrCodePath]);
                \Log::info('QR code path updated in database', [
                    'share_id' => $share->id,
                    'updated' => $updated,
                    'qr_code_path' => $share->fresh()->qr_code_path,
                ]);
            } catch (\Exception $e) {
                // Log error but don't fail the share creation
                \Log::error('Failed to generate QR code for share ' . $share->id, [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'share_token' => $share->share_token,
                ]);
                // Optionally, you can still create the share without QR code
            }
        } else {
            \Log::info('QR code generation not requested', ['share_id' => $share->id]);
        }

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
     * Generate QR code for document share
     */
    public function generateQrCode(DocumentShare $share): string
    {
        try {
            \Log::info('Starting QR code generation', [
                'share_id' => $share->id,
                'share_token' => $share->share_token,
            ]);
            
            // Ensure qr-codes directory exists
            $qrCodesDir = 'qr-codes';
            if (!Storage::disk('public')->exists($qrCodesDir)) {
                Storage::disk('public')->makeDirectory($qrCodesDir);
                \Log::info('Created qr-codes directory');
            }

            $options = new QROptions([
                'version' => 5,
                'outputType' => QRCode::OUTPUT_IMAGE_PNG,
                'eccLevel' => QRCode::ECC_L,
                'scale' => 5,
                'imageBase64' => false,
            ]);

            $qrCode = new QRCode($options);
            
            // Get share URL - use route helper directly to ensure it works
            $shareUrl = route('documents.share.view', ['token' => $share->share_token]);
            
            \Log::info('Share URL generated', [
                'share_id' => $share->id,
                'share_url' => $shareUrl,
            ]);
            
            if (empty($shareUrl)) {
                throw new \Exception('Share URL is empty. Cannot generate QR code.');
            }
            
            \Log::info('Rendering QR code image');
            $qrImage = $qrCode->render($shareUrl);

            if (empty($qrImage)) {
                throw new \Exception('QR code image generation failed. Empty image returned.');
            }
            
            \Log::info('QR code image rendered successfully', [
                'image_size' => strlen($qrImage),
            ]);

            // Save QR code to storage
            $filename = $qrCodesDir . '/document-share-' . $share->id . '-' . time() . '.png';
            \Log::info('Saving QR code to storage', ['filename' => $filename]);
            
            $saved = Storage::disk('public')->put($filename, $qrImage);

            if (!$saved) {
                throw new \Exception('Failed to save QR code to storage. File: ' . $filename);
            }
            
            \Log::info('QR code saved successfully', [
                'filename' => $filename,
                'file_exists' => Storage::disk('public')->exists($filename),
            ]);

            return $filename;
        } catch (\Exception $e) {
            \Log::error('QR Code Generation Error', [
                'share_id' => $share->id,
                'share_token' => $share->share_token,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
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
