<?php

namespace App\Livewire\Training;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\TrainingUserCertification;
use App\Models\TrainingCertification;
use App\Models\TrainingCertificationReminder;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class UserCertificationDashboard extends Component
{
    use WithFileUploads;

    public $certifications;
    public $showAddModal = false;
    public $showEditModal = false;
    public $selectedCertification = null;
    
    // Form fields
    public $certification_id;
    public $issue_date;
    public $expiry_date;
    public $certificate_number;
    public $issuing_authority;
    public $certificate_document;
    public $notes;

    protected $rules = [
        'certification_id' => 'required|exists:training_certifications,id',
        'issue_date' => 'required|date',
        'expiry_date' => 'nullable|date|after:issue_date',
        'certificate_number' => 'nullable|string|max:255',
        'issuing_authority' => 'nullable|string|max:255',
        'notes' => 'nullable|string',
    ];

    public function mount()
    {
        $this->loadCertifications();
        $this->checkAndCreateReminders(); // Check reminders on-demand (no scheduler needed)
    }

    /**
     * Check and create reminders for expiring certifications
     * This runs on-demand when user views dashboard (no cron/scheduler required)
     */
    public function checkAndCreateReminders()
    {
        $user = Auth::user();
        $now = Carbon::now();

        foreach ($this->certifications as $cert) {
            if (!$cert->expiry_date) {
                continue;
            }

            $expiryDate = Carbon::parse($cert->expiry_date);
            $monthsUntilExpiry = $now->diffInMonths($expiryDate, false);

            // Create reminders at 6 months, 3 months, and 1 month before expiry
            $reminderTypes = [
                '6_months' => 6,
                '3_months' => 3,
                '1_month' => 1,
            ];

            foreach ($reminderTypes as $type => $months) {
                if ($monthsUntilExpiry <= $months && $monthsUntilExpiry > ($months - 1)) {
                    // Check if reminder already exists
                    $existingReminder = TrainingCertificationReminder::where('user_id', $user->id)
                        ->where('user_certification_id', $cert->id)
                        ->where('reminder_type', $type)
                        ->where('is_sent', false)
                        ->first();

                    if (!$existingReminder) {
                        TrainingCertificationReminder::create([
                            'user_id' => $user->id,
                            'user_certification_id' => $cert->id,
                            'reminder_type' => $type,
                            'reminder_date' => $now,
                            'is_sent' => false,
                            'course_recommendations' => $this->getCourseRecommendations($cert->certification_id),
                        ]);
                    }
                }
            }

            // Create expired reminder if certification is expired
            if ($expiryDate->isPast()) {
                $existingReminder = TrainingCertificationReminder::where('user_id', $user->id)
                    ->where('user_certification_id', $cert->id)
                    ->where('reminder_type', 'expired')
                    ->where('is_sent', false)
                    ->first();

                if (!$existingReminder) {
                    TrainingCertificationReminder::create([
                        'user_id' => $user->id,
                        'user_certification_id' => $cert->id,
                        'reminder_type' => 'expired',
                        'reminder_date' => $now,
                        'is_sent' => false,
                        'course_recommendations' => $this->getCourseRecommendations($cert->certification_id),
                    ]);
                }
            }
        }
    }

    /**
     * Get course recommendations for a certification
     */
    private function getCourseRecommendations($certificationId)
    {
        $courses = \App\Models\TrainingProviderCourse::where('certification_id', $certificationId)
            ->where('is_active', true)
            ->with(['provider', 'upcomingSchedules'])
            ->orderBy('rating_avg', 'desc')
            ->take(5)
            ->get();

        return $courses->map(function ($course) {
            return [
                'id' => $course->id,
                'provider' => $course->provider->name,
                'price' => $course->price,
                'ywc_price' => $course->ywc_price,
                'next_date' => $course->upcomingSchedules->first()?->start_date?->format('Y-m-d'),
            ];
        })->toArray();
    }

    public function loadCertifications()
    {
        $this->certifications = TrainingUserCertification::where('user_id', Auth::id())
            ->with(['certification.category', 'providerCourse.provider'])
            ->orderBy('expiry_date', 'asc')
            ->get();
    }

    public function openAddModal()
    {
        $this->resetForm();
        $this->showAddModal = true;
    }

    public function openEditModal($id)
    {
        $this->selectedCertification = TrainingUserCertification::findOrFail($id);
        $this->certification_id = $this->selectedCertification->certification_id;
        $this->issue_date = $this->selectedCertification->issue_date->format('Y-m-d');
        $this->expiry_date = $this->selectedCertification->expiry_date ? $this->selectedCertification->expiry_date->format('Y-m-d') : null;
        $this->certificate_number = $this->selectedCertification->certificate_number;
        $this->issuing_authority = $this->selectedCertification->issuing_authority;
        $this->notes = $this->selectedCertification->notes;
        $this->showEditModal = true;
    }

    public function closeModals()
    {
        $this->showAddModal = false;
        $this->showEditModal = false;
        $this->selectedCertification = null;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->certification_id = null;
        $this->issue_date = null;
        $this->expiry_date = null;
        $this->certificate_number = null;
        $this->issuing_authority = null;
        $this->certificate_document = null;
        $this->notes = null;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'user_id' => Auth::id(),
            'certification_id' => $this->certification_id,
            'issue_date' => $this->issue_date,
            'expiry_date' => $this->expiry_date,
            'certificate_number' => $this->certificate_number,
            'issuing_authority' => $this->issuing_authority,
            'notes' => $this->notes,
        ];

        if ($this->certificate_document) {
            $data['certificate_document_path'] = $this->certificate_document->store('certificates', 'public');
        }

        if ($this->selectedCertification) {
            $this->selectedCertification->update($data);
            session()->flash('success', 'Certification updated successfully.');
        } else {
            TrainingUserCertification::create($data);
            session()->flash('success', 'Certification added successfully.');
        }

        $this->loadCertifications();
        $this->closeModals();
    }

    public function delete($id)
    {
        $certification = TrainingUserCertification::findOrFail($id);
        $certification->delete();
        session()->flash('success', 'Certification removed successfully.');
        $this->loadCertifications();
    }

    public function getExpiringSoonCount()
    {
        return $this->certifications->filter(function ($cert) {
            return $cert->isExpiringSoon() && !$cert->isExpired();
        })->count();
    }

    public function getExpiredCount()
    {
        return $this->certifications->filter(function ($cert) {
            return $cert->isExpired();
        })->count();
    }

    public function getValidCount()
    {
        return $this->certifications->filter(function ($cert) {
            return $cert->status === 'valid';
        })->count();
    }

    public function render()
    {
        $availableCertifications = TrainingCertification::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('livewire.training.user-certification-dashboard', [
            'availableCertifications' => $availableCertifications,
        ])->layout('layouts.app');
    }
}
