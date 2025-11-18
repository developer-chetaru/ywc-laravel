<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Yacht;

class Profile extends Component
{
    use WithFileUploads;

    public $photo;
    public $profile_photo_path;
    public $first_name, $last_name, $email;
    public $user;
    
    // Crew Profile Fields
    public $years_experience;
    public $current_yacht;
    public $current_yacht_start_date;
    public $languages = [];
    public $certifications = [];
    public $specializations = [];
    public $interests = [];
    public $availability_status;
    public $availability_message;
    public $looking_to_meet = false;
    public $looking_for_work = false;
    public $sea_service_time_months;
    public $previous_yachts = [];
    
    // Language/Certification input helpers
    public $newLanguage = '';
    public $newCertification = '';
    public $newSpecialization = '';
    public $newInterest = '';
    
    // Previous Yacht input helpers
    public $newPreviousYachtId = '';
    public $newPreviousYachtName = '';
    public $newPreviousYachtStartDate = '';
    public $newPreviousYachtEndDate = '';
    public $showOtherInput = false;
    
    // Yachts list for dropdown
    public $yachts = [];

    public function mount()
    {
        $user = Auth::user();
        $this->first_name = $user->first_name;
        $this->last_name = $user->last_name;
        $this->email = $user->email;
        $this->profile_photo_path = $user->profile_photo_path;
        
        // Load crew profile fields
        $this->years_experience = $user->years_experience;
        $this->current_yacht = $user->current_yacht;
        $this->current_yacht_start_date = $user->current_yacht_start_date;
        $this->languages = $user->languages ?? [];
        $this->certifications = $user->certifications ?? [];
        $this->specializations = $user->specializations ?? [];
        $this->interests = $user->interests ?? [];
        $this->availability_status = $user->availability_status;
        $this->availability_message = $user->availability_message;
        $this->looking_to_meet = $user->looking_to_meet ?? false;
        $this->looking_for_work = $user->looking_for_work ?? false;
        $this->sea_service_time_months = $user->sea_service_time_months;
        
        // Load previous yachts - handle both old format (strings) and new format (objects)
        $previousYachts = $user->previous_yachts ?? [];
        $this->previous_yachts = [];
        foreach ($previousYachts as $yacht) {
            if (is_string($yacht)) {
                // Old format - convert to new format
                $this->previous_yachts[] = [
                    'yacht_id' => null,
                    'name' => $yacht,
                    'start_date' => null,
                    'end_date' => null,
                ];
            } else {
                // New format - fix invalid dates (swap if end < start)
                $startDate = !empty($yacht['start_date']) ? $yacht['start_date'] : null;
                $endDate = !empty($yacht['end_date']) ? $yacht['end_date'] : null;
                
                // Fix invalid dates by swapping if end date is before start date
                if ($startDate && $endDate) {
                    $start = \Carbon\Carbon::parse($startDate);
                    $end = \Carbon\Carbon::parse($endDate);
                    
                    if ($end->lt($start)) {
                        // Swap dates if end is before start
                        $temp = $startDate;
                        $startDate = $endDate;
                        $endDate = $temp;
                    }
                }
                
                $this->previous_yachts[] = [
                    'yacht_id' => $yacht['yacht_id'] ?? null,
                    'name' => $yacht['name'] ?? '',
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ];
            }
        }
        
        // Load yachts for dropdown
        $this->loadYachts();
    }
    
    public function loadYachts()
    {
        $this->yachts = Yacht::orderBy('name')->get(['id', 'name']);
    }

    public function updateProfile()
    {
        $this->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $user->update([
            'first_name' => $this->first_name,
            'last_name'  => $this->last_name,
        ]);

        session()->flash('profile-message', 'Profile updated successfully.');
    }

    public function updateProfilePhoto()
    {
        $this->validate([
            'photo' => 'image|max:2048', // 2MB max
        ]);

        $user = Auth::user();

        // Delete old photo if exists
        if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        // Store new photo
        $path = $this->photo->store('profile-photos', 'public');

        $user->update([
            'profile_photo_path' => $path,
        ]);

        // 🔑 Update Livewire state
        $this->profile_photo_path = $path;
        $this->photo = null;

        session()->flash('message', 'Profile photo updated successfully.');
    }

    public function removeProfilePhoto()
    {
        $user = Auth::user();

        if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        $user->update([
            'profile_photo_path' => null,
        ]);

        // 🔑 Update Livewire state
        $this->profile_photo_path = null;

        session()->flash('profile-message', 'Profile photo removed.');
    }
    
