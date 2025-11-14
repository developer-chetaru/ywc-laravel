<?php

namespace App\Livewire\Itinerary;

use App\Models\ItineraryRoute;
use App\Models\ItineraryRouteCrew;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\Attributes\Locked;

class RouteCrewManager extends Component
{
    use AuthorizesRequests;

    #[Locked]
    public ItineraryRoute $route;

    public array $crew = [];

    public array $form = [
        'user_id' => '',
        'role' => 'viewer',
    ];

    public string $alert = '';

    public function mount(ItineraryRoute $route): void
    {
        // Ensure route is loaded with relationships and is a model instance
        $this->route = $route->loadMissing(['owner', 'crew.user']);
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
        try {
            // Ensure route is a model instance
            $route = $this->route;
            if ($route instanceof \Illuminate\Database\Eloquent\Collection) {
                $route = $route->first();
            }
            
            if (!$route || !($route instanceof ItineraryRoute)) {
                $this->addError('form.user_id', 'Route not found.');
                return;
            }

            // Only reload if we need fresh data, otherwise use existing instance
            if (!$route->relationLoaded('crew')) {
                $route = ItineraryRoute::with('crew')->find($route->id);
                if (!$route) {
                    $this->addError('form.user_id', 'Route not found.');
                    return;
                }
            }

            $this->authorize('manageCrew', $route);

            // Validate and get validated data
            $validated = $this->validate([
                'form.user_id' => ['required', 'exists:users,id'],
                'form.role' => ['required', Rule::in(['owner', 'editor', 'viewer'])],
            ], [
                'form.user_id.required' => 'Please select a user from the dropdown.',
                'form.user_id.exists' => 'The selected user does not exist.',
                'form.role.required' => 'Please select a role.',
            ]);

            // Extract from validated data - prioritize validated data, then form property
            $user_id = $validated['form']['user_id'] ?? null;
            if (empty($user_id)) {
                // Fallback to form property if validation didn't capture it
                $user_id = $this->form['user_id'] ?? null;
            }
            
            $role = $validated['form']['role'] ?? $this->form['role'] ?? 'viewer';

            // Ensure user_id is a scalar value (string or int)
            if (is_object($user_id)) {
                if (method_exists($user_id, 'id')) {
                    $user_id = $user_id->id;
                } elseif (method_exists($user_id, '__toString')) {
                    $user_id = (string) $user_id;
                } else {
                    $this->addError('form.user_id', 'Invalid user selection. Please select a user from the dropdown.');
                    return;
                }
            }

            // Convert to string first, then to integer to handle both string and int inputs
            $user_id = trim((string) $user_id);
            
            if (empty($user_id) || $user_id === '0' || $user_id === '') {
                $this->addError('form.user_id', 'Please select a user from the dropdown.');
                return;
            }

            // Convert to integer for database query
            $user_id = (int) $user_id;
            
            if ($user_id <= 0) {
                $this->addError('form.user_id', 'Invalid user selection. Please select a valid user.');
                return;
            }

            $user = User::find($user_id);
            if (!$user) {
                $this->addError('form.user_id', 'Selected user not found. Please try selecting again.');
                return;
            }

            // Check if user is the route owner
            if ($route->user_id == $user->id) {
                $this->addError('form.user_id', 'The route owner is already part of this crew.');
                return;
            }

            // Check if user is already in the crew
            $existingCrew = $route->crew()
                ->where(function ($query) use ($user) {
                    $query->where('user_id', $user->id)
                        ->orWhere('email', $user->email);
                })
                ->first();
                
            if ($existingCrew) {
                $this->addError('form.user_id', 'This user is already part of the crew.');
                return;
            }

            // Create the crew member
            $assignment = $route->crew()->create([
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $role,
                'status' => 'accepted',
                'notify_on_updates' => true,
                'invited_at' => now(),
            ]);

            // Refresh route crew relationship without full reload
            $route->load('crew.user');

            // TODO: trigger email / notification.

            $this->alert = 'Crew member added successfully.';
            $this->resetErrorBag();
            $this->reset('form');
            
            // Update crew array directly for faster UI update
            $this->crew = $route->crew->toArray();
            
            $this->dispatch('crew-updated');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Re-throw validation exceptions so Livewire can handle them
            throw $e;
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->addError('form.user_id', 'You do not have permission to add crew members.');
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle database constraint violations
            if (str_contains($e->getMessage(), 'Duplicate entry') || $e->getCode() == '23000') {
                $this->addError('form.user_id', 'This user is already part of the crew.');
            } else {
                \Log::error('Database error adding crew member', [
                    'error' => $e->getMessage(),
                    'route_id' => $route->id ?? null,
                    'user_id' => $user_id ?? null,
                ]);
                $this->addError('form.user_id', 'An error occurred while adding the crew member. Please try again.');
            }
        } catch (\Exception $e) {
            \Log::error('Error adding crew member', [
                'error' => $e->getMessage(),
                'route_id' => $route->id ?? null,
                'user_id' => $user_id ?? null,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->addError('form.user_id', 'An error occurred: ' . $e->getMessage());
        }
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
        // Get all users - we'll handle exclusions in validation
        // This allows users to see all available users and search properly
        $users = User::select('id', 'first_name', 'last_name', 'email')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        // Ensure owner is loaded properly
        $owner = $this->route->owner;
        if (!$owner) {
            $owner = User::find($this->route->user_id);
        }

        return view('livewire.itinerary.route-crew-manager', [
            'route' => $this->route,
            'owner' => $owner,
            'crewCollection' => $this->route->crew()->with('user')->get(),
            'users' => $users,
        ]);
    }
}

