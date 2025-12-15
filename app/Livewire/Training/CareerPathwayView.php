<?php

namespace App\Livewire\Training;

use Livewire\Component;
use App\Models\TrainingCareerPathway;
use App\Models\TrainingCertification;
use App\Models\TrainingUserCertification;
use Illuminate\Support\Facades\Auth;

class CareerPathwayView extends Component
{
    public $certificationId;
    public $certification;
    public $pathways = [];
    public $userCertifications = [];

    public function mount($certificationId = null)
    {
        if ($certificationId) {
            $this->certificationId = $certificationId;
            $this->certification = TrainingCertification::findOrFail($certificationId);
        }

        $this->loadPathways();
        $this->loadUserCertifications();
    }

    public function loadPathways()
    {
        $query = TrainingCareerPathway::where('is_active', true);

        if ($this->certificationId) {
            // Find pathways that include this certification
            $query->whereJsonContains('certification_sequence', (string)$this->certificationId);
        }

        $this->pathways = $query->orderBy('sort_order')->get();
    }

    public function loadUserCertifications()
    {
        if (Auth::check()) {
            $this->userCertifications = TrainingUserCertification::where('user_id', Auth::id())
                ->where('status', 'valid')
                ->pluck('certification_id')
                ->toArray();
        }
    }

    public function isCertificationCompleted($certificationId)
    {
        return in_array($certificationId, $this->userCertifications);
    }

    public function render()
    {
        return view('livewire.training.career-pathway-view')->layout('layouts.app');
    }
}
