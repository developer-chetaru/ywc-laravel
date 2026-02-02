<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Yacht;
use App\Models\CareerHistoryEntry;
use App\Models\YachtReview;
use App\Models\MarinaReview;
use App\Models\ItineraryRoute;
use App\Models\ReviewVote;
use App\Services\Forum\ForumReputationService;
use App\Services\Forum\BadgeService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class Profile extends Component
{
    use WithFileUploads;

    public $profile_photo_path;
    public $photo; // For file upload
    public $first_name, $last_name, $email;
    public $user;
    
    // Basic Profile Fields
    public $professional_summary;
    public $current_position;
    public $employment_type;
    public $expected_salary;
    public $vessel_preference;
    public $special_services;
    public $available_from;
    public $nationality;
    public $passport_validity;
    public $date_of_birth;
    public $visas;
    
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
    
    // Edit mode flags
    public $editingProfile = false; // Main profile edit mode
    public $editingName = false;
    public $editingSummary = false;
    public $editingCareerProfile = false;
    public $editingPersonalDetails = false;
    public $editingSkills = false;
    public $editingLanguages = false;
    
    // Language/Certification input helpers
    public $newLanguage = '';
    public $newCertification = '';
    public $newSpecialization = '';
    public $newInterest = '';
    
    // Certification details form
    public $showCertificationModal = false;
    public $editingCertificationIndex = null;
    public $certificationName = '';
    public $certificationIssuedBy = '';
    public $certificationExpiryDate = '';
    public $certificationStatus = 'pending';
    
    // Previous Yacht input helpers
    public $newPreviousYachtId = '';
    public $newPreviousYachtName = '';
    public $newPreviousYachtStartDate = '';
    public $newPreviousYachtEndDate = '';
    public $showOtherInput = false;
    
    // Yachts list for dropdown
    public $yachts = [];
    
    // Career History Entries
    public $careerHistoryEntries = [];
    
    // Reviews and Itineraries
    public $yachtReviews = [];
    public $marinaReviews = [];
    public $itineraryRoutes = [];
    public $activeTab = 'reviews'; // 'reviews' or 'itineraries'
    public $showAllReviews = false;
    public $showAllItineraries = false;
    public $showActivityCard = true; // Show/hide Activity card section
    
    // Forum stats
    public $forumStats = [];
    
    // Card visibility states
    public $showProfessionalSummary = true;
    public $showCareerProfile = true;
    public $showCareerHistory = true;
    public $showCertifications = true;
    public $showSkills = true;
    public $showPersonalDetails = true;
    public $showLanguages = true;
    public $showSidebarCard = false; // Show/hide sidebar navigation card (hidden by default)

    public function mount()
    {
        $user = Auth::user()->load('roles');
        $this->user = $user;
        $this->first_name = $user->first_name;
        $this->last_name = $user->last_name;
        $this->email = $user->email;
        $this->profile_photo_path = $user->profile_photo_path;
        
        // Load new profile fields
        $this->professional_summary = $user->professional_summary;
        // Current Position should come from user's role, not from database field
        $this->current_position = $user->roles->first()->name ?? 'N/A';
        $this->employment_type = $user->employment_type;
        $this->expected_salary = $user->expected_salary;
        $this->vessel_preference = $user->vessel_preference;
        $this->special_services = $user->special_services;
        $this->available_from = $user->available_from;
        $this->nationality = $user->nationality;
        $this->passport_validity = $user->passport_validity;
        $this->date_of_birth = $user->dob;
        $this->visas = $user->visas;
        
        // Load crew profile fields
        $this->years_experience = $user->years_experience;
        $this->current_yacht = $user->current_yacht;
        $this->current_yacht_start_date = $user->current_yacht_start_date;
        // Load languages - handle both old format (strings) and new format (arrays)
        $languages = $user->languages ?? [];
        $this->languages = [];
        foreach ($languages as $lang) {
            if (is_string($lang)) {
                // Old format - convert to new format
                $this->languages[] = [
                    'name' => $lang,
                    'proficiency' => 'Proficient',
                    'read' => true,
                    'write' => true,
                    'speak' => true,
                ];
            } else {
                // New format - ensure all fields exist
                $this->languages[] = [
                    'name' => $lang['name'] ?? $lang,
                    'proficiency' => $lang['proficiency'] ?? 'Proficient',
                    'read' => $lang['read'] ?? true,
                    'write' => $lang['write'] ?? true,
                    'speak' => $lang['speak'] ?? true,
                ];
            }
        }
        // Load certifications - handle both old format (strings) and new format (arrays)
        $certifications = $user->certifications ?? [];
        $this->certifications = [];
        foreach ($certifications as $cert) {
            if (is_string($cert)) {
                // Old format - convert to new format
                $this->certifications[] = [
                    'name' => $cert,
                    'issued_by' => '',
                    'expiry_date' => null,
                    'status' => 'pending',
                ];
            } else {
                // New format - ensure all fields exist
                $this->certifications[] = [
                    'name' => $cert['name'] ?? $cert,
                    'issued_by' => $cert['issued_by'] ?? '',
                    'expiry_date' => $cert['expiry_date'] ?? null,
                    'status' => $cert['status'] ?? 'pending',
                ];
            }
        }
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
        
        // Load career history entries from career_history_entries table
        $this->loadCareerHistory();
        
        // Load reviews and itineraries
        $this->loadReviewsAndItineraries();
        
        // Load forum stats
        $this->loadForumStats();
    }
    
    public function loadForumStats()
    {
        $user = Auth::user();
        $reputationService = app(ForumReputationService::class);
        $badgeService = app(BadgeService::class);
        
        // Get reputation summary
        $reputationSummary = $reputationService->getUserReputationSummary($user);
        
        // Get user badges
        $badges = DB::table('forum_user_badges')
            ->join('forum_badges', 'forum_user_badges.badge_id', '=', 'forum_badges.id')
            ->where('forum_user_badges.user_id', $user->id)
            ->where('forum_badges.is_active', true)
            ->select('forum_badges.*', 'forum_user_badges.earned_at')
            ->orderBy('forum_user_badges.earned_at', 'desc')
            ->get();
        
        // Get thread and post counts
        $threadCount = DB::table('forum_threads')
            ->where('author_id', $user->id)
            ->count();
        
        $postCount = DB::table('forum_posts')
            ->where('author_id', $user->id)
            ->count();
        
        // Get warning count
        $warningCount = DB::table('forum_warnings')
            ->where('user_id', $user->id)
            ->count();
        
        $this->forumStats = [
            'reputation' => $reputationSummary,
            'badges' => $badges,
            'thread_count' => $threadCount,
            'post_count' => $postCount,
            'warning_count' => $warningCount,
        ];
    }
    
    public function loadReviewsAndItineraries()
    {
        $user = Auth::user();
        
        // Load yacht reviews - remove limit to get all, we'll limit in the view
        $this->yachtReviews = $user->yachtReviews()
            ->where('is_approved', true)
            ->with(['yacht'])
            ->latest()
            ->get();
        
        // Load marina reviews - remove limit to get all, we'll limit in the view
        $this->marinaReviews = $user->marinaReviews()
            ->where('is_approved', true)
            ->with(['marina'])
            ->latest()
            ->get();
        
        // Load itinerary routes - remove limit to get all, we'll limit in the view
        $this->itineraryRoutes = $user->itineraryRoutes()
            ->latest('created_at')
            ->get();
    }
    
    public function loadCareerHistory()
    {
        $user = Auth::user();
        $this->careerHistoryEntries = $user->careerHistoryEntries()
            ->where('visible_on_profile', true)
            ->orderBy('display_order')
            ->orderBy('start_date', 'desc')
            ->get();
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

        // Refresh user model to update header
        $this->user = $user->fresh();
        
        // Dispatch event to update header in real-time
        $this->dispatch('profile-updated', [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
        ]);

        $this->editingProfile = false;
        session()->flash('profile-message', 'Name updated successfully.');
        
        // Refresh page to ensure header updates properly
        $this->dispatch('refresh-page');
    }
    
    public function updatedPhoto()
    {
        // Auto-upload when photo is selected
        $this->validate([
            'photo' => 'required|image|max:2048', // 2MB max
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
        
        $this->profile_photo_path = $path;
        $this->photo = null;
        
        // Refresh user model to get updated accessor
        $user = $user->fresh();
        $this->user = $user;
        
        // Get photo URL - use accessor if available, otherwise construct manually
        // Make sure we get the full absolute URL
        $photoUrl = $user->profile_photo_url;
        if (!$photoUrl) {
            $photoUrl = asset('storage/' . $path);
        }
        // Ensure it's a full URL (not relative)
        if (!str_starts_with($photoUrl, 'http')) {
            $photoUrl = url($photoUrl);
        }
        
        // Update local property for immediate UI update
        $this->profile_photo_path = $path;
        
        // Dispatch event to update header in real-time with full URL
        $this->dispatch('profile-photo-updated', [
            'photo_url' => $photoUrl,
            'photo_path' => $path,
        ]);
        
        session()->flash('profile-message', 'Profile photo updated successfully.');
        
        // Refresh page after a short delay to ensure image is properly displayed
        $this->dispatch('refresh-page');
    }
    
    public function updateProfessionalSummary()
    {
        $this->validate([
            'professional_summary' => 'nullable|string|max:2000',
        ]);

        $user = Auth::user();
        $user->update([
            'professional_summary' => $this->professional_summary,
        ]);

        $this->editingSummary = false;
        $this->editingProfile = false;
        session()->flash('profile-message', 'Professional summary updated successfully.');
    }
    
    public function updateCareerProfile()
    {
        $this->validate([
            'employment_type' => 'nullable|string|max:255',
            'expected_salary' => 'nullable|string|max:255',
            'current_yacht' => 'nullable|string|max:255',
            'vessel_preference' => 'nullable|string|max:255',
            'availability_status' => 'nullable|in:available,busy,looking_for_work,on_leave',
            'years_experience' => 'nullable|integer|min:0|max:100',
            'special_services' => 'nullable|string|max:255',
            'available_from' => 'nullable|date',
        ]);

        $user = Auth::user();
        // Current Position comes from role, so don't update it
        $user->update([
            'employment_type' => $this->employment_type,
            'expected_salary' => $this->expected_salary,
            'current_yacht' => $this->current_yacht,
            'vessel_preference' => $this->vessel_preference,
            'availability_status' => $this->availability_status,
            'years_experience' => $this->years_experience,
            'special_services' => $this->special_services,
            'available_from' => $this->available_from,
        ]);

        // Refresh current_position from role after update
        $this->current_position = $user->fresh()->roles->first()->name ?? 'N/A';
        
        $this->editingCareerProfile = false;
        $this->editingProfile = false;
        session()->flash('profile-message', 'Career profile updated successfully.');
    }
    
    public function updatePersonalDetails()
    {
        $this->validate([
            'nationality' => 'nullable|string|max:255',
            'passport_validity' => 'nullable|date',
            'date_of_birth' => 'nullable|date',
            'visas' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        $user->update([
            'nationality' => $this->nationality,
            'passport_validity' => $this->passport_validity,
            'dob' => $this->date_of_birth,
            'visas' => $this->visas,
        ]);

        $this->editingPersonalDetails = false;
        session()->flash('profile-message', 'Personal details updated successfully.');
    }
    
    public function updateSkills()
    {
        $this->validate([
            'specializations' => 'nullable|array',
        ]);

        $user = Auth::user();
        $user->update([
            'specializations' => $this->specializations,
        ]);

        $this->editingSkills = false;
        session()->flash('profile-message', 'Skills updated successfully.');
    }
    
    public function updateLanguages()
    {
        $this->validate([
            'languages' => 'nullable|array',
        ]);

        $user = Auth::user();
        $user->update([
            'languages' => $this->languages,
        ]);

        $this->editingLanguages = false;
        session()->flash('profile-message', 'Languages updated successfully.');
    }
    
    public function addCareerHistory()
    {
        // This will use the existing addPreviousYacht functionality
        // User can add career history via the previous yachts section
        // For now, just show a message
        session()->flash('profile-message', 'Use the "Add Career History" button to add your work experience.');
    }
    
    public function addCertificationWithDetails()
    {
        // Enhanced certification with details
        if ($this->newCertification) {
            $cert = [
                'name' => $this->newCertification,
                'issued_by' => '',
                'expiry_date' => null,
                'status' => 'pending',
            ];
            
            if (!in_array($cert, $this->certifications, true)) {
                $this->certifications[] = $cert;
                $this->newCertification = '';
                $this->updateCrewProfile();
            }
        }
    }

    // Photo upload/remove methods moved to ProfilePhotoController
    // This uses standard Laravel file uploads (works without tmpfile() on shared hosting)
    
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
        // Clear previous errors
        $this->resetErrorBag('newLanguage');
        
        if (empty(trim($this->newLanguage))) {
            $this->addError('newLanguage', 'Please enter a language name.');
            return;
        }
        
        // Check if language already exists (case-insensitive)
        $newLang = trim($this->newLanguage);
        foreach ($this->languages as $lang) {
            $langName = is_array($lang) ? ($lang['name'] ?? '') : $lang;
            if (strtolower(trim($langName)) === strtolower($newLang)) {
                $this->addError('newLanguage', 'This language is already added. Please add a different language.');
                return;
            }
        }
        
        // Add the language
        $this->languages[] = [
            'name' => $newLang,
            'proficiency' => 'Proficient',
            'read' => true,
            'write' => true,
            'speak' => true,
        ];
        $this->newLanguage = '';
    }
    
    public function removeLanguage($index)
    {
        if (isset($this->languages[$index])) {
            unset($this->languages[$index]);
            $this->languages = array_values($this->languages);
            
            // Save to database
            $user = Auth::user();
            $user->update([
                'languages' => $this->languages,
            ]);
            
            session()->flash('profile-message', 'Language removed successfully.');
        }
    }
    
    public function updatedLanguages($value, $key)
    {
        // Auto-save when any language checkbox is updated
        // $key format: "0.read", "1.write", etc.
        if (str_contains($key, '.')) {
            $user = Auth::user();
            $user->update([
                'languages' => $this->languages,
            ]);
        }
    }
    
    public function openCertificationModal($index = null)
    {
        $this->editingCertificationIndex = $index;
        if ($index !== null && isset($this->certifications[$index])) {
            $cert = $this->certifications[$index];
            $this->certificationName = is_array($cert) ? ($cert['name'] ?? '') : $cert;
            $this->certificationIssuedBy = is_array($cert) ? ($cert['issued_by'] ?? '') : '';
            $this->certificationExpiryDate = is_array($cert) && isset($cert['expiry_date']) ? $cert['expiry_date'] : '';
            $this->certificationStatus = is_array($cert) ? ($cert['status'] ?? 'pending') : 'pending';
        } else {
            $this->certificationName = $this->newCertification;
            $this->certificationIssuedBy = '';
            $this->certificationExpiryDate = '';
            $this->certificationStatus = 'pending';
        }
        $this->showCertificationModal = true;
    }
    
    public function closeCertificationModal()
    {
        $this->showCertificationModal = false;
        $this->editingCertificationIndex = null;
        $this->certificationName = '';
        $this->certificationIssuedBy = '';
        $this->certificationExpiryDate = '';
        $this->certificationStatus = 'pending';
        $this->newCertification = '';
    }
    
    public function saveCertification()
    {
        $this->validate([
            'certificationName' => 'required|string|max:255',
            'certificationIssuedBy' => 'nullable|string|max:255',
            'certificationExpiryDate' => 'nullable|date',
            'certificationStatus' => 'required|in:pending,verified,expired',
        ]);
        
        $certData = [
            'name' => $this->certificationName,
            'issued_by' => $this->certificationIssuedBy,
            'expiry_date' => $this->certificationExpiryDate ?: null,
            'status' => $this->certificationStatus,
        ];
        
        if ($this->editingCertificationIndex !== null) {
            // Update existing
            $this->certifications[$this->editingCertificationIndex] = $certData;
        } else {
            // Check if already exists
            $exists = false;
            foreach ($this->certifications as $cert) {
                $certName = is_array($cert) ? ($cert['name'] ?? '') : $cert;
                if ($certName === $this->certificationName) {
                    $exists = true;
                    break;
                }
            }
            
            if (!$exists) {
                $this->certifications[] = $certData;
            }
        }
        
        // Save to database
        $this->updateCrewProfile();
        $this->closeCertificationModal();
        session()->flash('profile-message', 'Certification saved successfully.');
    }
    
    public function addCertification()
    {
        // Open modal instead of directly adding
        $this->openCertificationModal();
    }
    
    public function removeCertification($index)
    {
        if (isset($this->certifications[$index])) {
            unset($this->certifications[$index]);
            $this->certifications = array_values($this->certifications);
            
            // Save to database
            $user = Auth::user();
            $user->update([
                'certifications' => $this->certifications,
            ]);
            
            session()->flash('profile-message', 'Certification removed successfully.');
        }
    }
    
    public function addSpecialization()
    {
        // Clear previous errors
        $this->resetErrorBag('newSpecialization');
        
        if (empty(trim($this->newSpecialization))) {
            $this->addError('newSpecialization', 'Please enter a skill name.');
            return;
        }
        
        // Check for duplicates (case-insensitive)
        $newSkill = trim($this->newSpecialization);
        foreach ($this->specializations as $skill) {
            if (strtolower(trim($skill)) === strtolower($newSkill)) {
                $this->addError('newSpecialization', 'This skill is already added. Please add a different skill.');
                return;
            }
        }
        
        // Add the skill
        $this->specializations[] = $newSkill;
        $this->newSpecialization = '';
    }
    
    public function removeSpecialization($index)
    {
        if (isset($this->specializations[$index])) {
            unset($this->specializations[$index]);
            $this->specializations = array_values($this->specializations);
            
            // Save to database
            $user = Auth::user();
            $user->update([
                'specializations' => $this->specializations,
            ]);
            
            session()->flash('profile-message', 'Skill removed successfully.');
        }
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
        if (isset($this->interests[$index])) {
            unset($this->interests[$index]);
            $this->interests = array_values($this->interests);
            
            // Save to database
            $user = Auth::user();
            $user->update([
                'interests' => $this->interests,
            ]);
            
            session()->flash('profile-message', 'Interest removed successfully.');
        }
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

    public function toggleLike($reviewId, $reviewType)
    {
        $user = Auth::user();
        
        if ($reviewType === 'yacht') {
            $review = YachtReview::find($reviewId);
        } else {
            $review = MarinaReview::find($reviewId);
        }
        
        if (!$review) {
            return;
        }
        
        // Check if user already voted
        $existingVote = ReviewVote::where('reviewable_type', get_class($review))
            ->where('reviewable_id', $review->id)
            ->where('review_id', $review->id)
            ->where('user_id', $user->id)
            ->first();
        
        if ($existingVote) {
            // Toggle: if helpful, remove; if not helpful, change to helpful
            if ($existingVote->is_helpful) {
                // Remove like
                $existingVote->delete();
                $review->decrement('helpful_count');
            } else {
                // Change to helpful
                $existingVote->update(['is_helpful' => true]);
                $review->increment('helpful_count');
                if ($review->not_helpful_count > 0) {
                    $review->decrement('not_helpful_count');
                }
            }
        } else {
            // Create new helpful vote
            ReviewVote::create([
                'reviewable_type' => get_class($review),
                'reviewable_id' => $review->id,
                'review_id' => $review->id,
                'user_id' => $user->id,
                'is_helpful' => true,
            ]);
            $review->increment('helpful_count');
        }
        
        // Reload reviews to get updated counts
        $this->loadReviewsAndItineraries();
    }
    
    public function hasUserLiked($reviewId, $reviewType)
    {
        $user = Auth::user();
        
        if ($reviewType === 'yacht') {
            $review = YachtReview::find($reviewId);
        } else {
            $review = MarinaReview::find($reviewId);
        }
        
        if (!$review) {
            return false;
        }
        
        return ReviewVote::where('reviewable_type', get_class($review))
            ->where('reviewable_id', $review->id)
            ->where('review_id', $review->id)
            ->where('user_id', $user->id)
            ->where('is_helpful', true)
            ->exists();
    }

    public function render()
    {
        return view('livewire.profile')->layout('layouts.app'); 
    }
}
