<?php

namespace App\Livewire\WorkLog;

use Livewire\Component;

class WorkLogIndex extends Component
{
    public function render()
    {
        return view('livewire.work-log.work-log-index')->layout('layouts.app');
    }
}


// <?php

// namespace App\Livewire\WorkLog;

// use Livewire\Component;
// use App\Models\WorkLog;

// class WorkLogIndex extends Component
// {
//     public $dateFilter = '';
//     public $search = '';

//     public function render()
//     {
//         $logs = WorkLog::query()
//             ->when($this->dateFilter, fn($q) =>
//                 $q->whereDate('work_date', $this->dateFilter)
//             )
//             ->when($this->search, fn($q) =>
//                 $q->where('task', 'like', '%' . $this->search . '%')
//             )
//             ->orderBy('work_date', 'desc')
//             ->get();

//         return view('livewire.work-log.work-log-index', [
//             'logs' => $logs,
//         ]);
//     }
// }