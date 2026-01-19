<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AgencyCandidate;
use App\Models\JobPosting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RecruitmentAgencyController extends Controller
{
    /**
     * Show the agency dashboard
     */
    public function index(Request $request)
    {
        $agency = Auth::user();

        // Get candidates with their documents
        $candidatesQuery = AgencyCandidate::where('agency_id', $agency->id)
            ->with(['candidate.documents', 'candidate.roles']);

        // Apply filters
        if ($request->filled('status')) {
            $candidatesQuery->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $candidatesQuery->whereHas('candidate', function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('position')) {
            $candidatesQuery->where('desired_position', 'like', '%' . $request->position . '%');
        }

        $candidates = $candidatesQuery->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Statistics
        $stats = [
            'total_candidates' => AgencyCandidate::where('agency_id', $agency->id)->count(),
            'active_candidates' => AgencyCandidate::where('agency_id', $agency->id)->where('status', 'active')->count(),
            'placed_candidates' => AgencyCandidate::where('agency_id', $agency->id)->where('status', 'placed')->count(),
            'total_jobs' => JobPosting::where('posted_by', $agency->id)->count(),
            'open_jobs' => JobPosting::where('posted_by', $agency->id)->where('status', 'open')->count(),
        ];

        return view('agency.dashboard', compact('candidates', 'stats'));
    }

    /**
     * Show candidate details
     */
    public function showCandidate($id)
    {
        $agency = Auth::user();

        $agencyCandidate = AgencyCandidate::where('agency_id', $agency->id)
            ->where('candidate_id', $id)
            ->with([
                'candidate.documents.documentType',
                'candidate.documents.passportDetail',
                'candidate.documents.certificates.certificateType',
                'candidate.roles'
            ])
            ->firstOrFail();

        $candidate = $agencyCandidate->candidate;

        // Get matching jobs
        $matchingJobs = $this->findMatchingJobs($agencyCandidate);

        return view('agency.candidate-details', compact('agencyCandidate', 'candidate', 'matchingJobs'));
    }

    /**
     * Add candidate
     */
    public function addCandidate(Request $request)
    {
        $validated = $request->validate([
            'candidate_email' => 'required|email|exists:users,email',
            'desired_position' => 'nullable|string|max:255',
            'desired_vessel_type' => 'nullable|string|max:255',
            'desired_salary_min' => 'nullable|numeric|min:0',
            'desired_salary_max' => 'nullable|numeric|min:0|gte:desired_salary_min',
            'available_from' => 'nullable|date',
            'status' => 'required|in:active,inactive,placed,unavailable',
            'notes' => 'nullable|string|max:2000',
            'tags' => 'nullable|string',
            'priority' => 'nullable|integer|min:0|max:10',
        ]);

        $candidate = User::where('email', $validated['candidate_email'])->first();

        if (!$candidate) {
            return redirect()->back()->with('error', 'Candidate not found.');
        }

        $exists = AgencyCandidate::where('agency_id', Auth::id())
            ->where('candidate_id', $candidate->id)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'This candidate is already in your database.');
        }

        try {
            // Process tags
            $tags = null;
            if (!empty($validated['tags'])) {
                $tags = array_map('trim', explode(',', $validated['tags']));
            }

            AgencyCandidate::create([
                'agency_id' => Auth::id(),
                'candidate_id' => $candidate->id,
                'desired_position' => $validated['desired_position'] ?? null,
                'desired_vessel_type' => $validated['desired_vessel_type'] ?? null,
                'desired_salary_min' => $validated['desired_salary_min'] ?? null,
                'desired_salary_max' => $validated['desired_salary_max'] ?? null,
                'available_from' => $validated['available_from'] ?? null,
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
                'tags' => $tags,
                'priority' => $validated['priority'] ?? 0,
                'added_by' => Auth::id(),
            ]);

            return redirect()->back()->with('success', 'Candidate added successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to add candidate: ' . $e->getMessage());
        }
    }

    /**
     * Update candidate
     */
    public function updateCandidate(Request $request, $id)
    {
        $agency = Auth::user();

        $agencyCandidate = AgencyCandidate::where('agency_id', $agency->id)
            ->where('id', $id)
            ->firstOrFail();

        $validated = $request->validate([
            'desired_position' => 'nullable|string|max:255',
            'desired_vessel_type' => 'nullable|string|max:255',
            'desired_salary_min' => 'nullable|numeric|min:0',
            'desired_salary_max' => 'nullable|numeric|min:0|gte:desired_salary_min',
            'available_from' => 'nullable|date',
            'status' => 'required|in:active,inactive,placed,unavailable',
            'notes' => 'nullable|string|max:2000',
            'tags' => 'nullable|string',
            'priority' => 'nullable|integer|min:0|max:10',
        ]);

        try {
            // Process tags
            if (isset($validated['tags'])) {
                $validated['tags'] = array_map('trim', explode(',', $validated['tags']));
            }

            $agencyCandidate->update($validated);
            return redirect()->back()->with('success', 'Candidate updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update candidate: ' . $e->getMessage());
        }
    }

    /**
     * Remove candidate
     */
    public function removeCandidate($id)
    {
        $agency = Auth::user();

        $agencyCandidate = AgencyCandidate::where('agency_id', $agency->id)
            ->where('id', $id)
            ->firstOrFail();

        try {
            $agencyCandidate->delete();
            return redirect()->back()->with('success', 'Candidate removed from your database.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to remove candidate: ' . $e->getMessage());
        }
    }

    /**
     * Job postings list
     */
    public function jobs(Request $request)
    {
        $agency = Auth::user();

        $jobsQuery = JobPosting::where('posted_by', $agency->id);

        // Apply filters
        if ($request->filled('status')) {
            $jobsQuery->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $jobsQuery->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('position', 'like', "%{$search}%")
                  ->orWhere('vessel_name', 'like', "%{$search}%");
            });
        }

        $jobs = $jobsQuery->orderBy('created_at', 'desc')->paginate(15);

        return view('agency.jobs', compact('jobs'));
    }

    /**
     * Create job posting
     */
    public function createJob(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'position' => 'required|string|max:255',
            'vessel_name' => 'nullable|string|max:255',
            'vessel_type' => 'nullable|string|max:255',
            'vessel_flag' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:255',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0|gte:salary_min',
            'contract_duration' => 'nullable|string|max:100',
            'required_certificates' => 'nullable|string',
            'required_skills' => 'nullable|string',
            'additional_requirements' => 'nullable|string',
            'start_date' => 'nullable|date',
            'application_deadline' => 'nullable|date|after:today',
            'status' => 'required|in:open,closed,filled,draft',
        ]);

        try {
            // Process arrays
            if (!empty($validated['required_certificates'])) {
                $validated['required_certificates'] = array_map('trim', explode(',', $validated['required_certificates']));
            }
            if (!empty($validated['required_skills'])) {
                $validated['required_skills'] = array_map('trim', explode(',', $validated['required_skills']));
            }

            $validated['posted_by'] = Auth::id();

            JobPosting::create($validated);

            return redirect()->route('agency.jobs')->with('success', 'Job posted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create job: ' . $e->getMessage());
        }
    }

    /**
     * Update job posting
     */
    public function updateJob(Request $request, $id)
    {
        $job = JobPosting::where('posted_by', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'position' => 'required|string|max:255',
            'vessel_name' => 'nullable|string|max:255',
            'vessel_type' => 'nullable|string|max:255',
            'vessel_flag' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:255',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0|gte:salary_min',
            'contract_duration' => 'nullable|string|max:100',
            'required_certificates' => 'nullable|string',
            'required_skills' => 'nullable|string',
            'additional_requirements' => 'nullable|string',
            'start_date' => 'nullable|date',
            'application_deadline' => 'nullable|date',
            'status' => 'required|in:open,closed,filled,draft',
        ]);

        try {
            // Process arrays
            if (isset($validated['required_certificates'])) {
                $validated['required_certificates'] = array_map('trim', explode(',', $validated['required_certificates']));
            }
            if (isset($validated['required_skills'])) {
                $validated['required_skills'] = array_map('trim', explode(',', $validated['required_skills']));
            }

            $job->update($validated);

            return redirect()->back()->with('success', 'Job updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update job: ' . $e->getMessage());
        }
    }

    /**
     * Delete job posting
     */
    public function deleteJob($id)
    {
        $job = JobPosting::where('posted_by', Auth::id())->findOrFail($id);

        try {
            $job->delete();
            return redirect()->back()->with('success', 'Job deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete job: ' . $e->getMessage());
        }
    }

    /**
     * Job matching - find candidates for a job
     */
    public function matchCandidates($jobId)
    {
        $job = JobPosting::where('posted_by', Auth::id())->findOrFail($jobId);

        $candidates = AgencyCandidate::where('agency_id', Auth::id())
            ->where('status', 'active')
            ->with(['candidate.documents'])
            ->get();

        // Calculate match scores
        $matches = $candidates->map(function($candidate) use ($job) {
            return [
                'candidate' => $candidate,
                'score' => $candidate->matchScore($job),
            ];
        })->sortByDesc('score');

        return view('agency.job-matches', compact('job', 'matches'));
    }

    /**
     * Find matching jobs for a candidate
     */
    private function findMatchingJobs(AgencyCandidate $agencyCandidate): \Illuminate\Support\Collection
    {
        $jobs = JobPosting::where('status', 'open')
            ->where('posted_by', Auth::id())
            ->get();

        return $jobs->map(function($job) use ($agencyCandidate) {
            return [
                'job' => $job,
                'score' => $agencyCandidate->matchScore($job),
            ];
        })->sortByDesc('score')->take(10);
    }
}
