<?php

namespace App\Livewire\Training;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\TrainingProvider;
use App\Models\TrainingProviderCourse;
use App\Models\TrainingCertification;
use App\Models\TrainingCourseLocation;
use App\Models\TrainingCourseSchedule;
use App\Models\TrainingProviderGallery;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ProviderPortal extends Component
{
    use WithPagination, WithFileUploads;

    public $provider;
    public $showCourseModal = false;
    public $showLocationModal = false;
    public $showScheduleModal = false;
    public $showGalleryModal = false;
    public $selectedCourse = null;
    public $selectedLocation = null;
    public $selectedSchedule = null;
    
    // Course form fields
    public $certification_id;
    public $price;
    public $ywc_discount_percentage = 20;
    public $duration_days;
    public $duration_hours;
    public $class_size_max;
    public $language_of_instruction = 'English';
    public $format = 'in-person';
    public $course_structure;
    public $daily_schedule = [];
    public $learning_outcomes = [];
    public $assessment_methods = [];
    public $materials_included = [];
    public $accommodation_included = false;
    public $accommodation_details;
    public $meals_included = false;
    public $meals_details;
    public $parking_included = false;
    public $transport_included = false;
    public $re_sits_included = false;
    public $special_features;
    public $booking_url;
    
    // Location form fields
    public $location_name;
    public $location_address;
    public $location_city;
    public $location_country;
    public $location_region;
    
    // Schedule form fields
    public $schedule_start_date;
    public $schedule_end_date;
    public $schedule_start_time;
    public $schedule_end_time;
    public $schedule_available_spots;
    public $schedule_location_id;
    
    // Gallery
    public $gallery_images = [];
    public $gallery_category;

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        // Get or create provider for current user
        $this->provider = TrainingProvider::where('user_id', Auth::id())->first();
        
        if (!$this->provider) {
            // Create a basic provider profile
            $this->provider = TrainingProvider::create([
                'user_id' => Auth::id(),
                'name' => Auth::user()->first_name . ' ' . Auth::user()->last_name . ' Training',
                'slug' => Str::slug(Auth::user()->first_name . ' ' . Auth::user()->last_name . ' Training'),
                'is_active' => false, // Requires admin approval
            ]);
        }
    }

    public function openCourseModal($courseId = null)
    {
        if ($courseId) {
            $this->selectedCourse = TrainingProviderCourse::findOrFail($courseId);
            $this->loadCourseData();
        } else {
            $this->resetCourseForm();
        }
        $this->showCourseModal = true;
    }

    public function loadCourseData()
    {
        $course = $this->selectedCourse;
        $this->certification_id = $course->certification_id;
        $this->price = $course->price;
        $this->ywc_discount_percentage = $course->ywc_discount_percentage;
        $this->duration_days = $course->duration_days;
        $this->duration_hours = $course->duration_hours;
        $this->class_size_max = $course->class_size_max;
        $this->language_of_instruction = $course->language_of_instruction;
        $this->format = $course->format;
        $this->course_structure = $course->course_structure;
        $this->daily_schedule = $course->daily_schedule ?? [];
        $this->learning_outcomes = $course->learning_outcomes ?? [];
        $this->assessment_methods = $course->assessment_methods ?? [];
        $this->materials_included = $course->materials_included ?? [];
        $this->accommodation_included = $course->accommodation_included;
        $this->accommodation_details = $course->accommodation_details;
        $this->meals_included = $course->meals_included;
        $this->meals_details = $course->meals_details;
        $this->parking_included = $course->parking_included;
        $this->transport_included = $course->transport_included;
        $this->re_sits_included = $course->re_sits_included;
        $this->special_features = $course->special_features;
        $this->booking_url = $course->booking_url;
    }

    public function resetCourseForm()
    {
        $this->selectedCourse = null;
        $this->certification_id = null;
        $this->price = null;
        $this->ywc_discount_percentage = 20;
        $this->duration_days = null;
        $this->duration_hours = null;
        $this->class_size_max = null;
        $this->language_of_instruction = 'English';
        $this->format = 'in-person';
        $this->course_structure = null;
        $this->daily_schedule = [];
        $this->learning_outcomes = [];
        $this->assessment_methods = [];
        $this->materials_included = [];
        $this->accommodation_included = false;
        $this->accommodation_details = null;
        $this->meals_included = false;
        $this->meals_details = null;
        $this->parking_included = false;
        $this->transport_included = false;
        $this->re_sits_included = false;
        $this->special_features = null;
        $this->booking_url = null;
    }

    public function saveCourse()
    {
        $this->validate([
            'certification_id' => 'required|exists:training_certifications,id',
            'price' => 'required|numeric|min:0',
            'ywc_discount_percentage' => 'nullable|numeric|min:0|max:100',
            'duration_days' => 'required|integer|min:1',
            'duration_hours' => 'nullable|integer',
            'class_size_max' => 'nullable|integer',
            'language_of_instruction' => 'required|string',
            'format' => 'required|in:in-person,online,hybrid,self-paced',
            'booking_url' => 'nullable|url',
        ]);

        $data = [
            'provider_id' => $this->provider->id,
            'certification_id' => $this->certification_id,
            'price' => $this->price,
            'ywc_discount_percentage' => $this->ywc_discount_percentage ?? 20,
            'duration_days' => $this->duration_days,
            'duration_hours' => $this->duration_hours,
            'class_size_max' => $this->class_size_max,
            'language_of_instruction' => $this->language_of_instruction,
            'format' => $this->format,
            'course_structure' => $this->course_structure,
            'daily_schedule' => $this->daily_schedule,
            'learning_outcomes' => $this->learning_outcomes,
            'assessment_methods' => $this->assessment_methods,
            'materials_included' => $this->materials_included,
            'accommodation_included' => $this->accommodation_included,
            'accommodation_details' => $this->accommodation_details,
            'meals_included' => $this->meals_included,
            'meals_details' => $this->meals_details,
            'parking_included' => $this->parking_included,
            'transport_included' => $this->transport_included,
            're_sits_included' => $this->re_sits_included,
            'special_features' => $this->special_features,
            'booking_url' => $this->booking_url,
            'ywc_tracking_code' => 'YWC' . $this->provider->id . '-' . time(),
        ];

        if ($this->selectedCourse) {
            $this->selectedCourse->update($data);
            session()->flash('success', 'Course updated successfully.');
        } else {
            TrainingProviderCourse::create($data);
            session()->flash('success', 'Course added successfully.');
        }

        $this->closeCourseModal();
    }

    public function closeCourseModal()
    {
        $this->showCourseModal = false;
        $this->resetCourseForm();
    }

    public function deleteCourse($id)
    {
        $course = TrainingProviderCourse::findOrFail($id);
        $course->delete();
        session()->flash('success', 'Course deleted successfully.');
    }

    // Location Management
    public function saveLocation()
    {
        $this->validate([
            'location_name' => 'required|string|max:255',
            'location_city' => 'required|string|max:255',
            'location_country' => 'required|string|max:255',
        ]);

        if ($this->selectedLocation) {
            $this->selectedLocation->update([
                'name' => $this->location_name,
                'address' => $this->location_address,
                'city' => $this->location_city,
                'country' => $this->location_country,
                'region' => $this->location_region,
            ]);
            session()->flash('success', 'Location updated successfully.');
        } else {
            // Create location for first course (or selected course)
            $course = TrainingProviderCourse::where('provider_id', $this->provider->id)->first();
            if ($course) {
                TrainingCourseLocation::create([
                    'provider_course_id' => $course->id,
                    'name' => $this->location_name,
                    'address' => $this->location_address,
                    'city' => $this->location_city,
                    'country' => $this->location_country,
                    'region' => $this->location_region,
                    'is_primary' => true,
                ]);
                session()->flash('success', 'Location added successfully.');
            } else {
                session()->flash('error', 'Please create a course first before adding locations.');
                return;
            }
        }

        $this->closeLocationModal();
    }

    public function openLocationModal($locationId = null)
    {
        if ($locationId) {
            $this->selectedLocation = TrainingCourseLocation::findOrFail($locationId);
            $this->location_name = $this->selectedLocation->name;
            $this->location_address = $this->selectedLocation->address;
            $this->location_city = $this->selectedLocation->city;
            $this->location_country = $this->selectedLocation->country;
            $this->location_region = $this->selectedLocation->region;
        } else {
            $this->resetLocationForm();
        }
        $this->showLocationModal = true;
    }

    public function closeLocationModal()
    {
        $this->showLocationModal = false;
        $this->resetLocationForm();
    }

    public function resetLocationForm()
    {
        $this->selectedLocation = null;
        $this->location_name = null;
        $this->location_address = null;
        $this->location_city = null;
        $this->location_country = null;
        $this->location_region = null;
    }

    // Schedule Management
    public function saveSchedule()
    {
        $this->validate([
            'schedule_start_date' => 'required|date',
            'schedule_end_date' => 'nullable|date|after_or_equal:schedule_start_date',
            'schedule_available_spots' => 'nullable|integer|min:1',
            'schedule_location_id' => 'required|exists:training_course_locations,id',
        ]);

        // Ensure we have a course ID
        $courseId = $this->selectedCourse ? $this->selectedCourse->id : null;
        if (!$courseId) {
            $courseId = TrainingProviderCourse::where('provider_id', $this->provider->id)->first()?->id;
        }
        
        if (!$courseId) {
            session()->flash('error', 'Please create a course first before adding schedules.');
            return;
        }

        $data = [
            'provider_course_id' => $courseId,
            'location_id' => $this->schedule_location_id,
            'start_date' => $this->schedule_start_date,
            'end_date' => $this->schedule_end_date ?? $this->schedule_start_date,
            'start_time' => $this->schedule_start_time,
            'end_time' => $this->schedule_end_time,
            'available_spots' => $this->schedule_available_spots,
            'is_cancelled' => false,
            'is_full' => false,
        ];

        if ($this->selectedSchedule) {
            $this->selectedSchedule->update($data);
            session()->flash('success', 'Schedule updated successfully.');
        } else {
            TrainingCourseSchedule::create($data);
            session()->flash('success', 'Schedule added successfully.');
        }

        $this->closeScheduleModal();
    }

    public function openScheduleModal($scheduleId = null, $courseId = null)
    {
        if ($courseId) {
            $this->selectedCourse = TrainingProviderCourse::findOrFail($courseId);
        }

        if ($scheduleId) {
            $this->selectedSchedule = TrainingCourseSchedule::findOrFail($scheduleId);
            $this->schedule_start_date = $this->selectedSchedule->start_date->format('Y-m-d');
            $this->schedule_end_date = $this->selectedSchedule->end_date ? $this->selectedSchedule->end_date->format('Y-m-d') : null;
            $this->schedule_start_time = $this->selectedSchedule->start_time;
            $this->schedule_end_time = $this->selectedSchedule->end_time;
            $this->schedule_available_spots = $this->selectedSchedule->available_spots;
            $this->schedule_location_id = $this->selectedSchedule->location_id;
        } else {
            $this->resetScheduleForm();
        }
        $this->showScheduleModal = true;
    }

    public function closeScheduleModal()
    {
        $this->showScheduleModal = false;
        $this->resetScheduleForm();
    }

    public function resetScheduleForm()
    {
        $this->selectedSchedule = null;
        $this->schedule_start_date = null;
        $this->schedule_end_date = null;
        $this->schedule_start_time = null;
        $this->schedule_end_time = null;
        $this->schedule_available_spots = null;
        $this->schedule_location_id = null;
    }

    public function render()
    {
        $courses = TrainingProviderCourse::where('provider_id', $this->provider->id)
            ->with(['certification.category', 'locations', 'schedules'])
            ->paginate(10);

        $certifications = TrainingCertification::where('is_active', true)
            ->orderBy('name')
            ->get();

        $locations = TrainingCourseLocation::whereHas('providerCourse', function ($query) {
            $query->where('provider_id', $this->provider->id);
        })->get();

        return view('livewire.training.provider-portal', [
            'courses' => $courses,
            'certifications' => $certifications,
            'locations' => $locations,
        ])->layout('layouts.app');
    }
}