    public function updateCrewProfile()
    {
        // Fix and validate dates in previous_yachts
        foreach ($this->previous_yachts as $index => $yacht) {
            if (is_array($yacht) && isset($yacht['start_date']) && isset($yacht['end_date'])) {
                if (!empty($yacht['start_date']) && !empty($yacht['end_date'])) {
                    $startDate = \Carbon\Carbon::parse($yacht['start_date']);
                    $endDate = \Carbon\Carbon::parse($yacht['end_date']);
                    
                    if ($endDate->lt($startDate)) {
                        // Auto-fix by swapping dates
                        $temp = $this->previous_yachts[$index]['start_date'];
                        $this->previous_yachts[$index]['start_date'] = $this->previous_yachts[$index]['end_date'];
                        $this->previous_yachts[$index]['end_date'] = $temp;
                    }
                }
            }
        }
        
        $this->validate([
            'years_experience' => 'nullable|integer|min:0|max:100',
            'current_yacht' => 'nullable|string|max:255',
            'current_yacht_start_date' => 'nullable|date',
            'languages' => 'nullable|array',
            'certifications' => 'nullable|array',
            'specializations' => 'nullable|array',
            'interests' => 'nullable|array',
            'availability_status' => 'nullable|in:available,busy,looking_for_work,on_leave',
            'availability_message' => 'nullable|string|max:500',
            'looking_to_meet' => 'nullable|boolean',
            'looking_for_work' => 'nullable|boolean',
            'sea_service_time_months' => 'nullable|integer|min:0',
            'previous_yachts' => 'nullable|array',
            'previous_yachts.*.name' => 'required|string|max:255',
            'previous_yachts.*.start_date' => 'nullable|date',
            'previous_yachts.*.end_date' => 'nullable|date',
        ]);

        $user = Auth::user();
        $user->update([
            'years_experience' => $this->years_experience,
            'current_yacht' => $this->current_yacht,
            'current_yacht_start_date' => $this->current_yacht_start_date,
            'languages' => $this->languages,
            'certifications' => $this->certifications,
            'specializations' => $this->specializations,
            'interests' => $this->interests,
            'availability_status' => $this->availability_status,
            'availability_message' => $this->availability_message,
            'looking_to_meet' => $this->looking_to_meet,
            'looking_for_work' => $this->looking_for_work,
            'sea_service_time_months' => $this->sea_service_time_months,
            'previous_yachts' => $this->previous_yachts,
        ]);

        session()->flash('profile-message', 'Crew profile updated successfully.');
    }
    
    public function addLanguage()
    {
        if ($this->newLanguage && !in_array($this->newLanguage, $this->languages)) {
            $this->languages[] = $this->newLanguage;
            $this->newLanguage = '';
        }
    }
    
    public function removeLanguage($index)
    {
        unset($this->languages[$index]);
        $this->languages = array_values($this->languages);
    }
    
    public function addCertification()
    {
        if ($this->newCertification && !in_array($this->newCertification, $this->certifications)) {
            $this->certifications[] = $this->newCertification;
            $this->newCertification = '';
        }
    }
    
    public function removeCertification($index)
    {
        unset($this->certifications[$index]);
        $this->certifications = array_values($this->certifications);
    }
    
    public function addSpecialization()
    {
        if ($this->newSpecialization && !in_array($this->newSpecialization, $this->specializations)) {
            $this->specializations[] = $this->newSpecialization;
            $this->newSpecialization = '';
        }
    }
    
    public function removeSpecialization($index)
    {
        unset($this->specializations[$index]);
        $this->specializations = array_values($this->specializations);
    }
    
    public function addInterest()
    {
        if ($this->newInterest && !in_array($this->newInterest, $this->interests)) {
            $this->interests[] = $this->newInterest;
            $this->newInterest = '';
        }
    }
    
    public function removeInterest($index)
    {
        unset($this->interests[$index]);
        $this->interests = array_values($this->interests);
    }
    
    public function updatedNewPreviousYachtId()
    {
        if ($this->newPreviousYachtId === 'other') {
            $this->showOtherInput = true;
            $this->newPreviousYachtName = '';
        } else {
            $this->showOtherInput = false;
            $this->newPreviousYachtName = '';
            if ($this->newPreviousYachtId && is_numeric($this->newPreviousYachtId)) {
                $yacht = Yacht::find($this->newPreviousYachtId);
                if ($yacht) {
                    // Don't set name here, let it be selected from dropdown
                }
            }
        }
    }
    
    public function addPreviousYacht()
    {
        $yachtName = '';
        $yachtId = null;
        
        if ($this->newPreviousYachtId === 'other') {
            // Manual entry
            if (empty($this->newPreviousYachtName)) {
                session()->flash('yacht-error', 'Please enter a yacht name.');
                return;
            }
            $yachtName = $this->newPreviousYachtName;
            $yachtId = null;
        } else {
            // Selected from dropdown
            if (empty($this->newPreviousYachtId)) {
                session()->flash('yacht-error', 'Please select a yacht.');
                return;
            }
            $yacht = Yacht::find($this->newPreviousYachtId);
            if (!$yacht) {
                session()->flash('yacht-error', 'Selected yacht not found.');
                return;
            }
            $yachtName = $yacht->name;
            $yachtId = $yacht->id;
        }
        
        // Validate dates
        if ($this->newPreviousYachtStartDate && $this->newPreviousYachtEndDate) {
            $startDate = \Carbon\Carbon::parse($this->newPreviousYachtStartDate);
            $endDate = \Carbon\Carbon::parse($this->newPreviousYachtEndDate);
            
            if ($endDate->lt($startDate)) {
                session()->flash('yacht-error', 'End date must be after start date.');
                return;
            }
        }
        
        // Check if already exists
        foreach ($this->previous_yachts as $existing) {
            if (isset($existing['name']) && $existing['name'] === $yachtName) {
                session()->flash('yacht-error', 'This yacht is already in your list.');
                return; // Already exists
            }
        }
        
        $this->previous_yachts[] = [
            'yacht_id' => $yachtId,
            'name' => $yachtName,
            'start_date' => $this->newPreviousYachtStartDate ?: null,
            'end_date' => $this->newPreviousYachtEndDate ?: null,
        ];
        
        // Reset form
        $this->newPreviousYachtId = '';
        $this->newPreviousYachtName = '';
        $this->newPreviousYachtStartDate = '';
        $this->newPreviousYachtEndDate = '';
        $this->showOtherInput = false;
        session()->forget('yacht-error');
    }
    
    public function removePreviousYacht($index)
    {
        unset($this->previous_yachts[$index]);
        $this->previous_yachts = array_values($this->previous_yachts);
    }

    public function render()
    {
        return view('livewire.profile')->layout('layouts.app'); 
    }
}
