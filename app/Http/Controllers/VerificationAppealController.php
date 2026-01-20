<?php

namespace App\Http\Controllers;

use App\Models\VerificationAppeal;
use App\Models\DocumentVerification;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class VerificationAppealController extends Controller
{
    /**
     * List all appeals (Admin view)
     */
    public function index(Request $request)
    {
        $query = VerificationAppeal::with(['document', 'user', 'verification', 'assignedTo', 'reviewedBy']);

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        $appeals = $query->latest()->paginate(20);

        // Stats
        $stats = [
            'total' => VerificationAppeal::count(),
            'pending' => VerificationAppeal::pending()->count(),
            'under_review' => VerificationAppeal::underReview()->count(),
            'resolved' => VerificationAppeal::resolved()->count(),
            'high_priority' => VerificationAppeal::highPriority()->count(),
        ];

        return view('verification.appeals.index', compact('appeals', 'stats'));
    }

    /**
     * Show create appeal form
     */
    public function create($verificationId)
    {
        $verification = DocumentVerification::with(['document', 'verificationLevel'])->findOrFail($verificationId);

        // Check authorization
        if ($verification->document->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        return view('verification.appeals.create', compact('verification'));
    }

    /**
     * Store new appeal
     */
    public function store(Request $request, $verificationId)
    {
        $verification = DocumentVerification::with('document')->findOrFail($verificationId);

        // Check authorization
        if ($verification->document->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:2000',
            'disputed_fields' => 'nullable|array',
            'supporting_evidence' => 'nullable|string|max:5000',
            'evidence_files.*' => 'nullable|file|max:10240', // 10MB max
            'priority' => 'nullable|in:1,2,3',
        ]);

        // Handle file uploads
        $evidenceFiles = [];
        if ($request->hasFile('evidence_files')) {
            foreach ($request->file('evidence_files') as $file) {
                $path = $file->store('appeal-evidence', 'private');
                $evidenceFiles[] = [
                    'path' => $path,
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                ];
            }
        }

        $appeal = VerificationAppeal::create([
            'verification_id' => $verificationId,
            'document_id' => $verification->document_id,
            'user_id' => Auth::id(),
            'reason' => $validated['reason'],
            'disputed_fields' => $validated['disputed_fields'] ?? null,
            'supporting_evidence' => $validated['supporting_evidence'] ?? null,
            'evidence_files' => $evidenceFiles,
            'original_decision' => $verification->status,
            'priority' => $validated['priority'] ?? 3,
        ]);

        return redirect()->route('verification.appeals.show', $appeal->id)
            ->with('success', 'Appeal submitted successfully. Reference: ' . $appeal->appeal_reference);
    }

    /**
     * Show appeal details
     */
    public function show($id)
    {
        $appeal = VerificationAppeal::with(['document', 'user', 'verification', 'assignedTo', 'reviewedBy'])
            ->findOrFail($id);

        // Check authorization
        if ($appeal->user_id !== Auth::id() && !Auth::user()->hasRole(['super_admin', 'admin', 'verifier'])) {
            abort(403, 'Unauthorized');
        }

        return view('verification.appeals.show', compact('appeal'));
    }

    /**
     * Assign appeal to verifier
     */
    public function assign(Request $request, $id)
    {
        $appeal = VerificationAppeal::findOrFail($id);

        $validated = $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        $appeal->assign($validated['assigned_to']);

        return redirect()->back()->with('success', 'Appeal assigned successfully.');
    }

    /**
     * Review appeal
     */
    public function review(Request $request, $id)
    {
        $appeal = VerificationAppeal::findOrFail($id);

        $validated = $request->validate([
            'action' => 'required|in:approve,reject',
            'resolution' => 'required|string|max:2000',
            'review_notes' => 'nullable|string|max:5000',
            'changes_made' => 'nullable|array',
            'new_decision' => 'nullable|string',
        ]);

        // Update review notes
        $appeal->update([
            'review_notes' => $validated['review_notes'] ?? null,
        ]);

        if ($validated['action'] === 'approve') {
            $appeal->approve(
                $validated['resolution'],
                $validated['changes_made'] ?? null,
                Auth::id()
            );

            // Update verification if needed
            if (isset($validated['new_decision'])) {
                $appeal->verification->update([
                    'status' => $validated['new_decision'],
                    'notes' => ($appeal->verification->notes ?? '') . "\n\nAppeal approved: " . $validated['resolution'],
                ]);
            }

            return redirect()->route('verification.appeals.index')
                ->with('success', 'Appeal approved successfully.');
        } else {
            $appeal->reject($validated['resolution'], Auth::id());

            return redirect()->route('verification.appeals.index')
                ->with('success', 'Appeal rejected.');
        }
    }

    /**
     * Withdraw appeal
     */
    public function withdraw($id)
    {
        $appeal = VerificationAppeal::findOrFail($id);

        // Check authorization
        if ($appeal->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        if ($appeal->isResolved()) {
            return redirect()->back()->with('error', 'Cannot withdraw a resolved appeal.');
        }

        $appeal->withdraw();

        return redirect()->route('verification.appeals.index')
            ->with('success', 'Appeal withdrawn successfully.');
    }

    /**
     * Download evidence file
     */
    public function downloadEvidence($id, $fileIndex)
    {
        $appeal = VerificationAppeal::findOrFail($id);

        // Check authorization
        if ($appeal->user_id !== Auth::id() && !Auth::user()->hasRole(['super_admin', 'admin', 'verifier'])) {
            abort(403, 'Unauthorized');
        }

        $files = $appeal->evidence_files ?? [];

        if (!isset($files[$fileIndex])) {
            abort(404, 'File not found.');
        }

        $file = $files[$fileIndex];

        return Storage::disk('private')->download($file['path'], $file['name']);
    }

    /**
     * My appeals (User view)
     */
    public function myAppeals()
    {
        $appeals = VerificationAppeal::with(['document', 'verification', 'reviewedBy'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('verification.appeals.my-appeals', compact('appeals'));
    }
}
