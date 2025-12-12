<?php

namespace App\Livewire\MentalHealth\Admin;

use Livewire\Component;
use App\Models\MentalHealthTherapist;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TherapistForm extends Component
{
    public $therapistId = null;
    public $isEditing = false;
    public $createNewUser = true;

    // User fields
    public $user_id = '';
    public $first_name = '';
    public $last_name = '';
    public $email = '';
    public $password = '';
    public $phone = '';

    // Therapist fields
    public $biography = '';
    public $specializations = [];
    public $specializationInput = '';
    public $languages_spoken = [];
    public $languageInput = '';
    public $therapeutic_approaches = [];
    public $approachInput = '';
    public $years_experience = 0;
    public $timezone = '';
    public $base_hourly_rate = 0;
    public $application_status = 'pending';
    public $is_active = false;
    public $is_featured = false;

    public function mount($id = null)
    {
        // Check if user is super admin
        if (!Auth::user()->hasRole('super_admin')) {
            abort(403, 'Unauthorized access');
        }

        if ($id) {
            $this->therapistId = $id;
            $this->isEditing = true;
            $this->loadTherapist();
        }
    }

    protected function rules()
    {
        if ($this->createNewUser || $this->isEditing) {
            $emailRule = 'required|email';
            if ($this->isEditing && $this->user_id) {
                $emailRule .= '|unique:users,email,' . $this->user_id;
            } else {
                $emailRule .= '|unique:users,email';
            }

            return [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => $emailRule,
                'password' => $this->isEditing ? 'nullable|min:8' : 'required|min:8',
                'biography' => 'nullable|string',
                'years_experience' => 'nullable|integer|min:0',
                'base_hourly_rate' => 'nullable|numeric|min:0',
                'timezone' => 'nullable|string',
            ];
        } else {
            return [
                'user_id' => 'required|exists:users,id',
                'biography' => 'nullable|string',
                'years_experience' => 'nullable|integer|min:0',
                'base_hourly_rate' => 'nullable|numeric|min:0',
                'timezone' => 'nullable|string',
            ];
        }
    }

    public function loadTherapist()
    {
        $therapist = MentalHealthTherapist::with('user')->find($this->therapistId);
        if (!$therapist) {
            session()->flash('error', 'Therapist not found.');
            return redirect()->route('mental-health.admin.therapists');
        }

        $this->user_id = $therapist->user_id;
        $this->first_name = $therapist->user->first_name;
        $this->last_name = $therapist->user->last_name;
        $this->email = $therapist->user->email;
        $this->phone = $therapist->user->phone ?? '';
        $this->biography = $therapist->biography ?? '';
        $this->specializations = $therapist->specializations ?? [];
        $this->specializationInput = implode(', ', $therapist->specializations ?? []);
        $this->languages_spoken = $therapist->languages_spoken ?? [];
        $this->languageInput = implode(', ', $therapist->languages_spoken ?? []);
        $this->therapeutic_approaches = $therapist->therapeutic_approaches ?? [];
        $this->approachInput = implode(', ', $therapist->therapeutic_approaches ?? []);
        $this->years_experience = $therapist->years_experience ?? 0;
        $this->timezone = $therapist->timezone ?? '';
        $this->base_hourly_rate = $therapist->base_hourly_rate ?? 0;
        $this->application_status = $therapist->application_status;
        $this->is_active = $therapist->is_active;
        $this->is_featured = $therapist->is_featured;
        $this->createNewUser = false; // When editing, we're working with existing user
    }

    public function addSpecialization()
    {
        if ($this->specializationInput) {
            $newSpecs = array_map('trim', explode(',', $this->specializationInput));
            $this->specializations = array_unique(array_merge($this->specializations, $newSpecs));
            $this->specializationInput = '';
        }
    }

    public function removeSpecialization($spec)
    {
        $this->specializations = array_values(array_filter($this->specializations, fn($s) => $s !== $spec));
    }

    public function addLanguage()
    {
        if ($this->languageInput) {
            $newLangs = array_map('trim', explode(',', $this->languageInput));
            $this->languages_spoken = array_unique(array_merge($this->languages_spoken, $newLangs));
            $this->languageInput = '';
        }
    }

    public function removeLanguage($lang)
    {
        $this->languages_spoken = array_values(array_filter($this->languages_spoken, fn($l) => $l !== $lang));
    }

    public function addApproach()
    {
        if ($this->approachInput) {
            $newApproaches = array_map('trim', explode(',', $this->approachInput));
            $this->therapeutic_approaches = array_unique(array_merge($this->therapeutic_approaches, $newApproaches));
            $this->approachInput = '';
        }
    }

    public function removeApproach($approach)
    {
        $this->therapeutic_approaches = array_values(array_filter($this->therapeutic_approaches, fn($a) => $a !== $approach));
    }

    public function save()
    {
        $this->validate($this->rules());

        // Process arrays
        if ($this->specializationInput) {
            $newSpecs = array_map('trim', explode(',', $this->specializationInput));
            $this->specializations = array_unique(array_merge($this->specializations, $newSpecs));
        }
        if ($this->languageInput) {
            $newLangs = array_map('trim', explode(',', $this->languageInput));
            $this->languages_spoken = array_unique(array_merge($this->languages_spoken, $newLangs));
        }
        if ($this->approachInput) {
            $newApproaches = array_map('trim', explode(',', $this->approachInput));
            $this->therapeutic_approaches = array_unique(array_merge($this->therapeutic_approaches, $newApproaches));
        }

        // Create or get user
        if ($this->createNewUser && !$this->isEditing) {
            $user = User::create([
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'phone' => $this->phone,
                'status' => 'active',
                'is_active' => true,
            ]);
            $user->assignRole('user');
            $userId = $user->id;
        } elseif ($this->isEditing && $this->therapistId) {
            $therapist = MentalHealthTherapist::find($this->therapistId);
            $user = $therapist->user;
            $user->update([
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'phone' => $this->phone,
            ]);
            if ($this->password) {
                $user->update(['password' => Hash::make($this->password)]);
            }
            $userId = $user->id;
        } else {
            $userId = $this->user_id;
        }

        $data = [
            'user_id' => $userId,
            'biography' => $this->biography,
            'specializations' => $this->specializations,
            'languages_spoken' => $this->languages_spoken,
            'therapeutic_approaches' => $this->therapeutic_approaches,
            'years_experience' => $this->years_experience,
            'timezone' => $this->timezone,
            'base_hourly_rate' => $this->base_hourly_rate,
            'application_status' => $this->application_status,
            'is_active' => $this->is_active,
            'is_featured' => $this->is_featured,
        ];

        if ($this->isEditing) {
            $therapist = MentalHealthTherapist::find($this->therapistId);
            $therapist->update($data);
            session()->flash('message', 'Therapist updated successfully.');
        } else {
            MentalHealthTherapist::create($data);
            session()->flash('message', 'Therapist created successfully.');
        }

        return redirect()->route('mental-health.admin.therapists');
    }

    public function render()
    {
        $availableUsers = User::whereDoesntHave('mentalHealthTherapist')
            ->where('is_active', true)
            ->orderBy('first_name')
            ->get();

        return view('livewire.mental-health.admin.therapist-form', [
            'availableUsers' => $availableUsers,
        ])->layout('layouts.app');
    }
}

