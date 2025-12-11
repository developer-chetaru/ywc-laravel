<?php

namespace App\Livewire\FinancialPlanning;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\FinancialAdvisor;
use App\Models\FinancialConsultation;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class AdvisoryServices extends Component
{
    use WithPagination;

    public $showBookingForm = false;
    public $selectedAdvisor = null;
    public $consultationType = '60min';
    public $scheduledDate = '';
    public $scheduledTime = '';
    public $preConsultationNotes = '';

    public function mount()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        $this->scheduledDate = now()->addDays(7)->format('Y-m-d');
        $this->scheduledTime = '10:00';
    }

    public function openBooking($advisorId)
    {
        $this->selectedAdvisor = FinancialAdvisor::where('id', $advisorId)
            ->where('is_active', true)
            ->firstOrFail();
        $this->showBookingForm = true;
    }

    public function closeBooking()
    {
        $this->showBookingForm = false;
        $this->selectedAdvisor = null;
        $this->reset(['consultationType', 'scheduledDate', 'scheduledTime', 'preConsultationNotes']);
    }

    public function bookConsultation()
    {
        $this->validate([
            'consultationType' => 'required|in:30min,60min,90min,specialty',
            'scheduledDate' => 'required|date|after:today',
            'scheduledTime' => 'required',
            'preConsultationNotes' => 'nullable|string',
        ]);

        $scheduledAt = $this->scheduledDate . ' ' . $this->scheduledTime . ':00';

        FinancialConsultation::create([
            'user_id' => Auth::id(),
            'advisor_id' => $this->selectedAdvisor->id,
            'type' => $this->consultationType,
            'status' => 'pending',
            'scheduled_at' => $scheduledAt,
            'pre_consultation_notes' => $this->preConsultationNotes,
            'amount' => $this->selectedAdvisor->hourly_rate ? 
                ($this->consultationType === '30min' ? $this->selectedAdvisor->hourly_rate * 0.5 :
                 ($this->consultationType === '60min' ? $this->selectedAdvisor->hourly_rate :
                 $this->selectedAdvisor->hourly_rate * 1.5)) : null,
        ]);

        session()->flash('message', 'Consultation booked successfully! You will receive a confirmation email.');
        $this->closeBooking();
        $this->resetPage();
    }

    public function render()
    {
        $advisors = FinancialAdvisor::where('is_active', true)
            ->orderBy('rating', 'desc')
            ->paginate(9);

        $myConsultations = FinancialConsultation::where('user_id', Auth::id())
            ->with('advisor')
            ->orderBy('scheduled_at', 'asc') // Show upcoming first
            ->get(); // Show all consultations, not just 5

        return view('livewire.financial-planning.advisory-services', [
            'advisors' => $advisors,
            'myConsultations' => $myConsultations,
        ]);
    }
}

