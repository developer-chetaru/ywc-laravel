<?php

namespace App\Livewire\FinancialPlanning\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\FinancialEducationalContent;
use App\Models\FinancialAdvisor;
use App\Models\FinancialSuccessStory;
use App\Models\FinancialConsultation;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class FinancialAdminPanel extends Component
{
    use WithPagination;

    public $activeTab = 'overview';
    public $showForm = false;
    public $formType = ''; // advisor, content, story
    public $editingId = null;

    public function mount()
    {
        // Route middleware handles access control
        // If we reach here, access is granted - no need to check again
        // Livewire redirects in mount() can cause issues, so let middleware handle it
    }

    // Educational Content Management
    public function openContentForm($contentId = null)
    {
        $this->formType = 'content';
        $this->editingId = $contentId;
        $this->showForm = true;
    }

    // Advisor Management
    public function openAdvisorForm($advisorId = null)
    {
        $this->formType = 'advisor';
        $this->editingId = $advisorId;
        $this->showForm = true;
    }

    // Success Story Management
    public function openStoryForm($storyId = null)
    {
        $this->formType = 'story';
        $this->editingId = $storyId;
        $this->showForm = true;
    }

    public function closeForm()
    {
        $this->showForm = false;
        $this->formType = '';
        $this->editingId = null;
    }

    // Consultation Status Management
    public function updateConsultationStatus($consultationId, $status)
    {
        $consultation = FinancialConsultation::findOrFail($consultationId);
        
        $allowedStatuses = ['pending', 'confirmed', 'cancelled', 'completed'];
        if (!in_array($status, $allowedStatuses)) {
            session()->flash('error', 'Invalid status.');
            return;
        }

        $consultation->update(['status' => $status]);
        
        session()->flash('message', "Consultation status updated to " . ucfirst($status) . ".");
        $this->resetPage();
    }

    public function render()
    {
        $stats = [
            'total_users' => User::whereHas('financialAccounts')->orWhereHas('financialGoals')->count(),
            'total_consultations' => FinancialConsultation::count(),
            'pending_consultations' => FinancialConsultation::where('status', 'pending')->count(),
            'total_advisors' => FinancialAdvisor::count(),
            'total_stories' => FinancialSuccessStory::where('is_published', true)->count(),
        ];

        $advisors = FinancialAdvisor::orderBy('created_at', 'desc')->paginate(10);
        $content = FinancialEducationalContent::orderBy('created_at', 'desc')->paginate(10);
        $stories = FinancialSuccessStory::orderBy('created_at', 'desc')->paginate(10);
        $consultations = FinancialConsultation::with(['user', 'advisor'])->orderBy('created_at', 'desc')->paginate(10);

        return view('livewire.financial-planning.admin.financial-admin-panel', [
            'stats' => $stats,
            'advisors' => $advisors,
            'content' => $content,
            'stories' => $stories,
            'consultations' => $consultations,
        ]);
    }
}

