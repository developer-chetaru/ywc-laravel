<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
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
use App\Jobs\ProcessDocumentOcr;

class CareerHistory extends Component
{
    use WithFileUploads;

    public bool $showModal = false;

    // Certificate Types
    public $certificateTypes = [];
    public $certificateIssuers = [];
    public $documentTypes = [];

    // Document shared fields
    public ?string $type = null;
    public ?TemporaryUploadedFile $file = null;
    public ?string $issue_date = null;
    public ?string $expiry_date = null;

    // Passport
    public ?string $passport_number = null;
    public ?string $nationality = null;
    public ?string $country_code = null;

    // ID/Visa
    public ?string $document_name   = null;
    public ?string $document_number = null;
    public ?string $issue_country   = null;
    public ?string $visa_type       = null;
    public ?string $place_of_issue  = null;

    // Certificate
    public ?int $certificate_type_id = null;
    public ?int $certificate_issuer_id = null;
    public ?string $certificate_number = null;
    public array $certificateRows = [['type_id'=>null,'issue'=>null,'expiry'=>null]];

    // Other document
    public ?string $doc_name = null;
    public ?string $doc_number = null;

    public function mount()
    {
        $this->certificateTypes = CertificateType::where('is_active', true)->orderBy('name')->get();
        $this->certificateIssuers = CertificateIssuer::where('is_active', true)->orderBy('name')->get();
        $this->documentTypes = DocumentType::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->groupBy('category');
    }

    public function showModal(): void
    {
        $this->showModal = true;
    }

    public function openModal(): void
    {
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    public function addCertificateRow(): void
    {
        $this->certificateRows[] = ['type_id'=>null,'issue'=>null,'expiry'=>null];
    }

    public function removeCertificateRow(int $index): void
    {
        if ($index === 0) return;
        unset($this->certificateRows[$index]);
        $this->certificateRows = array_values($this->certificateRows);
    }

    // Public rules method for validation on click
    public function getRules(): array
    {
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
            'file' => 'nullable|file|max:5120', // 5MB
        ];

        switch ($this->type) {
            case 'passport':
                return array_merge($base, [
                    'passport_number' => ['required','string','min:6','max:9'],
                    'nationality'     => ['required','string','max:50'],
                    'country_code'    => ['required','string','size:3'],
                    'issue_date'      => 'required|date|before_or_equal:today',
                    'expiry_date'     => 'required|date|after:issue_date',
                ]);

            case 'idvisa':
                $rules = array_merge($base, [
                    'document_name'   => 'required|string|in:Schengen visa,B1/B2 visa,Frontier work permit,C1/D visa,Driving license,Identity card',
                    'document_number' => 'required|string|max:50',
                    'issue_country'   => 'required|string|max:100',
                    'place_of_issue'  => 'nullable|string|max:100',
                    'issue_date'      => 'required|date|before_or_equal:today',
                ]);

                // Expiry date required for visas/permits, optional (nullable) for ID/License
                if (in_array($this->document_name, ['Schengen visa','B1/B2 visa','Frontier work permit','C1/D visa'])) {
                    $rules['expiry_date']  = 'required|date|after:issue_date';
                    $rules['country_code'] = 'required|string|size:3'; // ISO Alpha-3
                    $rules['visa_type']    = 'nullable|string|max:50';
                } else {
                    $rules['expiry_date']  = 'nullable|date|after:issue_date';
                    $rules['country_code'] = 'nullable|string|size:3';
                    $rules['visa_type']    = 'nullable|string|max:50';
                }

                return $rules;

            case 'certificate':
                return array_merge($base, [
                    'certificate_issuer_id'       => 'required|integer|exists:certificate_issuer_data,id',
                    'certificateRows'             => 'required|array|min:1',
                    'certificateRows.*.type_id'   => 'required|integer|exists:certificate_type_data,id',
                    'certificateRows.*.issue'     => 'nullable|date|before_or_equal:today',
                    'certificateRows.*.expiry'    => 'nullable|date|after_or_equal:certificateRows.*.issue',
                ]);

            case 'resume':
                return array_merge($base, [
                    'doc_name'   => 'nullable|string|max:100',
                    'issue_date' => 'nullable|date',
                    'expiry_date'=> 'nullable|date|after_or_equal:issue_date',
                ]);

           case 'other':
                return array_merge($base, [
                    'doc_name'   => 'required|string|max:100',
                    'doc_number' => 'nullable|string|max:100',  // Optional but max length 100
                    'issue_date' => 'nullable|date',
                    'expiry_date'=> 'nullable|date|after_or_equal:issue_date',
                    // 'file'       => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120', // 5MB max
                ]);

            default:
                return $base;
        }
    }

