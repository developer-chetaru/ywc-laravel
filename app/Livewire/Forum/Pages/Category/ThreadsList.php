<?php

namespace App\Livewire\Forum\Pages\Category;

use Livewire\Component;
use TeamTeaTime\Forum\Models\Thread;
use Illuminate\Support\Facades\Schema;

class ThreadsList extends Component
{
    public string $search = '';
    public string $status = '';

    public function toggleStatus($id)
    {
        $thread = Thread::findOrFail($id);
        
        // Check if status column exists
        if (Schema::hasColumn('forum_threads', 'status')) {
            $thread->status = !$thread->status;
            $thread->save();
        } else {
            // If status doesn't exist, we can use locked as a fallback
            // (locked = inactive, unlocked = active)
            $thread->locked = !$thread->locked;
            $thread->save();
        }
    }

    public function deleteThread($id)
    {
        try {
            $thread = Thread::findOrFail($id);
            
            if (!\Illuminate\Support\Facades\Gate::allows('delete', $thread)) {
                session()->flash('error', 'You do not have permission to delete this thread.');
                return;
            }

            $thread->delete();
            session()->flash('success', 'Thread deleted successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete thread: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $query = Thread::with('category');

        // Apply search filter
        if (!empty($this->search)) {
            $query->where('title', 'like', '%' . $this->search . '%');
        }

        // Apply status filter
        if ($this->status !== '') {
            if (Schema::hasColumn('forum_threads', 'status')) {
                $query->where('status', $this->status);
            } else {
                // Fallback: use locked field (locked = inactive, unlocked = active)
                // status 1 = active = not locked, status 0 = inactive = locked
                if ($this->status == '1') {
                    $query->where('locked', false);
                } elseif ($this->status == '0') {
                    $query->where('locked', true);
                }
            }
        }

        $threads = $query->orderBy('updated_at', 'desc')->get();
        
        // Add computed status attribute if status column doesn't exist
        if (!Schema::hasColumn('forum_threads', 'status')) {
            $threads->each(function ($thread) {
                // Use locked field as fallback: unlocked = active (status = 1), locked = inactive (status = 0)
                $thread->status = !$thread->locked;
            });
        }

        // Calculate active threads count
        if (Schema::hasColumn('forum_threads', 'status')) {
            $activeThreadsCount = Thread::where('status', true)->count();
        } else {
            // If status doesn't exist, count unlocked (active) threads
            $activeThreadsCount = Thread::where('locked', false)->count();
        }

        return view('pages.category.threads-list', [
            'threads' => $threads,
            'activeThreadsCount' => $activeThreadsCount,
        ]);
    }
}
