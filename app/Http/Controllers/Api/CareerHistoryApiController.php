<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\Document;
use App\Models\PassportDetail;
use App\Models\IdvisaDetail;
use App\Models\Certificate;
use App\Models\CertificateType;
use App\Models\CertificateIssuer;
use App\Models\OtherDocument;
use App\Models\User;
use App\Jobs\ProcessDocumentOcr;
use Carbon\Carbon;
use Imagick;
use thiagoalessio\TesseractOCR\TesseractOCR;
use App\Services\Documents\TesseractOCRWrapper;
use Illuminate\Support\Facades\Mail;
use App\Mail\ShareDocumentMail;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Crypt;

class CareerHistoryApiController extends Controller
{
    /**
     * Get Tesseract executable path
     */
    protected function getTesseractPath(): ?string
    {
        // Method 1: Check config first (manual override via .env)
        $configPath = config('services.tesseract.path');
        if ($configPath && file_exists($configPath) && is_executable($configPath)) {
            \Log::info('Tesseract path from config: ' . $configPath);
            return $configPath;
        }
        
        // Check if exec function is available
        if (!function_exists('exec') && !function_exists('shell_exec')) {
            \Log::warning('exec() and shell_exec() functions are disabled. Cannot detect Tesseract path.');
            return null;
        }

        $output = [];
        $returnVar = 0;
        
        // Method 2: Try direct tesseract --version (most reliable)
        @\exec('tesseract --version 2>&1', $output, $returnVar);
        if ($returnVar === 0) {
            // Command works, but we need full path to avoid namespace issues
            // Try to get full path via which
            $output2 = [];
            @\exec('which tesseract 2>&1', $output2, $returnVar2);
            if ($returnVar2 === 0 && !empty($output2)) {
                $path = trim($output2[0]);
                if (file_exists($path) && is_executable($path)) {
                    \Log::info('Tesseract found via which: ' . $path);
                    return $path;
                }
            }
            // Fallback to common path if which fails
            $commonPath = '/usr/bin/tesseract';
            if (file_exists($commonPath) && is_executable($commonPath)) {
                \Log::info('Tesseract command works, using common path: ' . $commonPath);
                return $commonPath;
            }
        }
        
        // Method 3: Try which tesseract directly
        @\exec('which tesseract 2>&1', $output, $returnVar);
        if ($returnVar === 0 && !empty($output)) {
            $path = trim($output[0]);
            if (file_exists($path) && is_executable($path)) {
                \Log::info('Tesseract found via which: ' . $path);
                return $path;
            }
        }
        
        // Method 4: Try common installation paths
        $commonPaths = [
            '/usr/bin/tesseract',
            '/usr/local/bin/tesseract',
            '/opt/homebrew/bin/tesseract',
            '/bin/tesseract',
        ];
        
        foreach ($commonPaths as $path) {
            if (file_exists($path) && is_executable($path)) {
                @\exec($path . ' --version 2>&1', $output, $returnVar);
                if ($returnVar === 0) {
                    \Log::info('Tesseract found at common path: ' . $path);
                    return $path;
                }
            }
        }
        
        // Method 5: Try shell_exec as fallback
        if (function_exists('shell_exec')) {
            $result = @shell_exec('tesseract --version 2>&1');
            if ($result && strpos($result, 'tesseract') !== false) {
                // Still need full path
                $result2 = @shell_exec('which tesseract 2>&1');
                if ($result2) {
                    $path = trim($result2);
                    if (file_exists($path) && is_executable($path)) {
                        \Log::info('Tesseract found via shell_exec: ' . $path);
                        return $path;
                    }
                }
            }
        }
        
        $pathEnv = getenv('PATH');
        \Log::warning('Tesseract not found. Checked config, common paths, and PATH variable. Server PATH: ' . ($pathEnv ?? 'not set'));
        return null;
    }

