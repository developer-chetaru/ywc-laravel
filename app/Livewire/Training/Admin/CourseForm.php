<?php

namespace App\Livewire\Training\Admin;

use Livewire\Component;
use App\Models\TrainingProviderCourse;
use App\Models\TrainingCertification;
use App\Models\TrainingProvider;
use Illuminate\Support\Facades\Auth;

class CourseForm extends Component
{
    public $courseId = null;

    public $certification_id;
    public $provider_id;
    public $price;
    public $ywc_discount_percentage;
    public $duration_days;
    public $duration_hours;
    public $language_of_instruction;
    public $format = 'in-person';
    public $booking_url;
    public $materials_included_text;
    public $meals_included = false;
    public $meals_details;
    public $accommodation_included = false;
    public $accommodation_details;
    public $parking_included = false;
    public $transport_included = false;
    public $is_active = true;
    public $requires_admin_approval = false;

    protected $rules = [
        'certification_id'        => 'required|exists:training_certifications,id',
        'provider_id'             => 'required|exists:training_providers,id',
        'price'                   => 'required|numeric|min:0',
        'ywc_discount_percentage' => 'required|numeric|min:0|max:100',
        'duration_days'           => 'required|integer|min:1',
        'duration_hours'          => 'nullable|integer|min:0',
        'language_of_instruction' => 'required|string|max:255',
        'format'                  => 'required|string|max:50',
        'booking_url'             => 'nullable|url|max:2048',
        'materials_included_text' => 'nullable|string|max:2000',
        'meals_included'          => 'boolean',
        'meals_details'           => 'nullable|string|max:1000',
        'accommodation_included'  => 'boolean',
        'accommodation_details'   => 'nullable|string|max:1000',
        'parking_included'        => 'boolean',
        'transport_included'      => 'boolean',
        'is_active'               => 'boolean',
        'requires_admin_approval' => 'boolean',
    ];

    public function mount($id = null)
    {
        if (!Auth::user()->hasRole('super_admin')) {
            abort(403, 'Unauthorized access');
        }

        if ($id) {
            $this->courseId = $id;
            $course = TrainingProviderCourse::findOrFail($id);

            $this->certification_id       = $course->certification_id;
            $this->provider_id            = $course->provider_id;
            $this->price                  = $course->price;
            $this->ywc_discount_percentage= $course->ywc_discount_percentage;
            $this->duration_days          = $course->duration_days;
            $this->duration_hours         = $course->duration_hours;
            $this->language_of_instruction= $course->language_of_instruction;
            $this->format                 = $course->format;
            $this->booking_url            = $course->booking_url;
            $this->materials_included_text= $course->materials_included ? implode(', ', $course->materials_included) : null;
            $this->meals_included         = $course->meals_included;
            $this->meals_details          = $course->meals_details;
            $this->accommodation_included = $course->accommodation_included;
            $this->accommodation_details  = $course->accommodation_details;
            $this->parking_included       = $course->parking_included;
            $this->transport_included     = $course->transport_included;
            $this->is_active              = $course->is_active;
            $this->requires_admin_approval= $course->requires_admin_approval;
        } else {
            // defaults for new course
            $this->language_of_instruction = 'English';
            $this->ywc_discount_percentage = $this->ywc_discount_percentage ?? 20;
            $this->duration_days = $this->duration_days ?? 1;
        }
    }

    public function save()
    {
        $this->validate();

        // Prevent duplicate course for same certification + provider (unique index)
        $duplicateQuery = TrainingProviderCourse::where('certification_id', $this->certification_id)
            ->where('provider_id', $this->provider_id);

        if ($this->courseId) {
            // Exclude current record when editing
            $duplicateQuery->where('id', '!=', $this->courseId);
        }

        if ($duplicateQuery->exists()) {
            $this->addError('provider_id', 'A course for this provider and certification already exists. Please edit the existing course instead of creating a duplicate.');
            session()->flash('error', 'A course for this provider and certification already exists.');
            return;
        }

        $data = [
            'certification_id'        => $this->certification_id,
            'provider_id'             => $this->provider_id,
            'price'                   => $this->price,
            'ywc_discount_percentage' => $this->ywc_discount_percentage,
            'duration_days'           => $this->duration_days,
            'duration_hours'          => $this->duration_hours,
            'language_of_instruction' => $this->language_of_instruction,
            'format'                  => $this->format,
            'booking_url'             => $this->booking_url,
            'materials_included'      => $this->materials_included_text
                                            ? array_map('trim', explode(',', $this->materials_included_text))
                                            : null,
            'meals_included'          => $this->meals_included,
            'meals_details'           => $this->meals_details,
            'accommodation_included'  => $this->accommodation_included,
            'accommodation_details'   => $this->accommodation_details,
            'parking_included'        => $this->parking_included,
            'transport_included'      => $this->transport_included,
            'is_active'               => $this->is_active,
            'requires_admin_approval' => $this->requires_admin_approval,
        ];

        if ($this->courseId) {
            $course = TrainingProviderCourse::findOrFail($this->courseId);
            $course->update($data);
            session()->flash('success', 'Course updated successfully.');
        } else {
            TrainingProviderCourse::create($data);
            session()->flash('success', 'Course created successfully.');
        }

        return redirect()->route('training.admin.courses');
    }

    public function render()
    {
        $providers = TrainingProvider::where('is_active', true)->orderBy('name')->get();
        $certifications = TrainingCertification::where('is_active', true)->orderBy('name')->get();

        return view('livewire.training.admin.course-form', [
            'providers' => $providers,
            'certifications' => $certifications,
        ])->layout('layouts.app');
    }
}


