<?php

namespace App\Livewire\JobBoard;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use App\Models\JobPost;
use App\Models\JobPostScreeningQuestion;
use App\Models\Yacht;
use App\Services\JobBoard\JobNotificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

#[Layout('layouts.app')]
class PostJob extends Component
{
    use WithFileUploads;

    public $jobType = 'permanent'; // permanent or temporary
    public $temporaryWorkType = 'day_work';
    
    // Basic Details
    public $positionTitle = '';
    public $department = 'deck';
    public $positionLevel = '';
    
    // Vessel Info
    public $yachtId = null;
    public $vesselType = '';
    public $vesselSize = '';
    public $flag = '';
    public $programType = 'private';
    public $cruisingRegions = '';
    
    // Contract Details (Permanent)
    public $contractType = '';
    public $rotationSchedule = 'none';
    public $startDate = '';
    public $startDateFlexibility = 'asap';
    public $contractDurationMonths = '';
    
    // Temporary Work Details
    public $workStartDate = '';
    public $workEndDate = '';
    public $workStartTime = '';
    public $workEndTime = '';
    public $urgencyLevel = 'normal';
    
    // Location
    public $location = '';
    public $latitude = '';
    public $longitude = '';
    public $berthDetails = '';
    
    // Compensation
    public $salaryMin = '';
    public $salaryMax = '';
    public $salaryCurrency = 'EUR';
    public $salaryNegotiable = true;
    public $dayRateMin = '';
    public $dayRateMax = '';
    public $hourlyRate = '';
    
    // Benefits
    public $benefits = [];
    public $additionalBenefits = '';
    
    // Requirements
    public $requiredCertifications = [];
    public $preferredCertifications = [];
    public $minYearsExperience = '';
    public $minVesselSizeExperience = '';
    public $essentialSkills = [];
    public $preferredSkills = [];
    public $requiredLanguages = [];
    public $preferredLanguages = [];
    public $otherRequirements = '';
    public $whatToBring = '';
    
    // Description
    public $aboutPosition = '';
    public $aboutVesselProgram = '';
    public $responsibilities = '';
    public $idealCandidate = '';
    public $crewSize = '';
    public $crewAtmosphere = '';
    
    // Contact
    public $contactName = '';
    public $contactPhone = '';
    public $whatsappAvailable = false;
    public $paymentMethod = 'cash';
    public $paymentTiming = '';
    public $cancellationPolicy = '';
    
    // Settings
    public $contactPreference = 'ywc_only';
    public $responseTimeline = '';
    public $publicPost = true;
    public $allowSearchEngineIndex = true;
    public $notifyMatchingCrew = true;
    public $featuredPosting = false;
    
    // Screening Questions
    public $screeningQuestions = [];
    public $newQuestion = '';
    
    public $editingJobId = null;

    protected $listeners = ['yachtSelected'];

    public function mount($id = null)
    {
        // Block admins from posting jobs as regular users
        if (Auth::user()->hasRole('super_admin')) {
            abort(403, 'Admins cannot post jobs as regular users. Use the admin panel to manage jobs.');
        }

        // Verify user is captain (has Captain role) or has verified vessel verification
        $user = Auth::user();
        $hasCaptainRole = $user->hasRole('Captain');
        $hasVerifiedVessel = $user->vesselVerification && $user->vesselVerification->isVerified();
        
        if (!$hasCaptainRole && !$hasVerifiedVessel) {
            return redirect()->route('job-board.verify')
                ->with('error', 'You must be a captain or have verified vessel verification to post jobs.');
        }

        if ($id) {
            $this->loadJob($id);
        }
        
        // Load user's yachts
        $this->yachts = Yacht::where('created_by_user_id', Auth::id())
            ->orWhereHas('reviews', function($q) {
                $q->where('user_id', Auth::id());
            })
            ->get();
    }

    public function loadJob($id)
    {
        $job = JobPost::findOrFail($id);
        
        if ($job->user_id !== Auth::id()) {
            abort(403);
        }

        $this->editingJobId = $id;
        $this->jobType = $job->job_type;
        $this->positionTitle = $job->position_title;
        // ... load all fields
        
        // Load screening questions
        $this->screeningQuestions = $job->screeningQuestions->map(function($q) {
            return [
                'id' => $q->id,
                'question_text' => $q->question_text,
                'question_type' => $q->question_type,
                'is_required' => $q->is_required,
            ];
        })->toArray();
    }

    public function addScreeningQuestion()
    {
        if (count($this->screeningQuestions) >= 5) {
            session()->flash('error', 'Maximum 5 screening questions allowed');
            return;
        }

        $this->screeningQuestions[] = [
            'question_text' => $this->newQuestion,
            'question_type' => 'textarea',
            'is_required' => true,
        ];
        $this->newQuestion = '';
    }

    public function removeScreeningQuestion($index)
    {
        unset($this->screeningQuestions[$index]);
        $this->screeningQuestions = array_values($this->screeningQuestions);
    }

    public function yachtSelected($yachtId)
    {
        $this->yachtId = $yachtId;
        $yacht = Yacht::find($yachtId);
        if ($yacht) {
            $this->vesselType = $yacht->type;
            $this->vesselSize = $yacht->length_meters;
            $this->flag = $yacht->flag_registry;
        }
    }

    public function save($publish = false)
    {
        $validated = $this->validate();

        DB::transaction(function() use ($validated, $publish) {
            if ($this->editingJobId) {
                $job = JobPost::findOrFail($this->editingJobId);
                $job->update($validated);
            } else {
                $job = JobPost::create(array_merge($validated, [
                    'user_id' => Auth::id(),
                    'status' => $publish ? 'active' : 'draft',
                ]));
            }

            // Save screening questions
            $job->screeningQuestions()->delete();
            foreach ($this->screeningQuestions as $index => $question) {
                JobPostScreeningQuestion::create([
                    'job_post_id' => $job->id,
                    'order' => $index + 1,
                    'question_text' => $question['question_text'],
                    'question_type' => $question['question_type'] ?? 'textarea',
                    'is_required' => $question['is_required'] ?? true,
                ]);
            }

            if ($publish) {
                $job->publish();
                
                // Notify matching crew
                app(JobNotificationService::class)->notifyMatchingCrew($job);
                
                session()->flash('success', 'Job posted successfully!');
                return redirect()->route('job-board.detail', $job->id);
            } else {
                session()->flash('success', 'Job saved as draft');
            }
        });
    }

    public function publish()
    {
        $this->save(true);
    }

    protected function rules(): array
    {
        $rules = [
            'jobType' => 'required|in:permanent,temporary',
            'positionTitle' => 'required|string|max:255',
            'department' => 'required|in:deck,interior,engine,galley,other',
            'location' => 'required|string|max:255',
        ];

        if ($this->jobType === 'permanent') {
            $rules['contractType'] = 'required';
            $rules['salaryMin'] = 'required|numeric|min:0';
            $rules['salaryMax'] = 'required|numeric|min:0|gte:salaryMin';
        } else {
            $rules['temporaryWorkType'] = 'required';
            $rules['workStartDate'] = 'required|date';
            $rules['workEndDate'] = 'required|date|after_or_equal:workStartDate';
            $rules['dayRateMin'] = 'required|numeric|min:0';
            $rules['dayRateMax'] = 'required|numeric|min:0|gte:dayRateMin';
        }

        return $rules;
    }

    public function render()
    {
        return view('livewire.job-board.post-job', [
            'yachts' => Yacht::where('created_by_user_id', Auth::id())->get(),
        ]);
    }
}
