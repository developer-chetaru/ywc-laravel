<?php

namespace App\Livewire\Training;

use Livewire\Component;
use App\Models\TrainingProviderCourse;
use App\Models\TrainingCourseReview;
use App\Models\TrainingCourseSchedule;
use Illuminate\Support\Facades\Auth;

class SubmitReview extends Component
{
    public $courseId;
    public $course;
    public $scheduleId;
    
    public $rating_overall = 5;
    public $rating_content = 5;
    public $rating_instructor = 5;
    public $rating_facilities = 5;
    public $rating_value = 5;
    public $rating_administration = 5;
    public $would_recommend = true;
    public $review_text;
    public $liked_most;
    public $areas_for_improvement;
    public $tips_for_students;
    public $date_attended;

    protected $rules = [
        'rating_overall' => 'required|integer|min:1|max:5',
        'rating_content' => 'required|integer|min:1|max:5',
        'rating_instructor' => 'required|integer|min:1|max:5',
        'rating_facilities' => 'required|integer|min:1|max:5',
        'rating_value' => 'required|integer|min:1|max:5',
        'rating_administration' => 'required|integer|min:1|max:5',
        'would_recommend' => 'boolean',
        'review_text' => 'nullable|string|max:2000',
        'liked_most' => 'nullable|string|max:500',
        'areas_for_improvement' => 'nullable|string|max:500',
        'tips_for_students' => 'nullable|string|max:500',
        'date_attended' => 'nullable|date',
    ];

    public function mount($courseId, $scheduleId = null)
    {
        $this->courseId = $courseId;
        $this->scheduleId = $scheduleId;
        $this->course = TrainingProviderCourse::with(['certification', 'provider'])->findOrFail($courseId);
        
        // Check if user already reviewed this course
        $existingReview = TrainingCourseReview::where('user_id', Auth::id())
            ->where('provider_course_id', $courseId)
            ->first();
            
        if ($existingReview) {
            // Load existing review for editing
            $this->rating_overall = $existingReview->rating_overall;
            $this->rating_content = $existingReview->rating_content;
            $this->rating_instructor = $existingReview->rating_instructor;
            $this->rating_facilities = $existingReview->rating_facilities;
            $this->rating_value = $existingReview->rating_value;
            $this->rating_administration = $existingReview->rating_administration;
            $this->would_recommend = $existingReview->would_recommend;
            $this->review_text = $existingReview->review_text;
            $this->liked_most = $existingReview->liked_most;
            $this->areas_for_improvement = $existingReview->areas_for_improvement;
            $this->tips_for_students = $existingReview->tips_for_students;
            $this->date_attended = $existingReview->date_attended ? $existingReview->date_attended->format('Y-m-d') : null;
        }
    }

    public function submit()
    {
        $this->validate();

        $data = [
            'user_id' => Auth::id(),
            'provider_course_id' => $this->courseId,
            'schedule_id' => $this->scheduleId,
            'rating_overall' => $this->rating_overall,
            'rating_content' => $this->rating_content,
            'rating_instructor' => $this->rating_instructor,
            'rating_facilities' => $this->rating_facilities,
            'rating_value' => $this->rating_value,
            'rating_administration' => $this->rating_administration,
            'would_recommend' => $this->would_recommend,
            'review_text' => $this->review_text,
            'liked_most' => $this->liked_most,
            'areas_for_improvement' => $this->areas_for_improvement,
            'tips_for_students' => $this->tips_for_students,
            'date_attended' => $this->date_attended,
            'is_verified_student' => true, // Assuming booking through YWC
        ];

        $existingReview = TrainingCourseReview::where('user_id', Auth::id())
            ->where('provider_course_id', $this->courseId)
            ->first();

        if ($existingReview) {
            $existingReview->update($data);
            session()->flash('success', 'Review updated successfully.');
        } else {
            TrainingCourseReview::create($data);
            session()->flash('success', 'Review submitted successfully. Thank you!');
        }

        return redirect()->route('training.certification.detail', $this->course->certification->slug);
    }

    public function render()
    {
        return view('livewire.training.submit-review')->layout('layouts.app');
    }
}
