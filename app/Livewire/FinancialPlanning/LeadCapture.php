<?php

namespace App\Livewire\FinancialPlanning;

use Livewire\Component;
use App\Models\FinancialCalculation;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class LeadCapture extends Component
{
    public $email = '';
    public $name = '';
    public $show_form = false;
    public $calculation_id = null;

    public function mount($calculationId = null)
    {
        $this->calculation_id = $calculationId;
        
        // Show form if user is not logged in and has a calculation
        if (!auth()->check() && $calculationId) {
            $this->show_form = true;
        }
    }

    public function saveLead()
    {
        $this->validate([
            'email' => 'required|email|max:255',
            'name' => 'nullable|string|max:255',
        ]);

        try {
            // Save email to calculation if calculation_id exists
            if ($this->calculation_id) {
                $calculation = FinancialCalculation::where('session_id', session()->getId())
                    ->whereNull('user_id')
                    ->find($this->calculation_id);

                if ($calculation) {
                    // Store email in input_data for lead capture
                    $inputData = $calculation->input_data ?? [];
                    $inputData['lead_email'] = $this->email;
                    $inputData['lead_name'] = $this->name;
                    
                    $calculation->update([
                        'input_data' => $inputData,
                        'session_id' => session()->getId(),
                    ]);

                    // Optionally send welcome email with results summary
                    // Mail::to($this->email)->send(new CalculatorResultsMail($calculation));
                }
            }

            session()->flash('lead_saved', true);
            $this->show_form = false;
            
            // Redirect to registration with pre-filled email
            return redirect()->route('register')->with('email', $this->email);

        } catch (\Exception $e) {
            throw ValidationException::withMessages([
                'email' => 'Unable to save. Please try again.',
            ]);
        }
    }

    public function skip()
    {
        $this->show_form = false;
    }

    public function render()
    {
        return view('livewire.financial-planning.lead-capture');
    }
}

