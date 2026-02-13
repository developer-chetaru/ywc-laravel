<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;
use thiagoalessio\TesseractOCR\TesseractOCR;
use App\Services\Documents\TesseractOCRWrapper;
use Livewire\TemporaryUploadedFile;
use Illuminate\Support\Facades\Log;
use App\Models\CertificateType;
use App\Models\CertificateIssuer;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\PassportDetail;
use App\Models\IdvisaDetail;
use App\Models\Certificate;
use App\Models\OtherDocument;
use App\Models\User;
use App\Jobs\ProcessDocumentOcr;
use App\Services\Documents\VersionService;
use Carbon\Carbon;
use Imagick;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class CareerHistoryController extends Controller
{
    /**
     * Show version history for a document
     */
    public function showVersions(Document $document)
    {
        // Check authorization
        if ($document->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $versions = \App\Models\DocumentVersion::where('document_id', $document->id)
            ->with('creator')
            ->latest('version_number')
            ->get();

        return view('documents.version-history-view', [
            'document' => $document,
            'versions' => $versions
        ]);
    }

    /**
     * Show the Career History page
     */
    public function index(Request $request)
    {
        $certificateTypes = CertificateType::where('is_active', true)
            ->with(['issuers' => function($q) {
                $q->where('is_active', true)->orderBy('name');
            }])->orderBy('name')->get();

        $certificateIssuers = CertificateIssuer::where('is_active', true)->orderBy('name')->get();

        // Load document types grouped by category
        $documentTypes = DocumentType::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->groupBy('category');

        $documents = Document::with([
            'documentType', // Load document type relationship for new document types
            'certificates.type',
            'certificates.issuer',
            'otherDocument',
            'verificationLevel',
            'statusChanges' => function($q) {
                $q->latest()->limit(1); // Get latest status change for notes
            },
        ])->where('user_id', Auth::id())->get();
        
        // Load version counts for all documents to avoid N+1 queries
        if ($documents->isNotEmpty()) {
            $documentIds = $documents->pluck('id');
            $versionCounts = \App\Models\DocumentVersion::whereIn('document_id', $documentIds)
                ->selectRaw('document_id, COUNT(*) as count')
                ->groupBy('document_id')
                ->pluck('count', 'document_id');
            
            // Add version count to each document
            $documents->each(function($doc) use ($versionCounts) {
                $doc->version_count = $versionCounts[$doc->id] ?? 0;
            });
        }

        $documents->transform(function ($doc) {
            if (!$doc->expiry_date) {
                $doc->remaining_number = null;
                $doc->remaining_type = 'N/A';
                $doc->is_expiring_soon = false;
                return $doc;
            }

            $today = \Carbon\Carbon::today();
            $expiry = \Carbon\Carbon::parse($doc->expiry_date)->startOfDay();

            $totalMonths = $today->diffInMonths($expiry);
            $diffYears = floor($totalMonths / 12);
            $diffMonths = $totalMonths % 12;

            if ($expiry->greaterThan($today)) {
                if ($totalMonths <= 6) {
                    $doc->remaining_number = $diffMonths ?: 0;
                    $doc->remaining_type = 'MTH';
                    $doc->is_expiring_soon = true;
                } elseif ($diffYears >= 1) {
                    $doc->remaining_number = $diffYears;
                    $doc->remaining_type = 'YR';
                    $doc->is_expiring_soon = false;
                } else {
                    $doc->remaining_number = $diffMonths ?: 0;
                    $doc->remaining_type = 'MTH';
                    $doc->is_expiring_soon = false;
                }
            } else {
                // Expired
                $doc->remaining_number = null;
                $doc->remaining_type = 'EXPIRED';
                $doc->is_expiring_soon = false;
                $doc->is_expired = true;
            }

            return $doc;
        });

        // Separate expired and expiring soon
        $expiredDocs = $documents->filter(fn($doc) => isset($doc->is_expired) && $doc->is_expired)->values();
        $expiringSoonDocs = $documents->filter(fn($doc) => $doc->is_expiring_soon && (!isset($doc->is_expired) || !$doc->is_expired))->values();
        
        // Sort expired/expiring soon first
        $documents = $documents->sortByDesc(fn($doc) => ($doc->is_expiring_soon || (isset($doc->is_expired) && $doc->is_expired)) ? 1 : 0);

        $share_documents = Document::with(['passportDetail', 'idvisaDetail', 'certificates.type', 'certificates.issuer', 'otherDocument'])
                ->where('user_id', Auth::id())
                ->where('is_active', 1)->get();

        $request = request(); // Laravel request helper

        $query = User::with(['roles'])
          ->withCount([
              'documents as documents_count',
              'documents as pending_count' => fn($q) => $q->where('status', 'pending'),
              'documents as approved_count' => fn($q) => $q->where('status', 'approved'),
              'documents as rejected_count' => fn($q) => $q->where('status', 'rejected'),
          ]);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('id', $search);
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $status = $request->input('status');
            $query->whereHas('documents', fn($q) => $q->where('status', $status));
        }

        // Sort filter
        if ($request->filled('sort')) {
            $sort = $request->input('sort');
            match($sort) {
                'oldest' => $query->orderBy('created_at', 'asc'),
                'newest' => $query->orderBy('created_at', 'desc'),
                'name_asc' => $query->orderBy('first_name', 'asc')->orderBy('last_name', 'asc'),
                'name_desc' => $query->orderBy('first_name', 'desc')->orderBy('last_name', 'desc'),
                'old' => $query->orderBy('created_at', 'asc'), // Legacy support
                default => $query->orderBy('created_at', 'desc'),
            };
        } else {
            // Default sort
            $query->orderBy('created_at', 'desc');
        }

        $users = $query->paginate(10)->appends($request->all());

        if ($request->ajax()) {
            return view('career-history.super-admin-career-history-dashboard', compact('users'));
        }

        return view('career-history.index', compact(
            'certificateTypes',
            'certificateIssuers',
            'documentTypes',
            'documents',
            'share_documents',
            'users'
        ));
    }

    /**
     * Share profile - web route (uses session auth)
     * Returns QR code and profile URL for the authenticated user
     * Automatically generates QR code and profile URL if they don't exist
     */
    public function shareProfile(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        // Generate QR code and profile URL if they don't exist
        if(empty($user->qrcode) || empty($user->profile_url)){
            try {
                $this->generateUserQrCode($user);
            } catch (\Exception $e) {
                Log::error('Failed to generate QR code for user ' . $user->id . ': ' . $e->getMessage());
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to generate QR code. Please try again or contact admin.',
                ], 500);
            }
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

    /**
     * Generate QR code and profile URL for a user
     */
    private function generateUserQrCode(User $user)
    {
        // Generate profile URL
        $encryptedId = Crypt::encryptString($user->id);
        $profileUrl = url('/p/' . urlencode($encryptedId));

        // Generate QR code
        $options = new QROptions([
            'outputType' => QRCode::OUTPUT_IMAGE_PNG,
            'eccLevel'   => QRCode::ECC_H,
            'scale'      => 7,
        ]);

        $qrcode = new QRCode($options);
        $qrImage = $qrcode->render($profileUrl);

        $manager = new ImageManager(new Driver());
        $qr = $manager->read($qrImage);

        // Add logo if it exists
        $logoPath = public_path('images/ywc-logo.png');
        if (file_exists($logoPath)) {
            $logo = $manager->read($logoPath);
            $logoMaxWidth = intval($qr->width() * 0.28);
            $logoMaxHeight = intval($qr->height() * 0.28);
            $logo->scaleDown($logoMaxWidth, $logoMaxHeight);
            $qr->place($logo, 'center');
        }

        $qrImage = $qr->encode()->toString();

        // Save QR code
        $filename = 'user_'.$user->id.'_'.Str::random(6).'.png';
        $path = 'public/qrcodes/' . $filename;
        Storage::put($path, $qrImage);

        // Update user
        $user->qrcode = Storage::url($path);
        $user->profile_url = $profileUrl;
        $user->save();

        return $user;
    }
  
  	public function getDocumentForEdit($id)
    {
        $document = Document::with([
            'passportDetail',
            'idvisaDetail',
            'certificates.type',
            'certificates.issuer',
            'otherDocument',
            'documentType', // Include document type relationship
        ])
        ->where('id', $id)
        ->where('user_id', Auth::id())
        ->first();

        if (!$document) {
            return response()->json(['error' => 'Document not found'], 404);
        }

        // Get document data - explicitly get all fields to ensure they're included
        $docData = [
            'id' => $document->id,
            'user_id' => $document->user_id,
            'type' => $document->type, // Will be overridden below for new types
            'document_type_id' => $document->document_type_id,
            'document_name' => $document->document_name,
            'document_number' => $document->document_number,
            'issuing_authority' => $document->issuing_authority,
            'issuing_country' => $document->issuing_country,
            'issue_date' => $document->issue_date ? $document->issue_date->format('Y-m-d') : null,
            'expiry_date' => $document->expiry_date ? $document->expiry_date->format('Y-m-d') : null,
            'dob' => $document->dob ? $document->dob->format('Y-m-d') : null,
            'status' => $document->status ?? 'pending',
            'file_path' => $document->file_path,
            'file_type' => $document->file_type,
            'file_size' => $document->file_size,
            'is_active' => $document->is_active,
            'ocr_status' => $document->ocr_status,
            'created_at' => $document->created_at ? $document->created_at->toDateTimeString() : null,
            'updated_at' => $document->updated_at ? $document->updated_at->toDateTimeString() : null,
        ];

        // Preserve document id
        $docData['document_id'] = $document->id;

        // For new document types, use document_type slug instead of legacy type
        // This ensures the correct document type is selected in the dropdown
        if ($document->documentType) {
            $docData['type'] = $document->documentType->slug;
            $docData['document_type_slug'] = $document->documentType->slug;
        } else {
            // Fallback to legacy type if no documentType relationship
            $docData['type'] = $document->type;
        }

        // Merge related data without overwriting keys like 'id'
        if ($document->passportDetail) {
            $passportData = $document->passportDetail->toArray();
            unset($passportData['id']); // Remove to prevent overwrite
            $docData['passportDetail'] = $passportData;
        }

        if ($document->idvisaDetail) {
            $idvisaData = $document->idvisaDetail->toArray();
            unset($idvisaData['id']);
            $docData['idvisaDetail'] = $idvisaData;
        }

        if ($document->certificates && count($document->certificates) > 0) {
            $docData['certificates'] = $document->certificates->toArray();
        }

        if ($document->otherDocument) {
            $otherData = $document->otherDocument->toArray();
            unset($otherData['id']);
            $docData['otherDocument'] = $otherData;
        }

        // Include file URL for download button
        $docData['file_url'] = $document->file_path ? asset("storage/{$document->file_path}") : null;

        return response()->json($docData);
    }


    public function update(Request $request, $id)
    {
        $document = Document::findOrFail($id);
        $oldType = $document->type;

        $base = [
            'type' => [
                'required',
                function ($attribute, $value, $fail) {
                    // Check if it's a legacy type
                    $legacyTypes = ['passport', 'idvisa', 'certificate', 'resume', 'other'];
                    if (in_array($value, $legacyTypes)) {
                        return;
                    }
                    
                    // Check if it's a valid document type slug
                    $exists = DocumentType::where('slug', $value)
                        ->where('is_active', true)
                        ->exists();
                    
                    if (!$exists) {
                        $fail('The selected type is invalid.');
                    }
                },
            ],
            'file' => 'nullable|file|max:5120',
            'dob'  => 'nullable|date|before_or_equal:today',
        ];

        $rules = $base;
        $typeInput = $request->input('type');
        
        // Check if it's a new document type (not a legacy type)
        $isNewDocumentType = !in_array($typeInput, ['passport', 'idvisa', 'certificate', 'resume', 'other']);
        $documentType = null;
        
        if ($isNewDocumentType) {
            // Get document type from database to check its requirements
            $documentType = DocumentType::where('slug', $typeInput)->where('is_active', true)->first();
            
            if ($documentType) {
                // Build validation rules based on document type requirements
                $newTypeRules = [];
                
                if ($documentType->requires_document_number) {
                    $newTypeRules['document_number'] = 'required|string|max:255';
                } else {
                    $newTypeRules['document_number'] = 'nullable|string|max:255';
                }
                
                if ($documentType->requires_expiry_date) {
                    $newTypeRules['expiry_date'] = 'required|date|after_or_equal:issue_date';
                } else {
                    $newTypeRules['expiry_date'] = 'nullable|date|after_or_equal:issue_date';
                }
                
                if ($documentType->requires_issuing_authority) {
                    $newTypeRules['issuing_authority'] = 'required|string|max:255';
                } else {
                    $newTypeRules['issuing_authority'] = 'nullable|string|max:255';
                }
                
                // Common fields
                $newTypeRules['issue_date'] = 'nullable|date|before_or_equal:today';
                $newTypeRules['issuing_country'] = 'nullable|string|max:255';
                $newTypeRules['dob'] = 'nullable|date|before_or_equal:today';
                
                $rules = array_merge($base, $newTypeRules);
            } else {
                // If document type not found, treat as 'other'
                $rules = array_merge($base, [
                    'doc_name'   => 'required|string|max:100',
                    'doc_number' => 'nullable|string|max:100',
                    'issue_date' => 'nullable|date',
                    'expiry_date'=> 'nullable|date|after_or_equal:issue_date',
                  	'dob'        => 'nullable|date|before_or_equal:today',
                ]);
            }
        } else {
            // Legacy types
            switch ($typeInput) {
                case 'passport':
                    $rules = array_merge($base, [
                        'passport_number' => 'required|string|min:6|max:9',
                        'nationality'     => 'required|string|max:50',
                        'country_code'    => 'required|string|min:2|max:3',
                        'issue_date'      => 'required|date|before_or_equal:today',
                        'expiry_date'     => 'required|date|after:issue_date',
                        'dob'             => 'required|date|before_or_equal:today',
                    ]);
                    break;
                case 'idvisa':
                    $rules = array_merge($base, [
                        'document_name'   => 'required|string',
                        'document_number' => 'required|string|max:50',
                        'issue_country'   => 'nullable|string|max:100',
                        'place_of_issue'  => 'nullable|string|max:100',
                        'issue_date'      => 'required|date|before_or_equal:today',
                        'dob'             => 'required|date|before_or_equal:today',
                    ]);
                    if (in_array($request->document_name, ['Schengen visa','B1/B2 visa','Frontier work permit','C1/D visa'])) {
                        $rules['expiry_date']  = 'required|date|after:issue_date';
                        $rules['country_code'] = 'required|string|min:2|max:3';
                    }
                    break;
                case 'certificate':
                    $rules = array_merge($base, [
                        'certificateRows'           => 'required|array|min:1',
                        'certificateRows.*.type_id' => 'required|integer|exists:certificate_types,id',
                        'certificateRows.*.issue'   => 'nullable|date|before_or_equal:today',
                        'certificateRows.*.expiry'  => 'nullable|date|after_or_equal:certificateRows.*.issue',
                        'dob'                       => 'required|date|before_or_equal:today',
                    ]);
                    break;
                case 'resume':
                    $rules = array_merge($base, [
                        'doc_name'   => 'nullable|string|max:100',
                        'issue_date' => 'nullable|date',
                        'expiry_date'=> 'nullable|date|after_or_equal:issue_date',
                        'dob'        => 'nullable|date|before_or_equal:today',
                    ]);
                    break;
                case 'other':
                    $rules = array_merge($base, [
                        'doc_name'   => 'required|string|max:100',
                        'doc_number' => 'nullable|string|max:100',
                        'issue_date' => 'nullable|date',
                        'expiry_date'=> 'nullable|date|after_or_equal:issue_date',
                        'dob'        => 'nullable|date|before_or_equal:today',
                    ]);
                    break;
            }
        }

        try {
            $validated = $request->validate($rules);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed in update:', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            throw $e;
        }
        
        // Get document type for new document types
        if ($isNewDocumentType && !$documentType) {
            $documentType = DocumentType::where('slug', $validated['type'])->where('is_active', true)->first();
        }
        
        // Log for debugging
        \Log::info('Update document:', [
            'document_id' => $id,
            'isNewDocumentType' => $isNewDocumentType,
            'documentType' => $documentType ? $documentType->slug : 'null',
            'validated' => $validated
        ]);

        // Always create version before updating to track all changes
        try {
            $versionService = app(VersionService::class);
            
            // Detect what changed for better change notes
            $changes = [];
            if ($request->hasFile('file')) {
                $changes[] = 'file uploaded';
            }
            if ($oldType !== $validated['type']) {
                $changes[] = 'document type changed';
            }
            
            // Check for metadata changes
            $metadataChanged = false;
            $metadataFields = ['passport_number', 'nationality', 'country_code', 'issue_date', 
                              'expiry_date', 'dob', 'document_name', 'document_number', 
                              'issuing_authority', 'issuing_country'];
            foreach ($metadataFields as $field) {
                if ($request->has($field)) {
                    $metadataChanged = true;
                    break;
                }
            }
            
            if ($metadataChanged) {
                $changes[] = 'metadata updated';
            }
            
            $changeNotes = $request->input('change_notes') ?: 
                          (!empty($changes) ? ucfirst(implode(', ', $changes)) : 'Document updated');
            
            $versionService->createVersion($document, $changeNotes);
        } catch (\Exception $e) {
            \Log::warning('Version creation failed for document ' . $document->id . ': ' . $e->getMessage());
            // Don't fail the update if version creation fails
        }

        // Delete old details if type changed
        // Get old document type slug for comparison
        $oldDocumentType = $document->documentType;
        $oldTypeSlug = $oldDocumentType ? $oldDocumentType->slug : $oldType;
        
        if ($oldTypeSlug !== $validated['type']) {
            // Delete old child records based on old type
            $oldLegacyType = in_array($oldTypeSlug, ['passport', 'idvisa', 'certificate', 'resume', 'other']) 
                ? $oldTypeSlug 
                : 'other';
            
            switch ($oldLegacyType) {
                case 'passport': $document->passportDetail?->delete(); break;
                case 'idvisa': $document->idvisaDetail?->delete(); break;
                case 'certificate': $document->certificates()->delete(); break;
                case 'other': 
                case 'resume': 
                    $document->otherDocument?->delete(); 
                    break;
            }
        }

        // Update file if uploaded
        if ($request->hasFile('file')) {
            if ($document->file_path && \Storage::disk('public')->exists($document->file_path)) {
                \Storage::disk('public')->delete($document->file_path);
            }
            // Delete old thumbnail if exists
            if ($document->thumbnail_path && \Storage::disk('public')->exists($document->thumbnail_path)) {
                \Storage::disk('public')->delete($document->thumbnail_path);
            }
            $storedPath = $request->file('file')->store('documents', 'public');
            $document->file_path = $storedPath;
            $document->file_type = $request->file->getClientOriginalExtension();
            $document->file_size = (int) ceil($request->file->getSize() / 1024);
        }

        // Map 'resume' to 'other' for legacy enum compatibility
        $legacyType = in_array($validated['type'], ['passport', 'idvisa', 'certificate', 'resume', 'other']) 
            ? $validated['type'] 
            : 'other';
        if ($legacyType === 'resume') {
            $legacyType = 'other';
        }

        // Helper function to convert empty strings to null
        $toNull = function($value) {
            return ($value === '' || $value === null) ? null : $value;
        };
        
        $updateData = [
            'type' => $legacyType,
            'issue_date' => $toNull($validated['issue_date'] ?? null),
            'expiry_date' => $toNull($validated['expiry_date'] ?? null),
            'dob' => $toNull($validated['dob'] ?? null),
            'status' => 'pending',
        ];
        
        // For new document types, update document_type_id and related fields
        if ($isNewDocumentType) {
            if ($documentType) {
                $updateData['document_type_id'] = $documentType->id;
                $updateData['document_name'] = $documentType->name;
            }
            // Always update these fields for new document types, even if documentType is null
            // Get from validated first, then fallback to request input to ensure we capture all data
            $updateData['document_number'] = $toNull($validated['document_number'] ?? $request->input('document_number', null));
            $updateData['issuing_authority'] = $toNull($validated['issuing_authority'] ?? $request->input('issuing_authority', null));
            $updateData['issuing_country'] = $toNull($validated['issuing_country'] ?? $request->input('issuing_country', null));
        }

        \Log::info('Updating document with data:', $updateData);
        $document->update($updateData);
        \Log::info('Document updated successfully. Document after update:', $document->fresh()->toArray());

        // Generate thumbnail if file was uploaded
        if ($request->hasFile('file') && $document->id) {
            try {
                $thumbnailService = app(\App\Services\Documents\ThumbnailService::class);
                $thumbnailService->ensureThumbnail($document);
            } catch (\Exception $e) {
                \Log::warning('Thumbnail generation failed for document ' . $document->id . ': ' . $e->getMessage());
                // Don't fail the update if thumbnail generation fails
            }
        }

        // For new document types (not legacy), treat as 'other'
        $typeForSwitch = in_array($validated['type'], ['passport', 'idvisa', 'certificate', 'resume', 'other']) 
            ? $validated['type'] 
            : 'other';
        
        switch ($typeForSwitch) {
            case 'resume':
                // Resume is treated as 'other' in database
                $other = $document->otherDocument ?? new OtherDocument(['document_id' => $document->id]);
                $other->doc_name = $validated['doc_name'] ?? 'Resume';
                $other->doc_number = null;
                $other->issue_date = $validated['issue_date'] ?? null;
                $other->expiry_date = $validated['expiry_date'] ?? null;
                $other->dob = $validated['dob'] ?? null;
                $other->save();
                break;

            case 'passport':
                $passport = $document->passportDetail ?? new PassportDetail(['document_id' => $document->id]);
                $passport->passport_number = $validated['passport_number'];
                $passport->nationality = $validated['nationality'];
                $passport->country_code = strtoupper($validated['country_code']);
                $passport->issue_date = $validated['issue_date'];
                $passport->expiry_date = $validated['expiry_date'];
                $passport->dob = $validated['dob'];
                $passport->save();
                break;

            case 'idvisa':
                $idvisa = $document->idvisaDetail ?? new IdvisaDetail(['document_id' => $document->id]);
                $idvisa->document_name = $validated['document_name'];
                $idvisa->document_number = $validated['document_number'];
                $idvisa->issue_country = $validated['issue_country'];
                $idvisa->place_of_issue = $validated['place_of_issue'] ?? null;
                $idvisa->issue_date = $validated['issue_date'];
                $idvisa->expiry_date = $validated['expiry_date'] ?? null;
                $idvisa->country_code = strtoupper($validated['country_code'] ?? '');
                $idvisa->visa_type = $validated['visa_type'] ?? null;
                $idvisa->dob = $validated['dob'];
                $idvisa->save();
                break;

            case 'certificate':
                $document->certificates()->delete();
                foreach ($validated['certificateRows'] as $row) {
                    Certificate::create([
                        'document_id' => $document->id,
                        'certificate_type_id' => $row['type_id'],
                        'certificate_issuer_id' => $validated['certificate_issuer_id'] ?? null,
                        'certificate_number' => $validated['certificate_number'] ?? null,
                        'issue_date' => $row['issue'] ?? null,
                        'expiry_date' => $row['expiry'] ?? null,
                        'dob' => $validated['dob'] ?? null,
                    ]);
                }
                break;

            case 'other':
                $other = $document->otherDocument ?? new OtherDocument(['document_id' => $document->id]);
                
                // For new document types, use document_name from DocumentType, otherwise use validated doc_name
                $docName = $isNewDocumentType && $documentType
                    ? $documentType->name
                    : ($validated['doc_name'] ?? 'Document');
                
                // For new document types, document_number is already saved in Document table
                $docNumber = $isNewDocumentType 
                    ? null // Already saved in Document table
                    : ($validated['doc_number'] ?? null);
                
                $other->doc_name = $docName;
                $other->doc_number = $docNumber;
                $other->issue_date = $validated['issue_date'] ?? null;
                $other->expiry_date = $validated['expiry_date'] ?? null;
                $other->dob = $validated['dob'] ?? null;
                $other->save();
                break;
        }

        // Refresh document to get latest version
        $document->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Document updated successfully',
            'version' => $document->version
        ]);
    }
  
  
    public function getIssuersByType($typeId)
    {
        $type = CertificateType::with(['issuers' => function($q) {
            $q->where('is_active', true)->orderBy('name');
        }])->findOrFail($typeId);

        return response()->json($type->issuers);
    }
  
    // API endpoint to fetch issuers for a selected certificate type
    public function getIssuers(Request $request)
    {
        $typeId = $request->type_id;
        $issuers = CertificateType::find($typeId)
            ?->issuers()
            ->where('is_active', true)
            ->orderBy('name')
            ->get() ?? [];

        return response()->json($issuers);
    }


    /**
     * Get Tesseract executable path
     */
    protected function getTesseractPath(): ?string
    {
        // First check if path is configured in config file (for server environments)
        $configuredPath = config('services.tesseract.path');
        if ($configuredPath && file_exists($configuredPath) && is_executable($configuredPath)) {
            Log::info('Using configured Tesseract path: ' . $configuredPath);
            return $configuredPath;
        }
        
        // Check if exec function is available
        if (!function_exists('exec') && !function_exists('shell_exec')) {
            Log::warning('Tesseract detection: exec and shell_exec functions not available');
            return null;
        }

        $output = [];
        $returnVar = 0;
        
        // Method 1: Try direct tesseract --version (most reliable)
        @\exec('tesseract --version 2>&1', $output, $returnVar);
        if ($returnVar === 0) {
            Log::info('Tesseract found via direct command: tesseract');
            return 'tesseract'; // Use command directly
        }
        
        // Method 2: Try which tesseract
        @\exec('which tesseract 2>&1', $output, $returnVar);
        if ($returnVar === 0 && !empty($output)) {
            $path = trim($output[0]);
            if (file_exists($path) && is_executable($path)) {
                Log::info('Tesseract found via which command: ' . $path);
                return $path;
            }
        }
        
        // Method 3: Try common installation paths (including server-specific paths)
        $commonPaths = [
            '/usr/bin/tesseract',
            '/usr/local/bin/tesseract',
            '/opt/homebrew/bin/tesseract',
            '/bin/tesseract',
            '/usr/bin/tesseract-ocr', // Some servers use this
            '/usr/local/bin/tesseract-ocr',
        ];
        
        foreach ($commonPaths as $path) {
            if (file_exists($path) && is_executable($path)) {
                @\exec($path . ' --version 2>&1', $output, $returnVar);
                if ($returnVar === 0) {
                    Log::info('Tesseract found at common path: ' . $path);
                    return $path;
                }
            }
        }
        
        // Method 4: Try shell_exec as fallback
        if (function_exists('shell_exec')) {
            $result = @shell_exec('tesseract --version 2>&1');
            if ($result && (stripos($result, 'tesseract') !== false || stripos($result, '5.3.0') !== false)) {
                Log::info('Tesseract found via shell_exec');
                return 'tesseract';
            }
        }
        
        // Method 5: Try to find via PATH environment variable
        $pathEnv = getenv('PATH');
        if ($pathEnv) {
            $paths = explode(':', $pathEnv);
            foreach ($paths as $basePath) {
                $testPath = rtrim($basePath, '/') . '/tesseract';
                if (file_exists($testPath) && is_executable($testPath)) {
                    Log::info('Tesseract found in PATH: ' . $testPath);
                    return $testPath;
                }
            }
        }
        
        Log::warning('Tesseract not found. Checked common paths and PATH variable.');
        return null;
    }

    /**
     * Check if TesseractOCR command is available
     */
    protected function checkTesseractAvailable(): bool
    {
        return $this->getTesseractPath() !== null;
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
            
            Log::info('TesseractOCR created with absolute path: ' . $fullPath);
            
            return $ocr;
        } catch (\Exception $e) {
            Log::error('TesseractOCR creation failed: ' . $e->getMessage());
            throw $e;
        }
    }

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
            
            // Try Google Cloud Vision API first (more accurate for images)
            if ($extension !== 'pdf' && config('services.google_cloud.api_key')) {
                try {
                    $ocrService = app(\App\Services\Documents\OcrService::class);
                    
                    // Store file in public disk for OcrService
                    $publicPath = $file->store('temp', 'public');
                    $result = $ocrService->extractText($publicPath);
                    
                    // Clean up public temp file
                    if (Storage::disk('public')->exists($publicPath)) {
                        Storage::disk('public')->delete($publicPath);
                    }
                    
                    if ($result['success'] && !empty(trim($result['text']))) {
                        $text = $result['text'];
                        Log::info('Manual scan: Used Google Vision API successfully');
                    }
                } catch (\Exception $e) {
                    Log::warning('Google Vision API failed for manual scan, falling back to TesseractOCR: ' . $e->getMessage());
                }
            }
            
            // Fallback to TesseractOCR if Google Vision failed or not available
            if (empty(trim($text))) {
                // Try to get Tesseract path
                $tesseractPath = $this->getTesseractPath();
                
                if (!$tesseractPath) {
                    // Log detailed error for debugging
                    Log::error('Tesseract OCR not available. Detection failed. Server PATH: ' . getenv('PATH'));
                    return response()->json([
                        'success' => false,
                        'message' => 'OCR is not available. Please install TesseractOCR: sudo apt-get install tesseract-ocr tesseract-ocr-eng'
                    ], 500);
                }
                
                Log::info('Using Tesseract at path: ' . $tesseractPath);

                if ($extension === 'pdf') {
                    // Try Imagick first if available
                    if (extension_loaded('imagick') && class_exists('Imagick')) {
                        // Convert PDF pages to images using Imagick
                        $imagick = new Imagick();
                        $imagick->setResolution(300, 300);
                        $imagick->readImage($fullPath);

                        foreach ($imagick as $i => $page) {
                            $page->setImageFormat('png');
                            $tmpImage = storage_path("app/temp/page_{$i}.png");
                            
                            // Ensure temp directory exists
                            if (!is_dir(storage_path('app/temp'))) {
                                mkdir(storage_path('app/temp'), 0755, true);
                            }
                            
                            $page->writeImage($tmpImage);

                            // OCR per page
                            try {
                                $ocr = $this->createTesseractOCR($tmpImage);
                                $ocr->lang('eng')->psm(3)->oem(1);
                                $text .= $ocr->run() . "\n";
                            } catch (\Error $e) {
                                // Catch fatal errors (like undefined function)
                                Log::error("TesseractOCR fatal error for PDF page {$i}: " . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
                                // Continue with next page instead of failing completely
                            } catch (\Exception $e) {
                                Log::warning("TesseractOCR failed for PDF page {$i}: " . $e->getMessage());
                                // Continue with next page instead of failing completely
                            }

                            // Delete temp page
                            if (file_exists($tmpImage)) {
                                unlink($tmpImage);
                            }
                        }

                        $imagick->clear();
                        $imagick->destroy();
                    } else {
                        // Fallback: Use Ghostscript to convert PDF pages to images, then OCR
                        // Check if Ghostscript (gs) is available
                        $gsPath = trim(shell_exec('which gs 2>/dev/null'));
                        
                        if ($gsPath && is_executable($gsPath)) {
                            // Ensure temp directory exists
                            if (!is_dir(storage_path('app/temp'))) {
                                mkdir(storage_path('app/temp'), 0755, true);
                            }
                            
                            // Use Ghostscript to convert PDF pages to PNG images
                            $outputPattern = storage_path("app/temp/pdf_page_%d.png");
                            $gsCommand = escapeshellarg($gsPath) . ' -dNOPAUSE -dBATCH -sDEVICE=png16m -r300 -dFirstPage=1 -dLastPage=100 -sOutputFile=' . escapeshellarg($outputPattern) . ' ' . escapeshellarg($fullPath) . ' 2>&1';
                            
                            exec($gsCommand, $gsOutput, $gsReturnCode);
                            
                            if ($gsReturnCode === 0) {
                                // Process each generated page image
                                $pageNum = 1;
                                while (true) {
                                    $tmpImage = storage_path("app/temp/pdf_page_{$pageNum}.png");
                                    
                                    if (!file_exists($tmpImage)) {
                                        break; // No more pages
                                    }
                                    
                                    try {
                                        $ocr = $this->createTesseractOCR($tmpImage);
                                        $ocr->lang('eng')->psm(3)->oem(1);
                                        $text .= $ocr->run() . "\n";
                                    } catch (\Error $e) {
                                        Log::error("TesseractOCR fatal error for PDF page {$pageNum}: " . $e->getMessage());
                                    } catch (\Exception $e) {
                                        Log::warning("TesseractOCR failed for PDF page {$pageNum}: " . $e->getMessage());
                                    }
                                    
                                    // Clean up temp file
                                    if (file_exists($tmpImage)) {
                                        unlink($tmpImage);
                                    }
                                    
                                    $pageNum++;
                                    
                                    // Safety limit: max 100 pages
                                    if ($pageNum > 100) {
                                        break;
                                    }
                                }
                            } else {
                                Log::warning("Ghostscript conversion failed: " . implode("\n", $gsOutput));
                                return response()->json([
                                    'success' => false,
                                    'message' => 'PDF processing failed. Ghostscript conversion error.'
                                ], 500);
                            }
                        } else {
                            // Last resort: Try pdftotext for text extraction (no OCR needed for text-based PDFs)
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
                                    
                                    if (empty(trim($text))) {
                                        return response()->json([
                                            'success' => false,
                                            'message' => 'PDF appears to be image-based. OCR processing requires Imagick or Ghostscript. Please contact your administrator to install php-imagick or ensure Ghostscript is available.'
                                        ], 500);
                                    }
                                } else {
                                    return response()->json([
                                        'success' => false,
                                        'message' => 'PDF processing failed. Please ensure pdftotext, Ghostscript, or Imagick is available on the server.'
                                    ], 500);
                                }
                            } else {
                                return response()->json([
                                    'success' => false,
                                    'message' => 'PDF processing requires Imagick, Ghostscript, or pdftotext. None are available. Please contact your administrator.'
                                ], 500);
                            }
                        }
                    }
                } else {
                    // Normal image OCR with TesseractOCR
                    try {
                        $ocr = $this->createTesseractOCR($fullPath);
                        $ocr->lang('eng')->psm(3)->oem(1);
                        $text = $ocr->run();
                    } catch (\Error $e) {
                        // Catch fatal errors (like undefined function)
                        Log::error("TesseractOCR fatal error: " . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
                        return response()->json([
                            'success' => false,
                            'message' => 'OCR processing failed: ' . $e->getMessage() . '. Please ensure TESSERACT_PATH is set in .env file.'
                        ], 500);
                    } catch (\Exception $e) {
                        Log::error("TesseractOCR failed: " . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
                        return response()->json([
                            'success' => false,
                            'message' => 'OCR processing failed: ' . $e->getMessage()
                        ], 500);
                    }
                }
            }

            if (empty(trim($text))) {
                return response()->json([
                    'success' => false,
                    'message' => 'OCR could not read any file. Please try again.'
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

        if ($isPassport) {
            $user = auth()->user();
            $alreadyPassport = $user->documents()->where('type', 'passport')->exists();
            if ($alreadyPassport) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already uploaded a passport. Only one passport allowed.'
                ], 422);
            }
        }

        return response()->json([
            'success' => true,
            'text' => $text,
            'detected_type' => $isPassport ? 'passport' : null,
        ]);
    }

    /**
     * Retry OCR processing for a document
     */
    public function restoreVersion(Request $request, Document $document, $versionId)
    {
        $version = \App\Models\DocumentVersion::where('document_id', $document->id)
            ->where('id', $versionId)
            ->firstOrFail();

        // Check authorization
        if ($document->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        try {
            $versionService = app(\App\Services\Documents\VersionService::class);
            $versionService->restoreVersion($document, $version);
            
            return redirect()->back()->with('success', 'Document restored to version ' . $version->version_number . ' successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to restore version: ' . $e->getMessage());
        }
    }

    public function retryOcr(Request $request, $id)
    {
        $document = Document::where('user_id', auth()->id())->findOrFail($id);
        
        // Reset OCR status
        $document->update([
            'ocr_status' => 'pending',
            'ocr_error' => null,
        ]);
        
        // Dispatch OCR job
        \App\Jobs\ProcessDocumentOcr::dispatch($document);
        
        return response()->json([
            'success' => true,
            'message' => 'OCR processing has been restarted. Please wait a few moments.',
        ]);
    }

    /**
     * Download verification certificate PDF
     */
    public function downloadVerificationCertificate(Document $document)
    {
        // Check authorization
        if ($document->user_id !== Auth::id() && !Auth::user()->hasRole(['super_admin', 'admin'])) {
            abort(403, 'Unauthorized');
        }

        // Get the latest approved verification
        $verification = \App\Models\DocumentVerification::where('document_id', $document->id)
            ->where('status', 'approved')
            ->with(['verificationLevel', 'verifier'])
            ->latest('verified_at')
            ->first();

        if (!$verification) {
            return redirect()->back()->with('error', 'No verification found for this document.');
        }

        // Generate and persist certificate number for 3rd party verification API
        $certificateNumber = $verification->certificate_number
            ?? ('YWC-' . strtoupper(substr(md5($document->id . $verification->id), 0, 12)));
        if (empty($verification->certificate_number)) {
            $verification->update(['certificate_number' => $certificateNumber]);
        }

        // Generate PDF
        $pdf = Pdf::loadView('documents.verification-certificate', [
            'document' => $document,
            'verification' => $verification,
            'certificateNumber' => $certificateNumber
        ]);

        $fileName = 'verification-certificate-' . $document->id . '-' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($fileName);
    }

    /**
     * Handle Add Document form submission
     */
    public function store(Request $request)
    {
        // Step 1: Base validation rules
        // Validate type: either a valid document type slug OR a legacy type
        $base = [
            'type' => [
                'required',
                function ($attribute, $value, $fail) {
                    // Check if it's a legacy type
                    $legacyTypes = ['passport', 'idvisa', 'certificate', 'resume', 'other'];
                    if (in_array($value, $legacyTypes)) {
                        return;
                    }
                    
                    // Check if it's a valid document type slug
                    $exists = DocumentType::where('slug', $value)
                        ->where('is_active', true)
                        ->exists();
                    
                    if (!$exists) {
                        $fail('The selected type is invalid.');
                    }
                },
            ],
            'file' => 'required|file|max:5120', // 5MB
          	'dob'  => 'nullable|date|before_or_equal:today',
        ];

        $rules = $base;
        $typeInput = $request->input('type');
        
        // Step 2: Check if it's a new document type (not a legacy type)
        $isNewDocumentType = !in_array($typeInput, ['passport', 'idvisa', 'certificate', 'resume', 'other']);
        $documentType = null;
        
        if ($isNewDocumentType) {
            // Get document type from database to check its requirements
            $documentType = DocumentType::where('slug', $typeInput)->where('is_active', true)->first();
            
            if ($documentType) {
                // Build validation rules based on document type requirements
                $newTypeRules = [];
                
                if ($documentType->requires_document_number) {
                    $newTypeRules['document_number'] = 'required|string|max:255';
                } else {
                    $newTypeRules['document_number'] = 'nullable|string|max:255';
                }
                
                if ($documentType->requires_expiry_date) {
                    $newTypeRules['expiry_date'] = 'required|date|after_or_equal:issue_date';
                } else {
                    $newTypeRules['expiry_date'] = 'nullable|date|after_or_equal:issue_date';
                }
                
                if ($documentType->requires_issuing_authority) {
                    $newTypeRules['issuing_authority'] = 'required|string|max:255';
                } else {
                    $newTypeRules['issuing_authority'] = 'nullable|string|max:255';
                }
                
                // Common fields
                $newTypeRules['issue_date'] = 'nullable|date|before_or_equal:today';
                $newTypeRules['issuing_country'] = 'nullable|string|max:255';
                $newTypeRules['dob'] = 'nullable|date|before_or_equal:today';
                
                $rules = array_merge($base, $newTypeRules);
            } else {
                // If document type not found, treat as 'other'
                $rules = array_merge($base, [
                    'doc_name'   => 'required|string|max:100',
                    'doc_number' => 'nullable|string|max:100',
                    'issue_date' => 'nullable|date',
                    'expiry_date'=> 'nullable|date|after_or_equal:issue_date',
                  	'dob'        => 'nullable|date|before_or_equal:today',
                ]);
            }
        } else {
            // Step 3: Conditional validation rules for legacy types
            switch ($typeInput) {
            case 'passport':
                $rules = array_merge($base, [
                    'passport_number' => ['required','string','min:6','max:9'],
                    'nationality'     => ['required','string','max:50'],
                    'country_code'    => ['required', 'string', 'min:2', 'max:3'],
                    'issue_date'      => 'required|date|before_or_equal:today',
                    'expiry_date'     => 'required|date|after:issue_date',
                    'dob'             => 'required|date|before_or_equal:today',
                    'type' => [
                        'required',
                        'in:passport,idvisa,certificate,other',
                        function ($attribute, $value, $fail) {
                            $exists = Document::where('user_id', auth()->id())
                                ->where('type', 'passport')
                                ->exists();
                            if ($exists) {
                                $fail('You have already uploaded a passport. Only one passport is allowed.');
                            }
                        }
                    ],
                ]);
                break;

            case 'idvisa':
                $rules = array_merge($base, [
                    'document_name'   => 'required|string|in:Schengen visa,B1/B2 visa,Frontier work permit,C1/D visa,Driving license,Identity card',
                    'document_number' => 'required|string|max:50',
                    'issue_country'   => 'required|string|max:100',
                    'place_of_issue'  => 'nullable|string|max:100',
                    'issue_date'      => 'required|date|before_or_equal:today',
                    'dob'             => 'required|date|before_or_equal:today',
                ]);

                if (in_array($request->document_name, ['Schengen visa','B1/B2 visa','Frontier work permit','C1/D visa'])) {
                    $rules['expiry_date']  = 'required|date|after:issue_date';
                    $rules['country_code'] = 'required|string|min:2|max:3';
                    $rules['visa_type']    = 'nullable|string|max:50';
                } else {
                    $rules['expiry_date']  = 'nullable|date|after:issue_date';
                    $rules['country_code'] = 'required|string|min:2|max:3';
                    $rules['visa_type']    = 'nullable|string|max:50';
                }
                break;

            case 'certificate':
                $rules = array_merge($base, [
                    'certificate_issuer_id'     => 'required|integer|exists:certificate_issuers,id',
                    'certificate_number'        => 'nullable|string|max:255',
                    'certificateRows'           => 'required|array|min:1',
                    'certificateRows.*.type_id' => 'required|integer|exists:certificate_types,id', // make sure table name is correct
                    'certificateRows.*.issue'   => 'nullable|sometimes|date|before_or_equal:today',
                    'certificateRows.*.expiry'  => 'nullable|date|after_or_equal:certificateRows.*.issue',
                  	'dob'                       => 'required|date|before_or_equal:today',
                ]);
                break;

            case 'resume':
                $rules = array_merge($base, [
                    'doc_name'   => 'nullable|string|max:100',
                    'issue_date' => 'nullable|date',
                    'expiry_date'=> 'nullable|date|after_or_equal:issue_date',
                  	'dob'        => 'nullable|date|before_or_equal:today',
                ]);
                break;

            case 'other':
                $rules = array_merge($base, [
                    'doc_name'   => 'required|string|max:100',
                    'doc_number' => 'required|string|max:100',
                    'issue_date' => 'nullable|date',
                    'expiry_date'=> 'nullable|date|after_or_equal:issue_date',
                  	'dob'        => 'required|date|before_or_equal:today',
                ]);
                break;
            }
        }

        // Step 4: Validate request
        $validated = $request->validate($rules);
        
        // Get document type for new document types if not already retrieved
        if ($isNewDocumentType && !$documentType) {
            $documentType = DocumentType::where('slug', $validated['type'])->where('is_active', true)->first();
        }

        // Step 5: File storage (if uploaded)
        $storedPath = null;
        if ($request->hasFile('file')) {
            $storedPath = $request->file('file')->store('documents', 'public');
        }

        // Step 6: Determine legacy type
        $legacyType = in_array($validated['type'], ['passport', 'idvisa', 'certificate', 'resume', 'other']) 
            ? $validated['type'] 
            : 'other'; // Default to 'other' for new document types
        
        // Map 'resume' to 'other' for legacy enum compatibility
        if ($legacyType === 'resume') {
            $legacyType = 'other';
        }

        // Step 7: Prepare document data
        // Helper function to convert empty strings to null
        $toNull = function($value) {
            return ($value === '' || $value === null) ? null : $value;
        };
        
        $documentData = [
            'user_id'         => auth()->id(),
            'type'            => $legacyType, // Keep legacy type for backward compatibility
            'document_type_id'=> $documentType ? $documentType->id : null, // New document type reference
            'document_name'   => $documentType ? $documentType->name : null,
            'file_path'       => $storedPath,
            'file_type'       => $request->file ? $request->file->getClientOriginalExtension() : null,
            'file_size'       => $request->file ? (int) ceil($request->file->getSize() / 1024) : null,
            'issue_date'      => $toNull($validated['issue_date'] ?? null),
            'expiry_date'     => $toNull($validated['expiry_date'] ?? null),
          	'dob'             => $toNull($validated['dob'] ?? null),
            'ocr_status'      => 'pending', // OCR will be processed in background
        ];
        
        // For new document types, ALWAYS save document_number, issuing_authority, and issuing_country
        // These fields are stored directly in the documents table
        if ($isNewDocumentType) {
            // Always save these fields, even if null (to ensure data consistency)
            $documentData['document_number'] = $toNull($validated['document_number'] ?? $request->input('document_number', null));
            $documentData['issuing_authority'] = $toNull($validated['issuing_authority'] ?? $request->input('issuing_authority', null));
            $documentData['issuing_country'] = $toNull($validated['issuing_country'] ?? $request->input('issuing_country', null));
        }

        // Step 8: Save main Document
        $document = Document::create($documentData);

        // Queue OCR processing if file was uploaded
        if ($storedPath) {
            ProcessDocumentOcr::dispatch($document);
        }

        // Generate thumbnail if file was uploaded
        if ($storedPath && $document->id) {
            try {
                $thumbnailService = app(\App\Services\Documents\ThumbnailService::class);
                $thumbnailService->ensureThumbnail($document);
            } catch (\Exception $e) {
                \Log::warning('Thumbnail generation failed for document ' . $document->id . ': ' . $e->getMessage());
                // Don't fail the upload if thumbnail generation fails
            }
        }

        // Step 9: Save child details depending on type
        // For new document types (not legacy), treat as 'other'
        $typeForSwitch = in_array($validated['type'], ['passport', 'idvisa', 'certificate', 'resume', 'other']) 
            ? $validated['type'] 
            : 'other';
        
        switch ($typeForSwitch) {
            case 'resume':
                // Resume is treated as 'other' in database
                OtherDocument::create([
                    'document_id' => $document->id,
                    'doc_name'    => $validated['doc_name'] ?? 'Resume',
                    'doc_number'  => null,
                    'issue_date'  => $validated['issue_date'] ?? null,
                    'expiry_date' => $validated['expiry_date'] ?? null,
                  	'dob'         => $validated['dob'] ?? null,
                ]);
                break;
            case 'passport':
                PassportDetail::create([
                    'document_id'     => $document->id,
                    'passport_number' => $validated['passport_number'],
                    'issue_date'      => $validated['issue_date'],
                    'expiry_date'     => $validated['expiry_date'],
                    'nationality'     => $validated['nationality'],
                    'country_code'    => strtoupper($validated['country_code']),
                  	'dob'              => $validated['dob'],
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
                    'country_code'    => isset($validated['country_code']) ? strtoupper($validated['country_code']) : null,
                    'visa_type'       => $validated['visa_type'] ?? null,
                  	'dob'              => $validated['dob'],
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
                        'issue_date'           => $row['issue'] ?? null,
                        'expiry_date'          => $row['expiry'] ?? null,
                      	'dob'                  => $validated['dob'] ?? null,
                    ]);
                }
                break;

            case 'other':
                // For new document types, use document_name from DocumentType, otherwise use validated doc_name
                $docName = $documentType 
                    ? $documentType->name 
                    : ($validated['doc_name'] ?? 'Document');
                
                OtherDocument::create([
                    'document_id' => $document->id,
                    'doc_name'    => $docName,
                    'doc_number'  => $validated['doc_number'] ?? null,
                    'issue_date'  => $validated['issue_date'] ?? null,
                    'expiry_date' => $validated['expiry_date'] ?? null,
                  	'dob'         => $validated['dob'] ?? null,
                ]);
                break;
        }

        // Return JSON response for AJAX requests, otherwise redirect
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Document saved successfully!',
                'document_id' => $document->id
            ]);
        }
        
        return redirect()->back()->with('success', 'Document saved successfully!');
    }
  
  	public function toggleShare(Request $request)
    {
        $doc = Document::find($request->id);

        if(!$doc) {
            return response()->json(['success' => false, 'message' => 'Document not found']);
        }

        $doc->is_active = $request->is_active;
        $doc->save();

        return response()->json(['success' => true]);
    }


	public function show($id)
    {
        try {
            $user = User::with([
                'documents.documentType', // New Phase 1 relationship
                'documents.passportDetail',
                'documents.idvisaDetail',
                'documents.certificates.type',
                'documents.certificates.issuer',
                'documents.otherDocument',
            ])->findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404, 'User not found');
        }

        // transform documents with expiry details
        $user->documents->transform(function ($doc) {
            if (!$doc->expiry_date) {
                $doc->remaining_number = null;
                $doc->remaining_type = 'N/A';
                $doc->is_expiring_soon = false;
                return $doc;
            }

            $today = \Carbon\Carbon::today();
            $expiry = \Carbon\Carbon::parse($doc->expiry_date)->startOfDay();
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

            // format dates for frontend
            $doc->issue_date_formatted = $doc->issue_date ? \Carbon\Carbon::parse($doc->issue_date)->format('d M Y') : null;
            $doc->expiry_date_formatted = $doc->expiry_date ? \Carbon\Carbon::parse($doc->expiry_date)->format('d M Y') : null;

            // default status
            $doc->status = $doc->status ?? 'pending';

            return $doc;
        });

        // Group documents by status
        $documentsByStatus = [
            'pending' => $user->documents->where('status', 'pending'),
            'approved' => $user->documents->where('status', 'approved'),
            'rejected' => $user->documents->where('status', 'rejected'),
        ];

        return view('career-history.show', compact('user', 'documentsByStatus'));
    }

    public function toggleDoc(Document $doc)
    {
        $doc->is_active = !$doc->is_active;
        $doc->save();

        return response()->json([
            'success' => true,
            'is_active' => $doc->is_active,
        ]);
    }

	public function updateStatus(Request $request, Document $document)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'notes' => 'nullable|string|max:500',
        ]);

        $oldStatus = $document->status;
        
        $document->status = $request->status;
        $document->updated_by = auth()->id();
        $document->save();

        // Track status change
        \App\Models\DocumentStatusChange::create([
            'document_id' => $document->id,
            'old_status' => $oldStatus,
            'new_status' => $request->status,
            'notes' => $request->notes,
            'changed_by' => auth()->id(),
        ]);

        // Send notification email
        try {
            \Illuminate\Support\Facades\Mail::to($document->user->email)
                ->send(new \App\Mail\DocumentStatusChangedMail($document, $request->status, $request->notes));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send document status change email: ' . $e->getMessage());
        }

        return response()->json(['success' => true]);
    }
  	

}