    /**
     * Create TesseractOCR instance with proper path configuration
     */
    protected function createTesseractOCR($imagePath): TesseractOCR
    {
        // Get Tesseract path first - must be absolute path
        $tesseractPath = $this->getTesseractPath();
        
        if (!$tesseractPath) {
            throw new \Exception('Tesseract OCR not found. Please ensure Tesseract is installed and accessible.');
        }
        
        // Always resolve to full absolute path to avoid namespace issues with exec()
        $fullPath = $tesseractPath;
        if ($tesseractPath === 'tesseract' || !file_exists($tesseractPath)) {
            // If we got 'tesseract' command or path doesn't exist, try to find the actual path
            $output = [];
            $returnVar = 0;
            
            // Try which command first
            if (function_exists('exec')) {
                @\exec('which tesseract 2>&1', $output, $returnVar);
                if ($returnVar === 0 && !empty($output)) {
                    $fullPath = trim($output[0]);
                }
            }
            
            // If which failed, try common paths
            if (!file_exists($fullPath) || !is_executable($fullPath)) {
                $commonPaths = ['/usr/bin/tesseract', '/usr/local/bin/tesseract', '/bin/tesseract'];
                foreach ($commonPaths as $commonPath) {
                    if (file_exists($commonPath) && is_executable($commonPath)) {
                        $fullPath = $commonPath;
                        break;
                    }
                }
            }
        }
        
        // Verify the path exists and is executable
        if (!file_exists($fullPath) || !is_executable($fullPath)) {
            throw new \Exception("Tesseract executable not found at: {$fullPath}. Please set TESSERACT_PATH in .env file.");
        }
        
        // Use wrapper class to handle namespace exec() issue
        try {
            $wrapper = new TesseractOCRWrapper($imagePath, $fullPath);
            $ocr = $wrapper->getOCR();
            
            \Log::info('TesseractOCR created with absolute path: ' . $fullPath);
            
            return $ocr;
        } catch (\Exception $e) {
            \Log::error('TesseractOCR creation failed: ' . $e->getMessage());
            throw $e;
        }
    }
    /**
     * Upload document (API).
     * Returns JSON.
     */
    public function uploadDocument(Request $request)
    {
        try {
            $documentType = strtolower($request->input('document_type'));

            if (!$documentType) {
                throw ValidationException::withMessages([
                    'document_type' => ['Document type is required.']
                ]);
            }

            // Common base validation
            $rules = [
                'file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
                'dob'  => 'required|date|before_or_equal:today',
            ];

            // Type-specific validation
            switch ($documentType) {
                case 'passport':
                    $rules = array_merge($rules, [
                        'passport_number' => 'required|string|max:20',
                        'issue_country'   => 'required|string|max:100',
                        'issue_date'      => 'required|date|before_or_equal:today',
                        'expiry_date'     => 'required|date|after:today',
                        'dob'             => 'required|date|before:today',
                    ]);
                    break;

                case 'certificate':
                    $rules = array_merge($rules, [
                        'certificate_issuer_id'     => 'required|integer|exists:certificate_issuers,id',
                        'certificate_number'        => 'nullable|string|max:255',
                        'certificateRows'           => 'required|array|min:1',
                        'certificateRows.*.type_id' => 'required|integer|exists:certificate_types,id',
                        'certificateRows.*.issue'   => 'nullable|date|before_or_equal:today',
                        'certificateRows.*.expiry'  => 'nullable|date|after_or_equal:certificateRows.*.issue',
                        'dob'                       => 'required|date|before_or_equal:today',
                    ]);
                    break;

                case 'idvisa':
                    $allowedDocs = ['Schengen visa','B1/B2 visa','Frontier work permit','C1/D visa','Driving license','Identity card'];
                    
                    $rules = array_merge($rules, [
                        'document_name'   => 'required|string|in:' . implode(",", $allowedDocs),
                        'document_number' => 'required|string|max:50',
                        'issue_country'   => 'required|string|max:100',
                        'place_of_issue'  => 'nullable|string|max:100',
                        'issue_date'      => 'required|date|before_or_equal:today',
                        'dob'             => 'required|date|before_or_equal:today',
                        'country_code'    => 'nullable|string|min:2|max:3',
                        'visa_type'       => 'nullable|string|max:50',
                    ]);

                    // visa types that require expiry_date
                    if (in_array($request->document_name, ['Schengen visa','B1/B2 visa','Frontier work permit','C1/D visa'])) {
                        $rules['expiry_date'] = 'required|date|after:issue_date';
                    } else {
                        $rules['expiry_date'] = 'nullable|date|after:issue_date';
                    }
                    break;

                case 'resume':
                    $rules = array_merge($rules, [
                        'doc_name'    => 'nullable|string|max:100',
                        'issue_date'  => 'nullable|date',
                        'expiry_date' => 'nullable|date|after_or_equal:issue_date',
                        'dob'         => 'nullable|date|before_or_equal:today',
                    ]);
                    break;

                case 'other':
                    $rules = array_merge($rules, [
                        'doc_name'    => 'required|string|max:100',
                        'doc_number'  => 'nullable|string|max:100',
                        'issue_date'  => 'nullable|date',
                        'expiry_date' => 'nullable|date|after_or_equal:issue_date',
                        'dob'         => 'nullable|date|before_or_equal:today',
                    ]);
                    break;

                default:
                    throw ValidationException::withMessages([
                        'document_type' => ['Invalid document type.']
                    ]);
            }

            $validated = $request->validate($rules);

            // ✅ Convert all date fields to correct MySQL format
            $convertDate = function ($date) {
                if (!$date) return null;
                try {
                    return Carbon::parse(str_replace('/', '-', $date))->format('Y-m-d');
                } catch (\Exception $e) {
                    return null;
                }
            };

            $validated['issue_date']  = $convertDate($validated['issue_date'] ?? null);
            $validated['expiry_date'] = $convertDate($validated['expiry_date'] ?? null);
            $validated['dob']         = $convertDate($validated['dob'] ?? null);

            // Store file
            $storedPath = $request->file('file')->store('documents', 'public');
            $publicPath = Storage::url($storedPath);

            // Get user ID from authenticated user or fallback to request
            $userId = $request->user()?->id ?? $request->input('user_id');
            
            if (!$userId) {
                return response()->json([
                    'status' => false,
                    'error'  => 'User authentication required. Please provide a valid authentication token or user_id.',
                ], 401);
            }

            // Save main document
            $document = Document::create([
                'user_id'     => $userId,
                'type'        => $documentType,
                'file_path'   => $storedPath,
                'file_type'   => $request->file->getClientOriginalExtension(),
                'file_size'   => (int) ceil($request->file->getSize() / 1024),
                'issue_date'  => $validated['issue_date'],
                'expiry_date' => $validated['expiry_date'],
                'dob'         => $validated['dob'],
                'status'      => 'pending',
                'ocr_status'  => 'pending', // OCR will be processed in background
            ]);

            // Queue OCR processing
            ProcessDocumentOcr::dispatch($document);

            // Automatically create DocumentVerification record for third-party verification
            // Default to level 3 (Employer Verified) if available, otherwise use the first active level
            try {
                $defaultVerificationLevel = \App\Models\VerificationLevel::where('level', 3)
                    ->where('is_active', true)
                    ->first();
                
                if (!$defaultVerificationLevel) {
                    // Fallback to first active level
                    $defaultVerificationLevel = \App\Models\VerificationLevel::where('is_active', true)
                        ->orderBy('level', 'asc')
                        ->first();
                }

                if ($defaultVerificationLevel) {
                    \App\Models\DocumentVerification::create([
                        'document_id' => $document->id,
                        'verification_level_id' => $defaultVerificationLevel->id,
                        'verifier_id' => null, // Will be set when verified by third party
                        'verifier_type' => 'employer', // Default for level 3
                        'status' => 'pending',
                        'notes' => null,
                    ]);
                } else {
                    // Log warning if no verification level found
                    \Log::warning('DocumentVerification not created: No active verification level found', [
                        'document_id' => $document->id
                    ]);
                }
            } catch (\Exception $e) {
                // Log error but don't fail document upload
                \Log::error('Failed to create DocumentVerification', [
                    'document_id' => $document->id,
                    'error' => $e->getMessage()
                ]);
            }

            // Type-specific save
            switch ($documentType) {
                case 'passport':
                    PassportDetail::create([
                        'document_id'     => $document->id,
                        'passport_number' => $validated['passport_number'],
                        'issue_country'   => $validated['issue_country'],
                        'expiry_date'     => $validated['expiry_date'],
                        'dob'             => $validated['dob'],
                    ]);
                    break;

                case 'idvisa':
                    IdvisaDetail::create([
                        'document_id'     => $document->id,
                        'document_name'   => $validated['document_name'],
                        'document_number' => $validated['document_number'],
                        'issue_country'   => $validated['issue_country'],
                        'place_of_issue'  => $validated['place_of_issue'] ?? null,
                        'issue_date'      => $validated['issue_date'],
                        'expiry_date'     => $validated['expiry_date'] ?? null,
                        'country_code'    => strtoupper($validated['country_code']),
                        'visa_type'       => $validated['visa_type'] ?? null,
                        'dob'             => $validated['dob'],
                    ]);
                    break;

                case 'certificate':
                    foreach ($validated['certificateRows'] as $row) {
                        if (empty($row['type_id'])) continue;

                        Certificate::create([
                            'document_id'          => $document->id,
                            'certificate_type_id'  => $row['type_id'],
                            'certificate_issuer_id'=> $validated['certificate_issuer_id'],
                            'certificate_number'   => $validated['certificate_number'] ?? null,
                            'issue_date'           => $convertDate($row['issue'] ?? null),
                            'expiry_date'          => $convertDate($row['expiry'] ?? null),
                            'dob'                  => $validated['dob'],
                        ]);
                    }
                    break;

                case 'resume':
                    OtherDocument::create([
                        'document_id' => $document->id,
                        'doc_name'    => $validated['doc_name'] ?? 'Resume',
                        'doc_number'  => null,
                        'issue_date'  => $validated['issue_date'] ?? null,
                        'expiry_date' => $validated['expiry_date'] ?? null,
                        'dob'         => $validated['dob'] ?? null,
                    ]);
                    break;

                case 'other':
                    OtherDocument::create([
                        'document_id' => $document->id,
                        'doc_name'    => $validated['doc_name'],
                        'doc_number'  => $validated['doc_number'] ?? null,
                        'issue_date'  => $validated['issue_date'] ?? null,
                        'expiry_date' => $validated['expiry_date'] ?? null,
                        'dob'         => $validated['dob'] ?? null,
                    ]);
                    break;
            }

            return response()->json([
                'status'  => true,
                'message' => ucfirst($documentType) . ' uploaded successfully!',
                'data'    => [
                    'document' => $document->fresh(),
                    'file_url' => $publicPath,
                ],
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'error'  => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * List documents of logged-in user with optional pagination & filters.
     */
	public function list(Request $request)
    {
        $query = Document::with([
            'passportDetail',
            'idvisaDetail',
			'certificates',
            'certificates.type',
            'certificates.issuer',
            'otherDocument'
        ])
        ->where('user_id', auth()->id());

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // ✅ Combined Filter → Expiring in next 6 months OR already expired
        if ($request->filled('expiring_within_6_months') && $request->expiring_within_6_months == 1) {
            $today = \Carbon\Carbon::today();
            $sixMonthsLater = $today->clone()->addMonths(6);

            $query->whereNotNull('expiry_date')
                ->where(function($q) use ($today, $sixMonthsLater) {
                    $q->whereDate('expiry_date', '<', $today) // expired
                    ->orWhere(function($q2) use ($today, $sixMonthsLater) { // expiring soon
                        $q2->whereDate('expiry_date', '>=', $today)
                            ->whereDate('expiry_date', '<=', $sixMonthsLater);
                    });
                });
        }

        // Search filter
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('file_type', 'like', "%{$s}%")
                ->orWhere('file_path', 'like', "%{$s}%");
            });
        }

        // Pagination
        $perPage = (int) $request->get('per_page', 10);
        $documents = $query->orderBy('created_at', 'desc')->paginate($perPage)->appends($request->all());

        // Expiry Calculation (unchanged)
        $documents->getCollection()->transform(function ($doc) {

			if (!empty($doc->file_path)) {
                $doc->file_path = asset('storage/' . ltrim($doc->file_path, '/'));
            }

            if (!$doc->expiry_date) {
                $doc->remaining_number = null;
                $doc->remaining_type = 'N/A';
                $doc->is_expiring_soon = false;
                return $doc;
            }

            $today = \Carbon\Carbon::today();
            $expiry = \Carbon\Carbon::parse($doc->expiry_date)->startOfDay();
            $totalMonths = $today->diffInMonths($expiry, false);
            $diffYears = floor(abs($totalMonths) / 12);
            $diffMonths = abs($totalMonths) % 12;

            if ($expiry->greaterThan($today)) {
                if ($totalMonths <= 6) {
                    $doc->remaining_number = $diffMonths ?: 0;
                    $doc->remaining_type = 'MONTHS';
                    $doc->is_expiring_soon = true;
                } elseif ($diffYears >= 1) {
                    $doc->remaining_number = $diffYears;
                    $doc->remaining_type = 'YRS';
                    $doc->is_expiring_soon = false;
                } else {
                    $doc->remaining_number = $diffMonths ?: 0;
                    $doc->remaining_type = 'MONTHS';
                    $doc->is_expiring_soon = false;
                }
            } else {
                $doc->remaining_number = null;
                $doc->remaining_type = 'EXPIRED';
                $doc->is_expiring_soon = true;
            }

            return $doc;
        });

        return response()->json($documents);
    }

    public function listOLd(Request $request)
    {
        $query = Document::with([
            'passportDetail',
            'idvisaDetail',
            'certificates.type',
            'certificates.issuer',
            'otherDocument'
        ])->where('user_id', auth()->id());

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('file_type', 'like', "%{$s}%")
                  ->orWhere('file_path', 'like', "%{$s}%");
            });
        }

        $perPage = (int) $request->get('per_page', 10);
        $documents = $query->orderBy('created_at', 'desc')->paginate($perPage)->appends($request->all());

        // augment each document with remaining/expiry info similar to your controller
        $documents->getCollection()->transform(function ($doc) {
            if (!$doc->expiry_date) {
                $doc->remaining_number = null;
                $doc->remaining_type = 'N/A';
                $doc->is_expiring_soon = false;
                return $doc;
            }

            $today = Carbon::today();
            $expiry = Carbon::parse($doc->expiry_date)->startOfDay();
            $totalMonths = $today->diffInMonths($expiry);
            $diffYears = floor($totalMonths / 12);
            $diffMonths = $totalMonths % 12;

            if ($expiry->greaterThan($today)) {
                if ($totalMonths <= 6) {
                    $doc->remaining_number = $diffMonths ?: 0;
                    $doc->remaining_type = 'MONTHS';
                    $doc->is_expiring_soon = true;
                } elseif ($diffYears >= 1) {
                    $doc->remaining_number = $diffYears;
                    $doc->remaining_type = 'YRS';
                    $doc->is_expiring_soon = false;
                } else {
                    $doc->remaining_number = $diffMonths ?: 0;
                    $doc->remaining_type = 'MONTHS';
                    $doc->is_expiring_soon = false;
                }
            } else {
                $doc->remaining_number = null;
                $doc->remaining_type = 'EXPIRED';
                $doc->is_expiring_soon = true;
            }
            return $doc;
        });

        return response()->json($documents);
    }

    /**
     * Return single document (for edit / view).
     */
    public function showDocument($id)
    {
        $document = Document::with([
            'passportDetail',
            'idvisaDetail',
			'certificates',
            'certificates.type',
            'certificates.issuer',
            'otherDocument'
        ])->where('id', $id)->where('user_id', auth()->id())->first();

        if (!$document) {
            return response()->json(['success' => false, 'message' => 'Document not found'], 404);
        }

        $docData = $document->toArray();
        $docData['file_url'] = $document->file_path ? asset("storage/{$document->file_path}") : null;

        return response()->json(['success' => true, 'document' => $docData]);
    }

    /**
     * Share a document via email (send copy + message).
     * Body: { emails: [...], message: "optional text", include_download_link: true/false }
     */
    public function shareDocument(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'emails' => 'required|string', 
            'documents' => 'required|array|min:1',
        	'message' => 'nullable|string|max:2000'
		]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        // Parse and clean email addresses
        $toEmails = collect(explode(',', $data['emails']))
            ->map(fn($email) => trim($email))
            ->filter()
            ->unique();

        // Validate each email
        foreach ($toEmails as $email) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return response()->json([
                    'success' => false,
                    'message' => "Invalid email address: {$email}"
                ], 422);
            }
        }

        // Fetch documents where is_active = 1 and user owns them
        $documents = Document::whereIn('id', $data['documents'])
            ->where('user_id', auth()->id())
            ->where('is_active', 1)
            ->get();

        if ($documents->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No valid documents found or you do not have permission to share these documents.'
            ], 404);
        }

        // Send email to all recipients
        $authUserName = Auth::user()->name ?? 'System';

		 Mail::to($toEmails->toArray())
            ->send(new ShareDocumentMail($documents->pluck('id')->toArray(), $data['message'] ?? '', $authUserName));


        return response()->json([
            'success' => true,
            'message' => 'Documents shared successfully'
        ]);
    }

    /**
     * Quick toggle share (is_active)
     */
    public function toggleShare(Request $request, $id)
    {
        $document = Document::where('id', $id)->where('user_id', auth()->id())->first();
        if (!$document) {
            return response()->json(['success' => false, 'message' => 'Document not found'], 404);
        }

        $document->is_active = !$document->is_active;
        $document->save();

        return response()->json(['success' => true, 'is_active' => $document->is_active]);
    }

    /**
     * Toggle preview feature (is_preview). Adds/uses documents.is_preview column (boolean).
     */
    public function togglePreview(Request $request, $id)
    {
        $document = Document::where('id', $id)->where('user_id', auth()->id())->first();
        if (!$document) {
            return response()->json(['success' => false, 'message' => 'Document not found'], 404);
        }

        // If model doesn't have is_preview attribute, the migration below will add it.
        $document->is_preview = !$document->is_preview;
        $document->save();

        return response()->json(['success' => true, 'is_preview' => (bool)$document->is_preview]);
    }

    /**
     * Share profile (send selected docs + profile info to list of emails).
     * Body: { user_id: optional (defaults to auth), document_ids: [..], emails: [..], message: optional }
     */

	public function shareProfile(Request $request)
    {
        $user = $request->user();

        if(empty($user->qrcode) || empty($user->profile_url)){
            return response()->json([
                'status' => false,
                'message' => 'QR Code not generated yet. Please contact admin.',
            ], 404);
        }

        $qrUrl = asset($user->qrcode);

        return response()->json([
            'status' => true,
            'message' => 'Share Profile Data',
            'data' => [
                'user_name'    => $user->first_name.' '.$user->last_name,
                'profile_link' => $user->profile_url,
                'qr_code_url'  => $qrUrl,
            ]
        ], 200);
    }


    public function shareProfileOLd(Request $request)
    {
        $user = $request->user(); // Logged-in user

        // Create encrypted profile link
        $encryptedId = encrypt($user->id);
        $profileUrl = url('profile/'.$encryptedId);

        // Generate QR
        $folder = storage_path('app/public/qrcodes');
        if (!file_exists($folder)) {
            mkdir($folder, 0755, true);
        }

        $fileName = 'qr_'.$user->id.'.png';
        $filePath = $folder.'/'.$fileName;

        $options = new QROptions([
            'outputType' => QRCode::OUTPUT_IMAGE_PNG,
            'eccLevel'   => QRCode::ECC_L,
            'scale'      => 6,
        ]);

        $qrcode = new QRCode($options);
        $png = $qrcode->render($profileUrl);

        file_put_contents($filePath, $png);

        return response()->json([
            'status' => true,
            'message' => 'Share Profile Data',
            'data' => [
                'user_name' => $user->name,
                'profile_link' => $profileUrl,
                'qr_code_url' => asset('storage/qrcodes/'.$fileName),
            ]
        ], 200);
    }

    /**
     * OCR / scan endpoint. Re-uses your scan logic but returns JSON.
     * Accepts file under 'docFile'
     */
    public function scan(Request $request)
    {
        $request->validate([
            'docFile' => 'required|file|mimes:jpeg,jpg,png,bmp,tiff,webp,pdf|max:10240',
        ], [
            'docFile.mimes' => 'Only image files (jpeg, jpg, png, bmp, tiff, webp, pdf) are allowed for scanning.',
        ]);

        $file = $request->file('docFile');

        // Prevent ZIP/RAR
        if (in_array(strtolower($file->getClientOriginalExtension()), ['zip', 'rar'])) {
            return response()->json([
                'success' => false,
                'message' => 'ZIP and RAR files are not supported.'
            ], 422);
        }

        $filePath = $file->store('temp');
        $fullPath = storage_path('app/' . $filePath);
        $text = '';

        try {
            $extension = strtolower($file->getClientOriginalExtension());

            if ($extension === 'pdf') {
                // Try Imagick first if available
                if (extension_loaded('imagick') && class_exists('Imagick')) {
                    $imagick = new Imagick();
                    $imagick->setResolution(300, 300);
                    $imagick->readImage($fullPath);

                    foreach ($imagick as $i => $page) {
                        $page->setImageFormat('png');
                        $tmpImage = storage_path("app/temp/page_{$i}.png");
                        
                        if (!is_dir(storage_path('app/temp'))) {
                            mkdir(storage_path('app/temp'), 0755, true);
                        }
                        
                        $page->writeImage($tmpImage);

                        $ocr = $this->createTesseractOCR($tmpImage);
                        $ocr->lang('eng')->psm(3)->oem(1);
                        $text .= $ocr->run() . "\n";

                        unlink($tmpImage);
                    }

                    $imagick->clear();
                    $imagick->destroy();
                } else {
                    // Fallback: Use Ghostscript to convert PDF pages to images, then OCR
                    $gsPath = trim(shell_exec('which gs 2>/dev/null'));
                    
                    if ($gsPath && is_executable($gsPath)) {
                        if (!is_dir(storage_path('app/temp'))) {
                            mkdir(storage_path('app/temp'), 0755, true);
                        }
                        
                        $outputPattern = storage_path("app/temp/pdf_page_%d.png");
                        $gsCommand = escapeshellarg($gsPath) . ' -dNOPAUSE -dBATCH -sDEVICE=png16m -r300 -dFirstPage=1 -dLastPage=100 -sOutputFile=' . escapeshellarg($outputPattern) . ' ' . escapeshellarg($fullPath) . ' 2>&1';
                        
                        exec($gsCommand, $gsOutput, $gsReturnCode);
                        
                        if ($gsReturnCode === 0) {
                            $pageNum = 1;
                            while (true) {
                                $tmpImage = storage_path("app/temp/pdf_page_{$pageNum}.png");
                                
                                if (!file_exists($tmpImage)) {
                                    break;
                                }
                                
                                try {
                                    $ocr = $this->createTesseractOCR($tmpImage);
                                    $ocr->lang('eng')->psm(3)->oem(1);
                                    $text .= $ocr->run() . "\n";
                                } catch (\Exception $e) {
                                    \Log::warning("TesseractOCR failed for PDF page {$pageNum}: " . $e->getMessage());
                                }
                                
                                unlink($tmpImage);
                                $pageNum++;
                                
                                if ($pageNum > 100) break;
                            }
                        } else {
                            // Try pdftotext as last resort
                            $pdftotextPath = trim(shell_exec('which pdftotext 2>/dev/null'));
                            
                            if ($pdftotextPath && is_executable($pdftotextPath)) {
                                $tmpTextFile = storage_path("app/temp/pdf_text_" . time() . ".txt");
                                $pdftotextCommand = escapeshellarg($pdftotextPath) . ' -layout ' . escapeshellarg($fullPath) . ' ' . escapeshellarg($tmpTextFile) . ' 2>&1';
                                exec($pdftotextCommand, $pdftotextOutput, $pdftotextReturnCode);
                                
                                if ($pdftotextReturnCode === 0 && file_exists($tmpTextFile)) {
                                    $text = file_get_contents($tmpTextFile);
                                    unlink($tmpTextFile);
                                }
                            }
                        }
                    } else {
                        // Last resort: Try pdftotext
                        $pdftotextPath = trim(shell_exec('which pdftotext 2>/dev/null'));
                        
                        if ($pdftotextPath && is_executable($pdftotextPath)) {
                            $tmpTextFile = storage_path("app/temp/pdf_text_" . time() . ".txt");
                            
                            if (!is_dir(storage_path('app/temp'))) {
                                mkdir(storage_path('app/temp'), 0755, true);
                            }
                            
                            $pdftotextCommand = escapeshellarg($pdftotextPath) . ' -layout ' . escapeshellarg($fullPath) . ' ' . escapeshellarg($tmpTextFile) . ' 2>&1';
                            exec($pdftotextCommand, $pdftotextOutput, $pdftotextReturnCode);
                            
                            if ($pdftotextReturnCode === 0 && file_exists($tmpTextFile)) {
                                $text = file_get_contents($tmpTextFile);
                                unlink($tmpTextFile);
                            }
                        }
                    }
                }
            } else {
                $ocr = $this->createTesseractOCR($fullPath);
                $ocr->lang('eng')->psm(3)->oem(1);
                $text = $ocr->run();
            }

            if (empty(trim($text))) {
                return response()->json([
                    'success' => false,
                    'message' => 'OCR could not read any text. Please try again.'
                ], 422);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'OCR scanning failed. Error: ' . $e->getMessage()
            ], 500);
        } finally {
            if (file_exists($fullPath)) unlink($fullPath);
        }

        $isPassport = stripos($text, 'passport') !== false;

        return response()->json([
            'success' => true,
            'text' => $text,
            'detected_type' => $isPassport ? 'passport' : null,
        ]);
    }

    /**
     * Issue country list (from config)
     */
    public function issueCountries()
    {
        $countries = config('countries.list', []);
        return response()->json(['success' => true, 'countries' => $countries]);
    }

    /**
     * Certificate type list
     */
  

	public function certificateTypes(Request $request)
    {
        $typeId = $request->query('type_id');

        $query = CertificateType::with(['issuers' => function($q) {
            $q->where('is_active', true)->orderBy('name');
        }])->where('is_active', true)->orderBy('name');

        if ($typeId) {
            $query->where('id', $typeId);
        }

        $types = $query->get();

        // Optional: if type_id provided but not found
        if ($typeId && $types->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Certificate type not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $types->map(function ($type) {
                return [
                    'id' => $type->id,
                    'name' => $type->name,
                    'issuers' => $type->issuers->map(function ($issuer) {
                        return [
                            'id' => $issuer->id,
                            'name' => $issuer->name,
                        ];
                    }),
                ];
            }),
        ]);
    }
}
