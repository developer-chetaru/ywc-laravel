<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ShareTemplate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ShareTemplateController extends Controller
{
    /**
     * Get all templates for the authenticated user
     */
    public function index()
    {
        $templates = ShareTemplate::forUser(Auth::id())
            ->orderBy('is_default', 'desc')
            ->orderBy('name', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'templates' => $templates
        ]);
    }

    /**
     * Store a new template
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'document_criteria' => 'nullable|array',
            'permissions' => 'nullable|array',
            'expiry_duration_days' => 'required|integer|min:1|max:3650',
            'default_message' => 'nullable|string|max:1000',
            'is_default' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // If this is set as default, unset other defaults
        if ($request->is_default) {
            ShareTemplate::forUser(Auth::id())
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }

        $template = ShareTemplate::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'description' => $request->description,
            'document_criteria' => $request->document_criteria ?? [],
            'permissions' => $request->permissions ?? [],
            'expiry_duration_days' => $request->expiry_duration_days,
            'default_message' => $request->default_message,
            'is_default' => $request->is_default ?? false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Template created successfully',
            'template' => $template
        ]);
    }

    /**
     * Update a template
     */
    public function update(Request $request, ShareTemplate $shareTemplate)
    {
        // Ensure user owns the template
        if ($shareTemplate->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'You can only update your own templates.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string|max:500',
            'document_criteria' => 'nullable|array',
            'permissions' => 'nullable|array',
            'expiry_duration_days' => 'sometimes|required|integer|min:1|max:3650',
            'default_message' => 'nullable|string|max:1000',
            'is_default' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // If this is set as default, unset other defaults
        if ($request->is_default) {
            ShareTemplate::forUser(Auth::id())
                ->where('id', '!=', $shareTemplate->id)
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }

        $shareTemplate->update($request->only([
            'name',
            'description',
            'document_criteria',
            'permissions',
            'expiry_duration_days',
            'default_message',
            'is_default',
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Template updated successfully',
            'template' => $shareTemplate->fresh()
        ]);
    }

    /**
     * Delete a template
     */
    public function destroy(ShareTemplate $shareTemplate)
    {
        // Ensure user owns the template
        if ($shareTemplate->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'You can only delete your own templates.'
            ], 403);
        }

        $shareTemplate->delete();

        return response()->json([
            'success' => true,
            'message' => 'Template deleted successfully'
        ]);
    }

    /**
     * Apply template to create a share
     */
    public function apply(Request $request, ShareTemplate $shareTemplate)
    {
        // Ensure user owns the template
        if ($shareTemplate->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'You can only use your own templates.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'document_ids' => 'required|array|min:1',
            'document_ids.*' => 'exists:documents,id',
            'recipient_email' => 'required|email',
            'recipient_name' => 'nullable|string|max:255',
            'personal_message' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Use DocumentShareService to create share with template settings
        $shareService = app(\App\Services\Documents\DocumentShareService::class);
        
        $expiresAt = \Carbon\Carbon::now()->addDays($shareTemplate->expiry_duration_days);
        $message = $request->personal_message ?? $shareTemplate->default_message ?? 'Shared documents for your reference.';

        try {
            $share = $shareService->createShare(
                Auth::user(),
                $request->document_ids,
                $request->recipient_email,
                $request->recipient_name,
                $message,
                $expiresAt,
                $shareTemplate->permissions ?? []
            );
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Documents shared successfully using template',
            'share' => $share
        ]);
    }
}
