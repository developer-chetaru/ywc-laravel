<?php

namespace App\Livewire\Training\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\TrainingCourseReview;
use Illuminate\Support\Facades\Auth;

class ManageReviews extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'all';
    public $ratingFilter = '';

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        if (!Auth::user()->hasRole('super_admin')) {
            abort(403, 'Unauthorized access');
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingRatingFilter()
    {
        $this->resetPage();
    }

    public function approve($id)
    {
        $review = TrainingCourseReview::findOrFail($id);
        $review->update(['is_approved' => true]);
        session()->flash('success', 'Review approved.');
    }

    public function reject($id)
    {
        $review = TrainingCourseReview::findOrFail($id);
        $review->update(['is_approved' => false]);
        session()->flash('success', 'Review rejected.');
    }

    public function delete($id)
    {
        $review = TrainingCourseReview::findOrFail($id);
        $review->delete();
        session()->flash('success', 'Review deleted successfully.');
    }

    public function render()
    {
        $query = TrainingCourseReview::with(['user', 'providerCourse.certification', 'providerCourse.provider']);

        if ($this->search) {
            $query->whereHas('user', function ($q) {
                $q->where('first_name', 'like', '%' . $this->search . '%')
                  ->orWhere('last_name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            })->orWhereHas('providerCourse.certification', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter === 'approved') {
            $query->where('is_approved', true);
        } elseif ($this->statusFilter === 'pending') {
            $query->where('is_approved', false);
        }

        if ($this->ratingFilter) {
            $query->where('rating_overall', $this->ratingFilter);
        }

        $reviews = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('livewire.training.admin.manage-reviews', [
            'reviews' => $reviews,
        ])->layout('layouts.app');
    }
}