    // Save method triggers validation on click
    public function save(): void
    {
        $this->validate($this->getRules());

        try {

            $storedPath = null;

                if ($this->file instanceof TemporaryUploadedFile) {
                    try {
                        $this->validate([
                            'file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120', // 5MB
                        ]);

                        $storedPath = $this->file->store('documents', 'public');

                    } catch (\Exception $e) {
                        Log::error('Document upload failed: '.$e->getMessage());
                        $this->addError('file', 'Failed to store uploaded file. '.$e->getMessage());
                        return;
                    }
                }

                // Check if type is a new document type slug or legacy type
                $documentType = DocumentType::where('slug', $this->type)->where('is_active', true)->first();
                $legacyType = in_array($this->type, ['passport', 'idvisa', 'certificate', 'resume', 'other']) 
                    ? $this->type 
                    : 'other'; // Default to 'other' for new document types
                
                $document = Document::create([
                    'user_id'         => auth()->id(),
                    'type'            => $legacyType, // Keep legacy type for backward compatibility
                    'document_type_id'=> $documentType ? $documentType->id : null, // New document type reference
                    'document_name'   => $documentType ? $documentType->name : null,
                    'file_path'       => $storedPath,
                    'file_type'       => $this->file ? $this->file->getClientOriginalExtension() : null,
                    'file_size'       => $this->file ? (int) ceil($this->file->getSize() / 1024) : null,
                    'issue_date'      => $this->issue_date,
                    'expiry_date'     => $this->expiry_date,
                    'ocr_status'      => 'pending', // OCR will be processed in background
                ]);

                // Queue OCR processing if file was uploaded
                if ($storedPath) {
                    ProcessDocumentOcr::dispatch($document);
                }

                // Child tables per type
                if ($this->type === 'passport') {
                    PassportDetail::create([
                        'document_id'     => $document->id,
                        'passport_number' => $this->passport_number,
                        'issue_date' => $this->issue_date,
                        'expiry_date' => $this->expiry_date,
                        'nationality'  => $this->nationality,
                        'country_code' => strtoupper($this->country_code),
                    ]);
                } elseif ($this->type === 'idvisa') {
                // Build base insert data
                    $data = [
                        'document_id'     => $document->id,
                        'document_name'   => $this->document_name,
                        'document_number' => $this->document_number,
                        'issue_country'   => $this->issue_country,
                        'place_of_issue'  => $this->place_of_issue,
                        'issue_date'      => $this->issue_date,
                    ];

                    if (in_array($this->document_name, ['Schengen visa', 'B1/B2 visa', 'Frontier work permit', 'C1/D visa'])) {
                        $data['expiry_date']  = $this->expiry_date;
                        $data['country_code'] = strtoupper($this->country_code);
                        $data['visa_type']    = $this->visa_type;
                    }

                    if (in_array($this->document_name, ['Driving license', 'Identity card'])) {
                        $data['expiry_date']  = $this->expiry_date; // can be null
                        $data['country_code'] = strtoupper($this->country_code); // can be null
                        $data['visa_type']    = $this->visa_type; // can be null
                    }

                    IdvisaDetail::create($data);
                } elseif ($this->type === 'certificate') {
                    foreach ($this->certificateRows as $row) {
                        if (empty($row['type_id'])) {
                            continue;
                        }

                        Certificate::create([
                            'document_id'           => $document->id,
                            'certificate_type_id'   => $row['type_id'],
                            'certificate_issuer_id' => $this->certificate_issuer_id,
                            'certificate_number'    => $this->certificate_number,
                            'issue_date'            => $row['issue'] ?? null,
                            'expiry_date'           => $row['expiry'] ?? null,
                        ]);
                    }
                } elseif ($this->type === 'resume') {
                    OtherDocument::create([
                        'document_id'    => $document->id,
                        'doc_name'       => $this->doc_name ?? 'Resume',
                        'doc_number'     => null,
                        'issue_date'     => $this->issue_date ?? null,
                        'expiry_date'    => $this->expiry_date ?? null,
                    ]);
                } elseif ($this->type === 'other') {
                    // $storedPath = null;
                    // if ($this->file instanceof \Livewire\TemporaryUploadedFile) {
                    //     $storedPath = $this->file->store('documents', 'public');
                    // }

                    OtherDocument::create([
                        'document_id'    => $document->id,
                        'doc_name'       => $this->doc_name,
                        'doc_number'     => $this->doc_number,
                        'issue_date'     => $this->issue_date ?? null,
                        'expiry_date'    => $this->expiry_date ?? null,
                        // 'file_path'      => $storedPath,
                    ]);
                }

            $this->resetAll();
            $this->closeModal();
            $this->showModal = false;
            $this->dispatchBrowserEvent('notify', ['message' => 'Document saved successfully!']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Livewire automatically shows validation errors
            throw $e;
        } catch (\Throwable $e) {
            $this->addError('general', 'Something went wrong: '.$e->getMessage());
        }
    }

    public function resetAll(): void
    {
        $this->reset([
            'showModal','type','file','issue_date','expiry_date',
            'passport_number','nationality','country_code',
            'document_name','document_number','issue_country','visa_type','place_of_issue',
            'certificate_type_id','certificate_issuer_id','certificate_number','certificateRows',
            'doc_name','doc_number',
        ]);

        $this->certificateRows = [['type_id'=>null,'issue'=>null,'expiry'=>null]];
        $this->showModal = false;
    }

    public function render()
    {
        return view('livewire.career-history')->layout('layouts.app'); 
    }
}
