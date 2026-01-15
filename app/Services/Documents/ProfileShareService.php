<?php

namespace App\Services\Documents;

use App\Models\ProfileShare;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\ProfileShareMail;
use Carbon\Carbon;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Support\Facades\Storage;

class ProfileShareService
{
    /**
     * Create a new profile share
     */
    public function createShare(
        User $user,
        array $sharedSections = ['personal_info', 'documents', 'career_history'],
        ?array $documentCategories = null,
        ?array $careerEntryIds = null,
        ?string $recipientEmail = null,
        ?string $recipientName = null,
        ?string $personalMessage = null,
        ?Carbon $expiresAt = null,
        bool $generateQrCode = false
    ): ProfileShare {
        $share = ProfileShare::create([
            'user_id' => $user->id,
            'share_token' => ProfileShare::generateToken(),
            'recipient_email' => $recipientEmail,
            'recipient_name' => $recipientName,
            'personal_message' => $personalMessage,
            'shared_sections' => $sharedSections,
            'document_categories' => $documentCategories,
            'career_entry_ids' => $careerEntryIds,
            'expires_at' => $expiresAt,
            'is_active' => true,
        ]);

        // Generate QR code if requested
        if ($generateQrCode) {
            $qrCodePath = $this->generateQrCode($share);
            $share->update(['qr_code_path' => $qrCodePath]);
        }

        // Send email if recipient email provided
        if ($recipientEmail) {
            Mail::to($recipientEmail)->send(new ProfileShareMail($share, $user));
        }

        return $share;
    }

    /**
     * Generate QR code for profile share
     */
    public function generateQrCode(ProfileShare $share): string
    {
        $options = new QROptions([
            'version' => 5,
            'outputType' => QRCode::OUTPUT_IMAGE_PNG,
            'eccLevel' => QRCode::ECC_L,
            'scale' => 5,
            'imageBase64' => false,
        ]);

        $qrCode = new QRCode($options);
        $shareUrl = $share->share_url;
        $qrImage = $qrCode->render($shareUrl);

        // Save QR code to storage
        $filename = 'qr-codes/profile-share-' . $share->id . '-' . time() . '.png';
        Storage::disk('public')->put($filename, $qrImage);

        return $filename;
    }

    /**
     * Revoke a share
     */
    public function revokeShare(ProfileShare $share): bool
    {
        return $share->update(['is_active' => false]);
    }

    /**
     * Get share by token
     */
    public function getShareByToken(string $token): ?ProfileShare
    {
        return ProfileShare::where('share_token', $token)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Record view access to a share
     */
    public function recordAccess(ProfileShare $share): void
    {
        if ($share->isValid()) {
            $share->recordView();
        }
    }

    /**
     * Get all active shares for a user
     */
    public function getUserShares(User $user)
    {
        return ProfileShare::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
