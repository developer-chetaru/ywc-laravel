<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Document;
use App\Models\EmployerCrew;
use App\Models\JobPosting;
use App\Models\IssuedCertificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    /**
     * User dashboard analytics
     */
    public function userDashboard()
    {
        $user = Auth::user();

        // Document statistics
        $documentStats = [
            'total' => $user->documents()->count(),
            'approved' => $user->documents()->where('status', 'approved')->count(),
            'pending' => $user->documents()->where('status', 'pending')->count(),
            'rejected' => $user->documents()->where('status', 'rejected')->count(),
            'expired' => $user->documents()->whereNotNull('expiry_date')
                ->where('expiry_date', '<', now())->count(),
            'expiring_soon' => $user->documents()->whereNotNull('expiry_date')
                ->whereBetween('expiry_date', [now(), now()->addDays(30)])->count(),
        ];

        // Document timeline (last 12 months)
        $documentTimeline = $user->documents()
            ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), DB::raw('count(*) as count'))
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Documents by type
        $documentsByType = $user->documents()
            ->select('type', DB::raw('count(*) as count'))
            ->groupBy('type')
            ->get();

        // Verification levels
        $verificationStats = Document::where('user_id', $user->id)
            ->join('document_verifications', 'documents.id', '=', 'document_verifications.document_id')
            ->join('verification_levels', 'document_verifications.verification_level_id', '=', 'verification_levels.id')
            ->select('verification_levels.name', DB::raw('count(*) as count'))
            ->groupBy('verification_levels.name')
            ->get();

        // Profile completion score
        $profileScore = $this->calculateProfileCompletion($user);

        return view('analytics.user-dashboard', compact(
            'documentStats',
            'documentTimeline',
            'documentsByType',
            'verificationStats',
            'profileScore'
        ));
    }

    /**
     * Employer analytics dashboard
     */
    public function employerDashboard()
    {
        $employer = Auth::user();

        // Crew statistics
        $crewStats = [
            'total' => EmployerCrew::where('employer_id', $employer->id)->count(),
            'active' => EmployerCrew::where('employer_id', $employer->id)->where('status', 'active')->count(),
            'inactive' => EmployerCrew::where('employer_id', $employer->id)->where('status', 'inactive')->count(),
            'pending' => EmployerCrew::where('employer_id', $employer->id)->where('status', 'pending')->count(),
        ];

        // Crew growth timeline (last 12 months)
        $crewGrowth = EmployerCrew::where('employer_id', $employer->id)
            ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), DB::raw('count(*) as count'))
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Compliance metrics
        $complianceMetrics = $this->calculateEmployerComplianceMetrics($employer->id);

        // Top vessels by crew count
        $topVessels = EmployerCrew::where('employer_id', $employer->id)
            ->select('vessel_name', DB::raw('count(*) as crew_count'))
            ->whereNotNull('vessel_name')
            ->groupBy('vessel_name')
            ->orderByDesc('crew_count')
            ->limit(10)
            ->get();

        // Position distribution
        $positionDistribution = EmployerCrew::where('employer_id', $employer->id)
            ->select('position', DB::raw('count(*) as count'))
            ->whereNotNull('position')
            ->groupBy('position')
            ->orderByDesc('count')
            ->get();

        // Contract expiry alerts
        $expiringContracts = EmployerCrew::where('employer_id', $employer->id)
            ->where('status', 'active')
            ->whereNotNull('contract_end_date')
            ->whereBetween('contract_end_date', [now(), now()->addDays(30)])
            ->with('crew')
            ->get();

        return view('analytics.employer-dashboard', compact(
            'crewStats',
            'crewGrowth',
            'complianceMetrics',
            'topVessels',
            'positionDistribution',
            'expiringContracts'
        ));
    }

    /**
     * Generate custom report
     */
    public function reportBuilder(Request $request)
    {
        $user = Auth::user();

        // Report configuration
        $reportTypes = [
            'documents' => 'Document Statistics',
            'compliance' => 'Compliance Report',
            'crew' => 'Crew Management Report',
            'jobs' => 'Job Posting Analytics',
            'certificates' => 'Certificate Issuance Report',
        ];

        $dateRanges = [
            '7days' => 'Last 7 Days',
            '30days' => 'Last 30 Days',
            '90days' => 'Last 90 Days',
            '6months' => 'Last 6 Months',
            '1year' => 'Last Year',
            'custom' => 'Custom Range',
        ];

        // If report is requested, generate it
        if ($request->has('generate')) {
            $reportData = $this->generateReport($request->all());
            return view('analytics.report-results', compact('reportData', 'reportTypes', 'dateRanges'));
        }

        return view('analytics.report-builder', compact('reportTypes', 'dateRanges'));
    }

    /**
     * Export report as CSV
     */
    public function exportReport(Request $request)
    {
        $reportData = $this->generateReport($request->all());

        $filename = 'report-' . now()->format('Y-m-d-His') . '.csv';

        $handle = fopen('php://output', 'w');
        ob_start();

        // Add headers
        if (!empty($reportData['headers'])) {
            fputcsv($handle, $reportData['headers']);
        }

        // Add data rows
        foreach ($reportData['data'] as $row) {
            fputcsv($handle, $row);
        }

        fclose($handle);
        $csv = ob_get_clean();

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Calculate profile completion score
     */
    private function calculateProfileCompletion(User $user): array
    {
        $fields = [
            'first_name' => 5,
            'last_name' => 5,
            'email' => 5,
            'phone' => 5,
            'dob' => 5,
            'nationality' => 5,
            'gender' => 5,
            'profile_photo_path' => 10,
            'years_experience' => 10,
            'current_yacht' => 10,
            'languages' => 10,
            'certifications' => 15,
            'specializations' => 10,
        ];

        $score = 0;
        $missing = [];

        foreach ($fields as $field => $points) {
            if (!empty($user->$field)) {
                if (is_array($user->$field) && count($user->$field) > 0) {
                    $score += $points;
                } elseif (!is_array($user->$field)) {
                    $score += $points;
                }
            } else {
                $missing[] = ucwords(str_replace('_', ' ', $field));
            }
        }

        return [
            'score' => min($score, 100),
            'missing_fields' => $missing,
        ];
    }

    /**
     * Calculate employer compliance metrics
     */
    private function calculateEmployerComplianceMetrics(int $employerId): array
    {
        $crew = EmployerCrew::where('employer_id', $employerId)
            ->where('status', 'active')
            ->with('crew.documents')
            ->get();

        $totalDocuments = 0;
        $expiredDocuments = 0;
        $expiringSoon = 0;
        $verifiedDocuments = 0;

        foreach ($crew as $employerCrew) {
            $documents = $employerCrew->crew->documents;
            $totalDocuments += $documents->count();

            foreach ($documents as $doc) {
                if ($doc->expiry_date && $doc->expiry_date->isPast()) {
                    $expiredDocuments++;
                }
                if ($doc->expiry_date && $doc->expiry_date->isFuture() && $doc->expiry_date->lte(now()->addDays(30))) {
                    $expiringSoon++;
                }
                if ($doc->status === 'approved') {
                    $verifiedDocuments++;
                }
            }
        }

        $complianceRate = $totalDocuments > 0 ? (($totalDocuments - $expiredDocuments) / $totalDocuments) * 100 : 100;

        return [
            'total_documents' => $totalDocuments,
            'expired' => $expiredDocuments,
            'expiring_soon' => $expiringSoon,
            'verified' => $verifiedDocuments,
            'compliance_rate' => round($complianceRate, 1),
        ];
    }

    /**
     * Generate report based on parameters
     */
    private function generateReport(array $params): array
    {
        $type = $params['report_type'] ?? 'documents';
        $dateRange = $this->getDateRange($params);

        switch ($type) {
            case 'documents':
                return $this->generateDocumentReport($dateRange);
            case 'compliance':
                return $this->generateComplianceReport($dateRange);
            case 'crew':
                return $this->generateCrewReport($dateRange);
            case 'jobs':
                return $this->generateJobsReport($dateRange);
            case 'certificates':
                return $this->generateCertificatesReport($dateRange);
            default:
                return ['headers' => [], 'data' => []];
        }
    }

    /**
     * Get date range from parameters
     */
    private function getDateRange(array $params): array
    {
        $range = $params['date_range'] ?? '30days';

        switch ($range) {
            case '7days':
                return [now()->subDays(7), now()];
            case '30days':
                return [now()->subDays(30), now()];
            case '90days':
                return [now()->subDays(90), now()];
            case '6months':
                return [now()->subMonths(6), now()];
            case '1year':
                return [now()->subYear(), now()];
            case 'custom':
                return [
                    Carbon::parse($params['start_date'] ?? now()->subMonth()),
                    Carbon::parse($params['end_date'] ?? now())
                ];
            default:
                return [now()->subDays(30), now()];
        }
    }

    /**
     * Generate document report
     */
    private function generateDocumentReport(array $dateRange): array
    {
        $documents = Document::whereBetween('created_at', $dateRange)
            ->with(['user', 'documentType'])
            ->get();

        $headers = ['Date', 'User', 'Document Type', 'Status', 'Verification', 'Expiry Date'];
        $data = [];

        foreach ($documents as $doc) {
            $data[] = [
                $doc->created_at->format('Y-m-d'),
                $doc->user->first_name . ' ' . $doc->user->last_name,
                $doc->type,
                $doc->status,
                $doc->verifications()->count() > 0 ? 'Verified' : 'Not Verified',
                $doc->expiry_date ? $doc->expiry_date->format('Y-m-d') : 'N/A',
            ];
        }

        return compact('headers', 'data');
    }

    /**
     * Generate compliance report
     */
    private function generateComplianceReport(array $dateRange): array
    {
        // Implementation similar to document report
        return ['headers' => ['Crew', 'Total Docs', 'Expired', 'Compliance %'], 'data' => []];
    }

    /**
     * Generate crew report
     */
    private function generateCrewReport(array $dateRange): array
    {
        // Implementation for crew statistics
        return ['headers' => ['Name', 'Position', 'Vessel', 'Status', 'Contract End'], 'data' => []];
    }

    /**
     * Generate jobs report
     */
    private function generateJobsReport(array $dateRange): array
    {
        // Implementation for job posting statistics
        return ['headers' => ['Job Title', 'Posted Date', 'Status', 'Applications', 'Views'], 'data' => []];
    }

    /**
     * Generate certificates report
     */
    private function generateCertificatesReport(array $dateRange): array
    {
        // Implementation for certificate issuance statistics
        return ['headers' => ['Certificate #', 'Holder', 'Issue Date', 'Expiry', 'Status'], 'data' => []];
    }
}
