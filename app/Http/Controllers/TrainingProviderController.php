<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\TrainingProvider;
use App\Models\IssuedCertificate;
use App\Models\TrainingCertification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class TrainingProviderController extends Controller
{
    /**
     * Show the training provider dashboard
     */
    public function index(Request $request)
    {
        // Get provider associated with current user
        $provider = TrainingProvider::where('user_id', Auth::id())->firstOrFail();

        // Get issued certificates
        $certificatesQuery = IssuedCertificate::where('provider_id', $provider->id)
            ->with(['user', 'certification', 'issuedBy']);

        // Apply filters
        if ($request->filled('status')) {
            $certificatesQuery->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $certificatesQuery->where(function($q) use ($search) {
                $q->where('certificate_number', 'like', "%{$search}%")
                  ->orWhere('certificate_name', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q2) use ($search) {
                      $q2->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $certificates = $certificatesQuery->orderBy('issue_date', 'desc')->paginate(20);

        // Statistics
        $stats = [
            'total_issued' => IssuedCertificate::where('provider_id', $provider->id)->count(),
            'active' => IssuedCertificate::where('provider_id', $provider->id)->where('status', 'active')->count(),
            'expiring_soon' => IssuedCertificate::where('provider_id', $provider->id)->expiringSoon()->count(),
            'expired' => IssuedCertificate::where('provider_id', $provider->id)->where('status', 'expired')->count(),
            'revoked' => IssuedCertificate::where('provider_id', $provider->id)->where('status', 'revoked')->count(),
        ];

        return view('training-provider.dashboard', compact('provider', 'certificates', 'stats'));
    }

    /**
     * Show issue certificate form
     */
    public function showIssueForm()
    {
        $provider = TrainingProvider::where('user_id', Auth::id())->firstOrFail();
        $certifications = TrainingCertification::where('is_active', true)->get();

        return view('training-provider.issue-certificate', compact('provider', 'certifications'));
    }

    /**
     * Issue a new certificate
     */
    public function issueCertificate(Request $request)
    {
        $provider = TrainingProvider::where('user_id', Auth::id())->firstOrFail();

        $validated = $request->validate([
            'user_email' => 'required|email|exists:users,email',
            'certification_id' => 'nullable|exists:training_certifications,id',
            'certificate_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'issue_date' => 'required|date|before_or_equal:today',
            'expiry_date' => 'nullable|date|after:issue_date',
        ]);

        // Find user
        $user = User::where('email', $validated['user_email'])->first();

        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }

        try {
            // Generate certificate number
            $certificateNumber = IssuedCertificate::generateCertificateNumber($provider->id);

            // Create issued certificate record
            $issuedCert = IssuedCertificate::create([
                'provider_id' => $provider->id,
                'user_id' => $user->id,
                'certification_id' => $validated['certification_id'] ?? null,
                'certificate_number' => $certificateNumber,
                'certificate_name' => $validated['certificate_name'],
                'description' => $validated['description'] ?? null,
                'issue_date' => $validated['issue_date'],
                'expiry_date' => $validated['expiry_date'] ?? null,
                'status' => 'active',
                'issued_by' => Auth::id(),
                'verification_data' => [
                    'verification_url' => route('certificate.verify', $certificateNumber),
                    'issued_at' => now()->toDateTimeString(),
                ],
            ]);

            // Generate PDF certificate
            $this->generateCertificatePDF($issuedCert);

            return redirect()->route('training-provider.dashboard')
                ->with('success', 'Certificate issued successfully!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to issue certificate: ' . $e->getMessage());
        }
    }

    /**
     * View certificate details
     */
    public function viewCertificate($id)
    {
        $provider = TrainingProvider::where('user_id', Auth::id())->firstOrFail();

        $certificate = IssuedCertificate::where('provider_id', $provider->id)
            ->with(['user', 'certification', 'issuedBy', 'revokedBy'])
            ->findOrFail($id);

        return view('training-provider.certificate-details', compact('certificate', 'provider'));
    }

    /**
     * Revoke certificate
     */
    public function revokeCertificate(Request $request, $id)
    {
        $provider = TrainingProvider::where('user_id', Auth::id())->firstOrFail();

        $certificate = IssuedCertificate::where('provider_id', $provider->id)->findOrFail($id);

        $validated = $request->validate([
            'revocation_reason' => 'required|string|max:500',
        ]);

        try {
            $certificate->revoke($validated['revocation_reason'], Auth::id());

            return redirect()->back()->with('success', 'Certificate revoked successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to revoke certificate: ' . $e->getMessage());
        }
    }

    /**
     * Reactivate certificate
     */
    public function reactivateCertificate($id)
    {
        $provider = TrainingProvider::where('user_id', Auth::id())->firstOrFail();

        $certificate = IssuedCertificate::where('provider_id', $provider->id)->findOrFail($id);

        try {
            $certificate->reactivate();

            return redirect()->back()->with('success', 'Certificate reactivated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to reactivate certificate: ' . $e->getMessage());
        }
    }

    /**
     * Download certificate PDF
     */
    public function downloadCertificate($id)
    {
        $provider = TrainingProvider::where('user_id', Auth::id())->firstOrFail();

        $certificate = IssuedCertificate::where('provider_id', $provider->id)
            ->with(['user', 'provider', 'certification'])
            ->findOrFail($id);

        $pdf = Pdf::loadView('training-provider.certificate-pdf', compact('certificate'));

        return $pdf->download('certificate-' . $certificate->certificate_number . '.pdf');
    }

    /**
     * Public certificate verification
     */
    public function verifyCertificate($certificateNumber)
    {
        $certificate = IssuedCertificate::where('certificate_number', $certificateNumber)
            ->with(['user', 'provider', 'certification'])
            ->first();

        if (!$certificate) {
            return view('training-provider.verify-certificate', [
                'found' => false,
                'message' => 'Certificate not found or invalid certificate number.'
            ]);
        }

        return view('training-provider.verify-certificate', [
            'found' => true,
            'certificate' => $certificate
        ]);
    }

    /**
     * Generate certificate PDF and save to storage
     */
    private function generateCertificatePDF($certificate): void
    {
        try {
            $pdf = Pdf::loadView('training-provider.certificate-pdf', compact('certificate'));
            
            $filename = 'certificates/' . $certificate->certificate_number . '.pdf';
            $path = storage_path('app/public/' . $filename);
            
            // Ensure directory exists
            if (!file_exists(dirname($path))) {
                mkdir(dirname($path), 0755, true);
            }

            $pdf->save($path);

            $certificate->update(['certificate_file_path' => $filename]);
        } catch (\Exception $e) {
            \Log::error('Failed to generate certificate PDF: ' . $e->getMessage());
        }
    }

    /**
     * Bulk certificate issuance
     */
    public function bulkIssueCertificates(Request $request)
    {
        $provider = TrainingProvider::where('user_id', Auth::id())->firstOrFail();

        $validated = $request->validate([
            'certificates' => 'required|array|min:1',
            'certificates.*.user_email' => 'required|email|exists:users,email',
            'certificates.*.certificate_name' => 'required|string|max:255',
            'certificates.*.issue_date' => 'required|date',
            'certificates.*.expiry_date' => 'nullable|date|after:certificates.*.issue_date',
        ]);

        $issued = 0;
        $failed = 0;

        foreach ($validated['certificates'] as $certData) {
            try {
                $user = User::where('email', $certData['user_email'])->first();
                
                if (!$user) {
                    $failed++;
                    continue;
                }

                $certificateNumber = IssuedCertificate::generateCertificateNumber($provider->id);

                $issuedCert = IssuedCertificate::create([
                    'provider_id' => $provider->id,
                    'user_id' => $user->id,
                    'certificate_number' => $certificateNumber,
                    'certificate_name' => $certData['certificate_name'],
                    'issue_date' => $certData['issue_date'],
                    'expiry_date' => $certData['expiry_date'] ?? null,
                    'status' => 'active',
                    'issued_by' => Auth::id(),
                ]);

                $this->generateCertificatePDF($issuedCert);
                $issued++;

            } catch (\Exception $e) {
                $failed++;
                \Log::error('Bulk certificate issuance error: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', "Issued {$issued} certificates successfully. {$failed} failed.");
    }
}
