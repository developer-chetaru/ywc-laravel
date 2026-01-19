<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\DocumentVerification;
use App\Models\VerificationLevel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DocumentVerificationController extends Controller
{
    /**
     * Request verification for a document
     */
    public function requestVerification(Request $request, Document $document)
    {
        // Ensure user owns the document
        if ($document->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'You can only request verification for your own documents.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'verification_level_id' => 'required|exists:verification_levels,id',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $verificationLevel = VerificationLevel::findOrFail($request->verification_level_id);

        // Check if verification already exists for this level
        $existing = DocumentVerification::where('document_id', $document->id)
            ->where('verification_level_id', $verificationLevel->id)
            ->where('status', 'pending')
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'A verification request for this level is already pending.'
            ], 400);
        }

        // Create verification request
        $verification = DocumentVerification::create([
            'document_id' => $document->id,
            'verification_level_id' => $verificationLevel->id,
            'verifier_id' => null, // Will be set when verified
            'verifier_type' => $this->getVerifierTypeForLevel($verificationLevel->level),
            'status' => 'pending',
            'notes' => $request->notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Verification request submitted successfully.',
            'verification' => $verification->load('verificationLevel')
        ]);
    }

    /**
     * Verify (approve/reject) a document
     */
    public function verify(Request $request, Document $document)
    {
        $validator = Validator::make($request->all(), [
            'verification_id' => 'required|exists:document_verifications,id',
            'status' => 'required|in:approved,rejected',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $verification = DocumentVerification::where('document_id', $document->id)
            ->where('id', $request->verification_id)
            ->where('status', 'pending')
            ->firstOrFail();

        // Update verification
        $verification->update([
            'status' => $request->status,
            'verifier_id' => Auth::id(),
            'notes' => $request->notes,
            'verified_at' => now(),
        ]);

        // If approved, update document's verification level
        if ($request->status === 'approved') {
            $verificationLevel = $verification->verificationLevel;
            
            // Update document's verification level if this is higher than current
            if (!$document->verification_level_id || 
                $verificationLevel->level > ($document->verificationLevel->level ?? 0)) {
                $document->update([
                    'verification_level_id' => $verificationLevel->id,
                    'highest_verification_level' => $verificationLevel->level,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Verification ' . $request->status . ' successfully.',
            'verification' => $verification->load('verificationLevel', 'verifier')
        ]);
    }

    /**
     * Get verification queue (pending verifications)
     */
    public function queue(Request $request)
    {
        $user = Auth::user();
        
        // Get pending verifications that this user can verify
        // For now, allow any authenticated user to verify (can be restricted by role later)
        $verifications = DocumentVerification::with([
            'document.user',
            'document.documentType',
            'verificationLevel'
        ])
        ->where('status', 'pending')
        ->orderBy('created_at', 'asc')
        ->paginate(20);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'verifications' => $verifications
            ]);
        }

        return view('verification.queue', [
            'verifications' => $verifications
        ]);
    }

    /**
     * Get verifier type based on verification level
     */
    private function getVerifierTypeForLevel($level)
    {
        return match($level) {
            1 => 'user', // Self-verified
            2 => 'user', // Peer-verified
            3 => 'employer',
            4 => 'training_provider',
            5 => 'official',
            default => 'user',
        };
    }
}
