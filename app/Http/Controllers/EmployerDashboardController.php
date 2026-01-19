<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Document;
use App\Models\EmployerCrew;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EmployerDashboardController extends Controller
{
    /**
     * Show the employer dashboard
     */
    public function index(Request $request)
    {
        $employer = Auth::user();

        // Get crew members with their document statistics
        $crewQuery = EmployerCrew::where('employer_id', $employer->id)
            ->with(['crew.documents' => function($query) {
                $query->select('user_id', 'status', 'type', 'expiry_date', 'id')
                      ->whereIn('type', ['passport', 'certificate', 'idvisa']);
            }])
            ->with('crew.roles');

        // Apply filters
        if ($request->filled('status')) {
            $crewQuery->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $crewQuery->whereHas('crew', function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('vessel')) {
            $crewQuery->where('vessel_name', 'like', '%' . $request->vessel . '%');
        }

        $crew = $crewQuery->orderBy('created_at', 'desc')->paginate(15);

        // Calculate statistics
        $stats = [
            'total_crew' => EmployerCrew::where('employer_id', $employer->id)->count(),
            'active_crew' => EmployerCrew::where('employer_id', $employer->id)->where('status', 'active')->count(),
            'pending_crew' => EmployerCrew::where('employer_id', $employer->id)->where('status', 'pending')->count(),
            'expiring_soon' => $this->getExpiringDocumentsCount($employer->id),
            'compliance_rate' => $this->calculateComplianceRate($employer->id),
        ];

        // Get vessels list for filter
        $vessels = EmployerCrew::where('employer_id', $employer->id)
            ->whereNotNull('vessel_name')
            ->distinct()
            ->pluck('vessel_name');

        return view('employer.dashboard', compact('crew', 'stats', 'vessels'));
    }

    /**
     * Show crew member details
     */
    public function showCrewMember($id)
    {
        $employer = Auth::user();

        $employerCrew = EmployerCrew::where('employer_id', $employer->id)
            ->where('crew_id', $id)
            ->with([
                'crew.documents.documentType',
                'crew.documents.passportDetail',
                'crew.documents.idvisaDetail',
                'crew.documents.certificates.certificateType',
                'crew.documents.verifications.verificationLevel',
                'crew.roles'
            ])
            ->firstOrFail();

        $crewMember = $employerCrew->crew;

        // Categorize documents
        $documents = [
            'identity' => $crewMember->documents->whereIn('type', ['passport', 'idvisa']),
            'certificates' => $crewMember->documents->where('type', 'certificate'),
            'medical' => $crewMember->documents->where('type', 'medical'),
            'other' => $crewMember->documents->whereNotIn('type', ['passport', 'idvisa', 'certificate', 'medical']),
        ];

        // Compliance status
        $compliance = $this->getCrewComplianceStatus($crewMember);

        return view('employer.crew-details', compact('employerCrew', 'crewMember', 'documents', 'compliance'));
    }

    /**
     * Add crew member
     */
    public function addCrew(Request $request)
    {
        $validated = $request->validate([
            'crew_email' => 'required|email|exists:users,email',
            'position' => 'nullable|string|max:255',
            'vessel_name' => 'nullable|string|max:255',
            'vessel_imo' => 'nullable|string|max:50',
            'contract_start_date' => 'nullable|date',
            'contract_end_date' => 'nullable|date|after:contract_start_date',
            'status' => 'required|in:active,inactive,pending,terminated',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Find crew member by email
        $crew = User::where('email', $validated['crew_email'])->first();

        if (!$crew) {
            return redirect()->back()->with('error', 'Crew member not found.');
        }

        // Check if already added (including soft-deleted)
        $existingCrew = EmployerCrew::withTrashed()
            ->where('employer_id', Auth::id())
            ->where('crew_id', $crew->id)
            ->first();

        if ($existingCrew) {
            if ($existingCrew->trashed()) {
                // Restore the soft-deleted record and update it
                $existingCrew->restore();
                $existingCrew->update([
                    'position' => $validated['position'] ?? null,
                    'vessel_name' => $validated['vessel_name'] ?? null,
                    'vessel_imo' => $validated['vessel_imo'] ?? null,
                    'contract_start_date' => $validated['contract_start_date'] ?? null,
                    'contract_end_date' => $validated['contract_end_date'] ?? null,
                    'status' => $validated['status'],
                    'notes' => $validated['notes'] ?? null,
                    'added_by' => Auth::id(),
                ]);
                
                return redirect()->route('employer.dashboard')
                    ->with('success', 'Crew member re-added successfully!');
            } else {
                return redirect()->back()->with('error', 'This crew member is already in your team.');
            }
        }

        try {
            EmployerCrew::create([
                'employer_id' => Auth::id(),
                'crew_id' => $crew->id,
                'position' => $validated['position'] ?? null,
                'vessel_name' => $validated['vessel_name'] ?? null,
                'vessel_imo' => $validated['vessel_imo'] ?? null,
                'contract_start_date' => $validated['contract_start_date'] ?? null,
                'contract_end_date' => $validated['contract_end_date'] ?? null,
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
                'added_by' => Auth::id(),
            ]);
            
            return redirect()->route('employer.dashboard')
                ->with('success', 'Crew member added successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to add crew member: ' . $e->getMessage());
        }
    }

    /**
     * Show edit crew page
     */
    public function editCrewPage($id)
    {
        $employer = Auth::user();
        
        $employerCrew = EmployerCrew::where('employer_id', $employer->id)
            ->where('id', $id)
            ->with('crew')
            ->first();

        if (!$employerCrew) {
            return redirect()->route('employer.dashboard')
                ->with('error', 'Crew member not found or you do not have permission to edit this crew member.');
        }

        return view('employer.edit-crew', compact('employerCrew'));
    }

    /**
     * Update crew member
     */
    public function updateCrew(Request $request, $id)
    {
        $employer = Auth::user();
        
        $employerCrew = EmployerCrew::where('employer_id', $employer->id)
            ->where('id', $id)
            ->firstOrFail();

        $validated = $request->validate([
            'position' => 'nullable|string|max:255',
            'vessel_name' => 'nullable|string|max:255',
            'vessel_imo' => 'nullable|string|max:50',
            'contract_start_date' => 'nullable|date',
            'contract_end_date' => 'nullable|date|after:contract_start_date',
            'status' => 'required|in:active,inactive,pending,terminated',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $employerCrew->update($validated);
            return redirect()->back()->with('success', 'Crew member updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update crew member: ' . $e->getMessage());
        }
    }

    /**
     * Remove crew member
     */
    public function removeCrew($id)
    {
        $employer = Auth::user();
        
        $employerCrew = EmployerCrew::where('employer_id', $employer->id)
            ->where('id', $id)
            ->first();

        if (!$employerCrew) {
            return redirect()->route('employer.dashboard')
                ->with('error', 'Crew member not found or you do not have permission to remove this crew member.');
        }

        try {
            $crewName = $employerCrew->crew->first_name . ' ' . $employerCrew->crew->last_name;
            $employerCrew->delete();
            
            return redirect()->route('employer.dashboard')
                ->with('success', "Crew member {$crewName} removed successfully!");
        } catch (\Exception $e) {
            return redirect()->route('employer.dashboard')
                ->with('error', 'Failed to remove crew member: ' . $e->getMessage());
        }
    }

    /**
     * Get compliance report
     */
    public function complianceReport(Request $request)
    {
        $employer = Auth::user();

        $crew = EmployerCrew::where('employer_id', $employer->id)
            ->where('status', 'active')
            ->with(['crew.documents'])
            ->get();

        $complianceData = [];

        foreach ($crew as $employerCrew) {
            $crewMember = $employerCrew->crew;
            $compliance = $this->getCrewComplianceStatus($crewMember);
            
            $complianceData[] = [
                'crew' => $crewMember,
                'employer_crew' => $employerCrew,
                'compliance' => $compliance,
            ];
        }

        // Sort by compliance score
        usort($complianceData, function($a, $b) {
            return $b['compliance']['score'] <=> $a['compliance']['score'];
        });

        return view('employer.compliance-report', compact('complianceData'));
    }

    /**
     * Export compliance report
     */
    public function exportComplianceReport()
    {
        $employer = Auth::user();

        $crew = EmployerCrew::where('employer_id', $employer->id)
            ->where('status', 'active')
            ->with(['crew.documents'])
            ->get();

        $csvData = [];
        $csvData[] = ['Name', 'Email', 'Position', 'Vessel', 'Status', 'Documents Total', 'Expired', 'Expiring Soon', 'Compliance %'];

        foreach ($crew as $employerCrew) {
            $crewMember = $employerCrew->crew;
            $compliance = $this->getCrewComplianceStatus($crewMember);
            
            $csvData[] = [
                $crewMember->first_name . ' ' . $crewMember->last_name,
                $crewMember->email,
                $employerCrew->position ?? 'N/A',
                $employerCrew->vessel_name ?? 'N/A',
                $employerCrew->status,
                $compliance['total'],
                $compliance['expired'],
                $compliance['expiring_soon'],
                round($compliance['score']) . '%',
            ];
        }

        $filename = 'compliance-report-' . now()->format('Y-m-d') . '.csv';
        
        $handle = fopen('php://output', 'w');
        ob_start();
        
        foreach ($csvData as $row) {
            fputcsv($handle, $row);
        }
        
        fclose($handle);
        $csv = ob_get_clean();

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Get count of expiring documents (within 30 days)
     */
    private function getExpiringDocumentsCount($employerId): int
    {
        $crewIds = EmployerCrew::where('employer_id', $employerId)
            ->where('status', 'active')
            ->pluck('crew_id');

        return Document::whereIn('user_id', $crewIds)
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '>', now())
            ->where('expiry_date', '<=', now()->addDays(30))
            ->count();
    }

    /**
     * Calculate overall compliance rate
     */
    private function calculateComplianceRate($employerId): float
    {
        $crew = EmployerCrew::where('employer_id', $employerId)
            ->where('status', 'active')
            ->with(['crew.documents'])
            ->get();

        if ($crew->isEmpty()) {
            return 100.0;
        }

        $totalScore = 0;

        foreach ($crew as $employerCrew) {
            $compliance = $this->getCrewComplianceStatus($employerCrew->crew);
            $totalScore += $compliance['score'];
        }

        return round($totalScore / $crew->count(), 1);
    }

    /**
     * Get compliance status for a crew member
     */
    private function getCrewComplianceStatus($crewMember): array
    {
        $documents = $crewMember->documents;
        
        $total = $documents->count();
        $expired = $documents->filter(function($doc) {
            return $doc->expiry_date && $doc->expiry_date->isPast();
        })->count();

        $expiringSoon = $documents->filter(function($doc) {
            return $doc->expiry_date 
                && $doc->expiry_date->isFuture() 
                && $doc->expiry_date->lte(now()->addDays(30));
        })->count();

        $verified = $documents->filter(function($doc) {
            return $doc->status === 'approved';
        })->count();

        $pending = $documents->filter(function($doc) {
            return $doc->status === 'pending';
        })->count();

        // Calculate compliance score
        $score = 0;
        if ($total > 0) {
            $validDocs = $total - $expired;
            $score = ($validDocs / $total) * 100;
        } else {
            $score = 0;
        }

        return [
            'total' => $total,
            'expired' => $expired,
            'expiring_soon' => $expiringSoon,
            'verified' => $verified,
            'pending' => $pending,
            'score' => $score,
            'status' => $score >= 80 ? 'compliant' : ($score >= 50 ? 'warning' : 'non_compliant'),
        ];
    }
}
