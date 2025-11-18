<?php

namespace App\Livewire\Itinerary;

use App\Models\ItineraryRoute;
use App\Models\ItineraryRouteComment;
use App\Models\MasterData;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class RouteDiscussion extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    public ItineraryRoute $route;

    public array $form = [
        'body' => '',
        'visibility' => 'crew',
        'stop_id' => null,
        'parent_id' => null,
    ];

    public string $alert = '';

    public int $replyingTo = 0;

    public function mount(ItineraryRoute $route): void
    {
        $this->route = $route->load([
            'stops:id,route_id,name',
            'comments' => fn ($q) => $q->with(['user:id,first_name,last_name,profile_photo_path', 'children.user'])->latest(),
        ]);

        $this->authorize('view', $this->route);
    }

    #[On('discussion-refreshed')]
    public function refresh(): void
    {
        $this->route->load([
            'comments' => fn ($q) => $q->with(['user:id,first_name,last_name,profile_photo_path', 'children.user'])->latest(),
        ]);
        $this->reset(['form', 'replyingTo']);
        $this->resetPage();
    }

    public function startReply(int $commentId): void
    {
        $this->replyingTo = $commentId;
        $this->form['parent_id'] = $commentId;
    }

    public function cancelReply(): void
    {
        $this->replyingTo = 0;
        $this->form['parent_id'] = null;
    }

    public function postComment(): void
    {
        $this->authorize('view', $this->route);
        abort_unless(Auth::check(), 403);

        $data = $this->validate([
            'form.body' => ['required', 'string', 'max:4000'],
            'form.visibility' => ['required', Rule::in(['crew', 'public'])],
            'form.stop_id' => ['nullable', 'exists:itinerary_route_stops,id'],
            'form.parent_id' => ['nullable', 'exists:itinerary_route_comments,id'],
        ])['form'];

        $this->route->comments()->create([
            'user_id' => Auth::id(),
            'stop_id' => $data['stop_id'],
            'parent_id' => $data['parent_id'],
            'visibility' => $data['visibility'],
            'body' => $data['body'],
            'status' => 'active',
        ]);

        $this->alert = 'Comment posted.';
        $this->dispatch('discussion-refreshed');
    }

    public function deleteComment(int $commentId): void
    {
        $comment = ItineraryRouteComment::findOrFail($commentId);
        $this->authorize('view', $this->route);
        abort_unless(Auth::check(), 403);

        if ($comment->user_id !== Auth::id() && !Auth::user()->can('manageCrew', $this->route)) {
            abort(403);
        }

        $comment->delete();

        $this->alert = 'Comment removed.';
        $this->dispatch('discussion-refreshed');
    }

    public function render()
    {
        // Get route visibility options (only crew and public for comments, not private)
        $routeVisibility = MasterData::getRouteVisibility()->filter(function($item) {
            return in_array($item->code, ['crew', 'public']);
        });
        
        return view('livewire.itinerary.route-discussion', [
            'route' => $this->route,
            'stops' => $this->route->stops ?? collect(),
            'comments' => $this->route->comments()
                ->whereNull('parent_id')
                ->with(['user', 'children.user'])
                ->latest()
                ->paginate(10),
            'routeVisibility' => $routeVisibility,
        ]);
    }
}

