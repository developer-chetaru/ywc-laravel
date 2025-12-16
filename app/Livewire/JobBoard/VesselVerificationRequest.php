<?php

namespace App\Livewire\JobBoard;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use App\Models\VesselVerification as VesselVerificationModel;
use App\Models\Yacht;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

#[Layout('layouts.app')]
class VesselVerificationRequest extends Component
{
    use WithFileUploads;

    public $verificationMethod = 'captain';
    public $vesselName = '';
    public $imoNumber = '';
    public $mmsiNumber = '';
    public $flagState = '';
    public $roleOnVessel = '';
    public $authorityDescription = '';
    
    public $captainLicense;
    public $vesselRegistration;
    public $authorizationLetter;
    public $managementCompanyDocs;
    
    public $yachtId = null;
    public $verification = null;
    public $yachts = [];

    public function mount()
    {
        $this->verification = Auth::user()->vesselVerification;
        
        if ($this->verification) {
            $this->loadVerification();
        }

        $this->yachts = Yacht::where('created_by_user_id', Auth::id())->get();
    }

    public function loadVerification()
    {
        $v = $this->verification;
        $this->verificationMethod = $v->verification_method;
        $this->vesselName = $v->vessel_name;
        $this->imoNumber = $v->imo_number ?? '';
        $this->mmsiNumber = $v->mmsi_number ?? '';
        $this->flagState = $v->flag_state ?? '';
        $this->roleOnVessel = $v->role_on_vessel;
        $this->authorityDescription = $v->authority_description ?? '';
        $this->yachtId = $v->yacht_id;
    }

    public function submit()
    {
        $validated = $this->validate([
            'verificationMethod' => 'required|in:captain,management_company,hod_authorized',
            'vesselName' => 'required|string|max:255',
            'roleOnVessel' => 'required|string|max:255',
            'authorityDescription' => 'required|string|max:1000',
            'captainLicense' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'vesselRegistration' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'authorizationLetter' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $data = [
            'user_id' => Auth::id(),
            'yacht_id' => $this->yachtId,
            'verification_method' => $this->verificationMethod,
            'vessel_name' => $this->vesselName,
            'imo_number' => $this->imoNumber,
            'mmsi_number' => $this->mmsiNumber,
            'flag_state' => $this->flagState,
            'role_on_vessel' => $this->roleOnVessel,
            'authority_description' => $this->authorityDescription,
            'status' => 'pending',
        ];

        // Upload documents
        if ($this->captainLicense) {
            $data['captain_license_path'] = $this->captainLicense->store('verifications', 'public');
        }
        if ($this->vesselRegistration) {
            $data['vessel_registration_path'] = $this->vesselRegistration->store('verifications', 'public');
        }
        if ($this->authorizationLetter) {
            $data['authorization_letter_path'] = $this->authorizationLetter->store('verifications', 'public');
        }

        if ($this->verification) {
            $this->verification->update($data);
        } else {
            VesselVerificationModel::create($data);
        }

        session()->flash('success', 'Verification request submitted! We will review it within 24-48 hours.');
        $this->mount(); // Reload
    }

    public function render()
    {
        return view('livewire.job-board.vessel-verification-request', [
            'yachts' => $this->yachts,
        ]);
    }
}

