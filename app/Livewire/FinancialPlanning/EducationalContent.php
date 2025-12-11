<?php

namespace App\Livewire\FinancialPlanning;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\FinancialEducationalContent;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class EducationalContent extends Component
{
    use WithPagination;

    public $filterType = 'all';
    public $selectedContent = null;

    public function mount()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
    }

    public function viewContent($contentId)
    {
        $this->selectedContent = FinancialEducationalContent::where('id', $contentId)
            ->where('is_published', true)
            ->firstOrFail();
    }

    public function closeContent()
    {
        $this->selectedContent = null;
    }

    public function render()
    {
        $query = FinancialEducationalContent::where('is_published', true);

        if ($this->filterType !== 'all') {
            $query->where('type', $this->filterType);
        }

        $content = $query->orderBy('order')->orderBy('created_at', 'desc')->paginate(12);

        return view('livewire.financial-planning.educational-content', [
            'content' => $content,
        ]);
    }
}

