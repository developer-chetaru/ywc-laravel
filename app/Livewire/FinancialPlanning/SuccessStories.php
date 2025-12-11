<?php

namespace App\Livewire\FinancialPlanning;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\FinancialSuccessStory;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class SuccessStories extends Component
{
    use WithPagination;

    public $filterStrategy = 'all';
    public $selectedStory = null;

    public function viewStory($storyId)
    {
        $this->selectedStory = FinancialSuccessStory::where('id', $storyId)
            ->where('is_published', true)
            ->firstOrFail();
    }

    public function closeStory()
    {
        $this->selectedStory = null;
    }

    public function render()
    {
        $query = FinancialSuccessStory::where('is_published', true);

        if ($this->filterStrategy !== 'all') {
            $query->where('strategy_type', $this->filterStrategy);
        }

        $stories = $query->orderBy('is_featured', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(9);

        $strategies = [
            'all' => 'All Strategies',
            'early_starter' => 'Early Starter',
            'late_starter' => 'Late Starter',
            'property_investor' => 'Property Investor',
            'aggressive_saver' => 'Aggressive Saver',
            'other' => 'Other',
        ];

        return view('livewire.financial-planning.success-stories', [
            'stories' => $stories,
            'strategies' => $strategies,
        ]);
    }
}

