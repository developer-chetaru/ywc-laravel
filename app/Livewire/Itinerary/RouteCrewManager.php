<?php

namespace App\Livewire\Itinerary;

use App\Models\ItineraryRoute;
use App\Models\ItineraryRouteCrew;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;

class RouteCrewManager extends Component
{
    use AuthorizesRequests;

    public ItineraryRoute $route;

    public array $crew = [];

    public array $form = [
        'email' => '',
        'role' => 'viewer',
    ];

    public string $alert = '';

    public function mount(ItineraryRoute $route): void
    {
        $this->route = $route->load('owner', 'crew.user');
        $this->crew = $this->route->crew->toArray();
        $this->authorize('view', $this->route);
    }

    #[On('crew-updated')]
    public function refresh(): void
    {
        $this->route->load('owner', 'crew.user');
        $this->crew = $this->route->crew->toArray();
        $this->reset('form');
    }

    public function addMember(): void
    {
        $this->authorize('manageCrew', $this->route);

        $data = $this->validate([
            'form.email' => ['required', 'email'],
            'form.role' => ['required', Rule::in(['owner', 'editor', 'viewer'])],
        ])['form'];

        if ($this->route->owner->email === $data['email']) {
            $this->addError('form.email', 'The owner is already part of this crew.');
            return;
        }

        $user = User::where('email', $data['email'])->first();

        $assignment = $this->route->crew()->updateOrCreate(
            [
                'email' => $data['email'],
            ],
            [
                'user_id' => $user?->id,
                'role' => $data['role'],
                'status' => $user ? 'accepted' : 'pending',
                'notify_on_updates' => true,
                'invited_at' => now(),
            ]
        );

        // TODO: trigger email / notification.

        $this->alert = 'Invitation sent.';
        $this->resetErrorBag();
        $this->dispatch('crew-updated');
    }

    public function updateRole(int $crewId, string $role): void
    {
        $this->authorize('manageCrew', $this->route);

        $role = strtolower($role);
        if (!in_array($role, ['owner', 'editor', 'viewer'], true)) {
            return;
        }

        /** @var ItineraryRouteCrew|null $member */
        $member = $this->route->crew()->find($crewId);
        if (!$member) {
            return;
        }

        if ($member->status !== 'accepted' && $role === 'owner') {
            $this->addError('alert', 'Only accepted crew can become owners.');
            return;
        }

        $member->update(['role' => $role]);
        $this->alert = 'Crew role updated.';
        $this->dispatch('crew-updated');
    }

    public function toggleNotifications(int $crewId): void
    {
        $this->authorize('manageCrew', $this->route);

        /** @var ItineraryRouteCrew|null $member */
        $member = $this->route->crew()->find($crewId);
        if (!$member) {
            return;
        }

        $member->update([
            'notify_on_updates' => !$member->notify_on_updates,
        ]);

        $this->alert = 'Notification preference updated.';
        $this->dispatch('crew-updated');
    }

    public function removeMember(int $crewId): void
    {
        $this->authorize('manageCrew', $this->route);

        /** @var ItineraryRouteCrew|null $member */
        $member = $this->route->crew()->find($crewId);
        if (!$member) {
            return;
        }

        $member->delete();
        $this->alert = 'Crew member removed.';
        $this->dispatch('crew-updated');
    }

    public function render()
    {
        return view('livewire.itinerary.route-crew-manager', [
            'route' => $this->route,
            'owner' => $this->route->owner,
            'crewCollection' => $this->route->crew()->with('user')->get(),
        ]);
    }
}

