<?php

namespace App\Http\Controllers;

use App\Models\ShareTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShareTemplateController extends Controller
{
    /**
     * List all templates
     */
    public function index()
    {
        $templates = ShareTemplate::where('user_id', Auth::id())
            ->orWhere('is_default', true)
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('shares.templates.index', compact('templates'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('shares.templates.create');
    }

    /**
     * Store new template
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            
            // Permissions
            'can_download' => 'boolean',
            'can_print' => 'boolean',
            'can_share' => 'boolean',
            'can_comment' => 'boolean',
            
            // Access Control
            'is_one_time' => 'boolean',
            'max_views' => 'nullable|integer|min:1',
            'require_password' => 'boolean',
            'require_watermark' => 'boolean',
            
            // Time Settings
            'duration_days' => 'nullable|integer|min:1|max:365',
            'has_access_window' => 'boolean',
        ]);

        ShareTemplate::create([
            'user_id' => Auth::id(),
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            
            'can_download' => $request->boolean('can_download', true),
            'can_print' => $request->boolean('can_print', true),
            'can_share' => $request->boolean('can_share', false),
            'can_comment' => $request->boolean('can_comment', false),
            
            'is_one_time' => $request->boolean('is_one_time', false),
            'max_views' => $validated['max_views'] ?? null,
            'require_password' => $request->boolean('require_password', false),
            'require_watermark' => $request->boolean('require_watermark', false),
            
            'duration_days' => $validated['duration_days'] ?? 30,
            'has_access_window' => $request->boolean('has_access_window', false),
            
            'is_default' => false,
            'usage_count' => 0,
        ]);

        return redirect()->route('share-templates.index')
            ->with('success', 'Template created successfully!');
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $template = ShareTemplate::where('user_id', Auth::id())
            ->orWhere('is_default', true)
            ->findOrFail($id);

        // Prevent editing default templates
        if ($template->is_default && $template->user_id !== Auth::id()) {
            abort(403, 'Cannot edit default templates.');
        }

        return view('shares.templates.edit', compact('template'));
    }

    /**
     * Update template
     */
    public function update(Request $request, $id)
    {
        $template = ShareTemplate::where('user_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            
            'can_download' => 'boolean',
            'can_print' => 'boolean',
            'can_share' => 'boolean',
            'can_comment' => 'boolean',
            
            'is_one_time' => 'boolean',
            'max_views' => 'nullable|integer|min:1',
            'require_password' => 'boolean',
            'require_watermark' => 'boolean',
            
            'duration_days' => 'nullable|integer|min:1|max:365',
            'has_access_window' => 'boolean',
        ]);

        $template->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            
            'can_download' => $request->boolean('can_download', true),
            'can_print' => $request->boolean('can_print', true),
            'can_share' => $request->boolean('can_share', false),
            'can_comment' => $request->boolean('can_comment', false),
            
            'is_one_time' => $request->boolean('is_one_time', false),
            'max_views' => $validated['max_views'] ?? null,
            'require_password' => $request->boolean('require_password', false),
            'require_watermark' => $request->boolean('require_watermark', false),
            
            'duration_days' => $validated['duration_days'] ?? 30,
            'has_access_window' => $request->boolean('has_access_window', false),
        ]);

        return redirect()->route('share-templates.index')
            ->with('success', 'Template updated successfully!');
    }

    /**
     * Delete template
     */
    public function destroy($id)
    {
        $template = ShareTemplate::where('user_id', Auth::id())->findOrFail($id);
        $template->delete();

        return redirect()->route('share-templates.index')
            ->with('success', 'Template deleted successfully!');
    }
}
