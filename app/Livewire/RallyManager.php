<?php

namespace App\Livewire;

use App\Models\Rally;
use App\Models\RallyAttendee;
use App\Models\RallyComment;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class RallyManager extends Component
{
    use WithPagination;

    #[Url]
    public $view = 'discover'; // 'discover', 'create', 'my-rallies'

    #[Url]
    public $type = '';

    #[Url]
    public $search = '';

    public $form = [
        'title' => '',
        'description' => '',
        'type' => 'social',
        'privacy' => 'public',
        'start_date' => '',
        'end_date' => '',
        'location_name' => '',
        'latitude' => null,
        'longitude' => null,
        'address' => '',
        'meeting_point' => '',
        'max_participants' => null,
        'cost' => 0,
        'what_to_bring' => '',
        'requirements' => '',
        'contact_info' => '',
    ];

    public $selectedRally = null;
    public $showRallyModal = false;
    public $rsvpStatus = '';
    public $rsvpComment = '';
    public $rallyComment = '';
    public $showRsvpModal = false;

    public $alert = '';
    public $error = '';

    protected $queryString = [
        'view' => ['except' => 'discover'],
        'type' => ['except' => ''],
        'search' => ['except' => ''],
    ];

    public function mount()
    {
        $user = auth()->user();
        if ($user) {
            $this->form['latitude'] = $user->latitude;
            $this->form['longitude'] = $user->longitude;
            $this->form['location_name'] = $user->location_name;
        }
    }

    public function createRally()
    {
        $this->validate([
            'form.title' => 'required|string|max:255',
            'form.description' => 'required|string',
            'form.type' => 'required|in:social,active,cultural,professional,learning,celebration',
            'form.privacy' => 'required|in:public,private,invite_only',
            'form.start_date' => 'required|date|after:now',
            'form.end_date' => 'nullable|date|after:form.start_date',
            'form.location_name' => 'required|string|max:255',
            'form.latitude' => 'nullable|numeric|between:-90,90',
            'form.longitude' => 'nullable|numeric|between:-180,180',
            'form.max_participants' => 'nullable|integer|min:1',
            'form.cost' => 'nullable|numeric|min:0',
        ]);

        $rally = Rally::create([
            'organizer_id' => auth()->id(),
            'title' => $this->form['title'],
            'description' => $this->form['description'],
            'type' => $this->form['type'],
            'privacy' => $this->form['privacy'],
            'start_date' => $this->form['start_date'],
            'end_date' => $this->form['end_date'],
            'location_name' => $this->form['location_name'],
            'latitude' => $this->form['latitude'],
            'longitude' => $this->form['longitude'],
            'address' => $this->form['address'],
            'meeting_point' => $this->form['meeting_point'],
            'max_participants' => $this->form['max_participants'],
            'cost' => $this->form['cost'] ?? 0,
            'what_to_bring' => $this->form['what_to_bring'],
            'requirements' => $this->form['requirements'],
            'contact_info' => $this->form['contact_info'],
            'status' => 'published',
        ]);

        // Auto-add organizer as going
        RallyAttendee::create([
            'rally_id' => $rally->id,
            'user_id' => auth()->id(),
            'rsvp_status' => 'going',
        ]);

        $this->alert = 'Rally created successfully!';
        $this->reset('form');
        $this->view = 'discover';
    }

    public function showRally($rallyId)
    {
        $this->selectedRally = Rally::with([
            'organizer:id,first_name,last_name,email,profile_photo_path',
            'goingAttendees.user:id,first_name,last_name,email,profile_photo_path',
            'maybeAttendees.user:id,first_name,last_name,email,profile_photo_path',
            'comments.user:id,first_name,last_name,email,profile_photo_path',
        ])->findOrFail($rallyId);

        $this->selectedRally->incrementViews();

        $userRsvp = RallyAttendee::where('rally_id', $rallyId)
            ->where('user_id', auth()->id())
            ->first();

        $this->rsvpStatus = $userRsvp ? $userRsvp->rsvp_status : '';
        $this->rsvpComment = $userRsvp ? $userRsvp->comment : '';
        $this->showRallyModal = true;
    }

    public function closeRallyModal()
    {
        $this->showRallyModal = false;
        $this->selectedRally = null;
        $this->rsvpStatus = '';
        $this->rsvpComment = '';
    }

    public function rsvpToRally()
    {
        if (!$this->selectedRally) {
            return;
        }

        $this->validate([
            'rsvpStatus' => 'required|in:going,maybe,cant_go,interested',
        ]);

        // Check max participants
        if ($this->rsvpStatus === 'going' && $this->selectedRally->max_participants) {
            $goingCount = RallyAttendee::where('rally_id', $this->selectedRally->id)
                ->where('rsvp_status', 'going')
                ->count();
            
            if ($goingCount >= $this->selectedRally->max_participants) {
                $this->error = 'Rally is full';
                return;
            }
        }

        RallyAttendee::updateOrCreate(
            [
                'rally_id' => $this->selectedRally->id,
                'user_id' => auth()->id(),
            ],
            [
                'rsvp_status' => $this->rsvpStatus,
                'comment' => $this->rsvpComment,
            ]
        );

        $this->alert = 'RSVP updated';
        $this->selectedRally->refresh();
        $this->selectedRally->load([
            'goingAttendees.user:id,first_name,last_name,email,profile_photo_path',
            'maybeAttendees.user:id,first_name,last_name,email,profile_photo_path',
        ]);
    }

    public function addComment()
    {
        if (!$this->selectedRally) {
            return;
        }

        $this->validate([
            'rallyComment' => 'required|string|max:1000',
        ]);

        RallyComment::create([
            'rally_id' => $this->selectedRally->id,
            'user_id' => auth()->id(),
            'comment' => $this->rallyComment,
        ]);

        $this->rallyComment = '';
        $this->selectedRally->refresh();
        $this->selectedRally->load('comments.user:id,first_name,last_name,email,profile_photo_path');
    }

    public function render()
    {
        $rallies = collect();
        $myRallies = collect();

        if ($this->view === 'discover') {
            $query = Rally::where('status', 'published')
                ->where('privacy', 'public')
                ->where('start_date', '>=', now())
                ->with(['organizer:id,first_name,last_name,email,profile_photo_path', 'goingAttendees']);

            if ($this->type) {
                $query->where('type', $this->type);
            }

            if ($this->search) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%')
                        ->orWhere('location_name', 'like', '%' . $this->search . '%');
                });
            }

            $rallies = $query->orderBy('start_date')->paginate(12);
        } elseif ($this->view === 'my-rallies') {
            $myRallies = Rally::where('organizer_id', auth()->id())
                ->with(['goingAttendees', 'maybeAttendees'])
                ->latest()
                ->paginate(12);
        }

        return view('livewire.rally-manager', [
            'rallies' => $rallies,
            'myRallies' => $myRallies,
        ])->layout('layouts.app');
    }
}
