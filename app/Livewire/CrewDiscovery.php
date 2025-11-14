<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\UserConnection;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\DB;

class CrewDiscovery extends Component
{
    use WithPagination;

    #[Url]
    public $view = 'list'; // 'list' or 'map'

    #[Url]
    public $radius = 'all'; // 'all' or numeric distance in km

    #[Url]
    public $position = '';

    #[Url]
    public $experience_level = '';

    #[Url]
    public $status = '';

    #[Url]
    public $search = '';

    public $latitude = null;
    public $longitude = null;
    public $location_name = '';

    public $nearbyCrew = [];
    public $onlineCrew = [];
    public $allCrew = []; // All crew with locations for map
    public $selectedUser = null;
    public $showProfileModal = false;
    
    // Connection request modal
    public $showRequestModal = false;
    public $requestUserId = null;
    public $requestMessage = '';
    
    protected function ensureAllCrewInitialized()
    {
        if (!isset($this->allCrew) || !is_array($this->allCrew)) {
            $this->allCrew = [];
        }
    }

    public $alert = '';
    public $error = '';

    protected $queryString = [
        'view' => ['except' => 'list'],
        'radius' => ['except' => 'all'],
        'position' => ['except' => ''],
        'experience_level' => ['except' => ''],
        'status' => ['except' => ''],
        'search' => ['except' => ''],
    ];

    public function mount()
    {
        $this->ensureAllCrewInitialized();
        $user = auth()->user();
        if ($user) {
            $this->latitude = $user->latitude;
            $this->longitude = $user->longitude;
            $this->location_name = $user->location_name;
        }
    }

    public function updated($property)
    {
        if (in_array($property, ['radius', 'position', 'experience_level', 'status', 'search'])) {
            $this->resetPage();
            $this->getAllCrew();
        }
    }

    public function getAllCrew()
    {
        $this->error = '';
        $this->alert = '';

        $currentUserId = auth()->id();
        
        $query = User::where(function ($q) use ($currentUserId) {
                // Include all users with shared locations OR current user (even without share_location enabled)
                $q->where(function ($subQ) {
                    $subQ->where('users.share_location', true)
                        ->where('users.show_in_discovery', true);
                })->orWhere('users.id', $currentUserId);
            })
            ->where(function ($q) {
                // Must have at least latitude OR longitude, or be current user
                $q->where(function ($subQ) {
                    $subQ->whereNotNull('users.latitude')
                        ->whereNotNull('users.longitude');
                });
            })
            ->with('roles');

        // Calculate distance if user has location
        if ($this->latitude && $this->longitude) {
            $query->select('users.*')
                ->selectRaw(
                    '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance',
                    [$this->latitude, $this->longitude, $this->latitude]
                );
        } else {
            $query->select('users.*')
                ->selectRaw('NULL AS distance');
        }

        // Apply visibility filters
        $query->where(function ($q) {
            $q->where('users.visibility', 'everyone')
                ->orWhere(function ($subQ) {
                    $subQ->where('users.visibility', 'connections_only')
                        ->whereExists(function ($existsQuery) {
                            $existsQuery->select(DB::raw(1))
                                ->from('user_connections')
                                ->whereColumn('user_connections.connected_user_id', 'users.id')
                                ->where('user_connections.user_id', auth()->id())
                                ->where('user_connections.status', 'accepted');
                        });
                })
                ->orWhere(function ($subQ) {
                    $subQ->where('users.visibility', 'verified_only')
                        ->where('users.is_active', true);
                });
        });

        // Filter by position/role
        if ($this->position) {
            $query->whereHas('roles', function ($q) {
                $q->where('name', 'like', '%' . $this->position . '%');
            });
        }

        // Filter by experience level
        if ($this->experience_level) {
            $experienceMap = [
                'new' => [0, 2],
                'intermediate' => [2, 5],
                'experienced' => [5, 10],
                'senior' => [10, 999],
            ];
            
            if (isset($experienceMap[$this->experience_level])) {
                [$min, $max] = $experienceMap[$this->experience_level];
                $query->whereBetween('years_experience', [$min, $max]);
            }
        }

        // Filter by status
        if ($this->status === 'online') {
            $query->where('is_online', true);
        } elseif ($this->status === 'available') {
            $query->where(function ($q) {
                $q->where('availability_status', 'available')
                    ->orWhere('looking_to_meet', true);
            });
        } elseif ($this->status === 'looking_for_work') {
            $query->where('looking_for_work', true);
        }

        // Search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('first_name', 'like', '%' . $this->search . '%')
                    ->orWhere('last_name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('location_name', 'like', '%' . $this->search . '%');
            });
        }

        // Apply distance filter if radius is set and user has location
        if ($this->radius !== 'all' && is_numeric($this->radius) && $this->latitude && $this->longitude) {
            $query->having('distance', '<=', $this->radius);
        }

        $crew = $query->when($this->latitude && $this->longitude && $this->radius !== 'all', function ($q) {
                return $q->orderBy('distance');
            }, function ($q) {
                return $q->orderBy('is_online', 'desc')->orderBy('last_seen_at', 'desc');
            })
            ->limit(500)
            ->get()
            ->map(function ($crew) use ($currentUserId) {
                $connection = UserConnection::where(function ($q) use ($crew) {
                    $q->where('user_id', auth()->id())
                        ->where('connected_user_id', $crew->id);
                })->orWhere(function ($q) use ($crew) {
                    $q->where('user_id', $crew->id)
                        ->where('connected_user_id', auth()->id());
                })->first();

                // Get coordinates based on privacy
                $lat = null;
                $lng = null;
                if ($crew->location_privacy === 'exact') {
                    $lat = $crew->latitude;
                    $lng = $crew->longitude;
                } elseif ($crew->location_privacy === 'approximate' && $crew->latitude && $crew->longitude) {
                    // Add small random offset for approximate location
                    $lat = $crew->latitude + (rand(-100, 100) / 10000);
                    $lng = $crew->longitude + (rand(-100, 100) / 10000);
                }

                return [
                    'id' => $crew->id,
                    'name' => $crew->name,
                    'first_name' => $crew->first_name,
                    'last_name' => $crew->last_name,
                    'email' => $crew->email,
                    'profile_photo_url' => $crew->profile_photo_url,
                    'position' => $crew->roles->pluck('name')->first(),
                    'years_experience' => $crew->years_experience,
                    'current_yacht' => $crew->current_yacht,
                    'languages' => $crew->languages ?? [],
                    'nationality' => $crew->nationality,
                    'availability_status' => $crew->availability_status,
                    'availability_message' => $crew->availability_message,
                    'looking_to_meet' => $crew->looking_to_meet,
                    'looking_for_work' => $crew->looking_for_work,
                    'is_online' => $crew->is_online,
                    'last_seen_at' => $crew->last_seen_at,
                    'rating' => $crew->rating,
                    'total_reviews' => $crew->total_reviews,
                    'distance' => $crew->distance ? round($crew->distance, 2) : null,
                    'location_name' => (!empty($crew->location_name) && trim($crew->location_name) !== '') ? $crew->location_name : (($lat && $lng) ? 'Current Location' : ''),
                    'location_updated_at' => $crew->location_updated_at ? ($crew->location_updated_at instanceof \Carbon\Carbon ? $crew->location_updated_at->toIso8601String() : $crew->location_updated_at) : null,
                    'connection_status' => $connection ? $connection->status : null,
                    'is_self' => $crew->id === $currentUserId,
                    'latitude' => $lat,
                    'longitude' => $lng,
                ];
            });

        $this->allCrew = $crew;
        $this->nearbyCrew = $crew;
        
        // Dispatch event to update map
        $this->dispatch('crew-data-updated');
    }

    public function discoverNearby()
    {
        $this->getAllCrew();
    }

    // Public method to refresh crew data (can be called from JavaScript)
    public function refreshCrewData()
    {
        $this->getAllCrew();
        $this->loadOnlineCrew();
    }

    public function loadOnlineCrew()
    {
        $this->onlineCrew = User::where('id', '!=', auth()->id())
            ->where('is_online', true)
            ->where('show_in_discovery', true)
            ->where('share_location', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->with('roles')
            ->limit(20)
            ->get()
            ->map(function ($crew) {
                $distance = $this->latitude && $this->longitude 
                    ? auth()->user()->getDistanceTo($crew->latitude, $crew->longitude)
                    : null;

                return [
                    'id' => $crew->id,
                    'name' => $crew->name,
                    'position' => $crew->roles->pluck('name')->first(),
                    'distance' => $distance ? round($distance, 2) : null,
                    'location_name' => $crew->location_name,
                    'availability_message' => $crew->availability_message,
                    'profile_photo_url' => $crew->profile_photo_url,
                ];
            });
    }

    public function showProfile($userId)
    {
        $this->selectedUser = User::with('roles')->find($userId);
        $this->showProfileModal = true;
    }

    public function closeProfileModal()
    {
        $this->showProfileModal = false;
        $this->selectedUser = null;
    }

    public function sendConnectionRequest($userId)
    {
        try {
            $this->error = '';
            $this->alert = '';

            // Check if connection already exists
            $existing = UserConnection::where(function ($q) use ($userId) {
                $q->where('user_id', auth()->id())
                    ->where('connected_user_id', $userId);
            })->orWhere(function ($q) use ($userId) {
                $q->where('user_id', $userId)
                    ->where('connected_user_id', auth()->id());
            })->first();

            if ($existing) {
                if ($existing->status === 'pending') {
                    $this->alert = 'Connection request already sent';
                } elseif ($existing->status === 'accepted') {
                    $this->alert = 'You are already connected';
                } else {
                    $this->error = 'Connection request was previously declined';
                }
                // Refresh crew data to update connection status
                $this->getAllCrew();
                return;
            }

            // Check if user exists
            $targetUser = User::find($userId);
            if (!$targetUser) {
                $this->error = 'User not found';
                return;
            }

            // Create connection request
            UserConnection::create([
                'user_id' => auth()->id(),
                'connected_user_id' => $userId,
                'status' => 'pending',
                'request_message' => '',
            ]);

            $this->alert = 'Connection request sent successfully!';
            
            // Refresh crew data to update connection status
            $this->getAllCrew();
        } catch (\Exception $e) {
            $this->error = 'Failed to send connection request: ' . $e->getMessage();
            \Log::error('Connection request error: ' . $e->getMessage());
        }
    }

    public function updateMyLocation($latitude, $longitude, $locationName = null)
    {
        try {
            $user = auth()->user();
            if (!$user) {
                $this->error = 'You must be logged in to update your location.';
                return;
            }

            // Ensure location_name is set - use provided name or default to 'Current Location'
            $finalLocationName = !empty($locationName) ? $locationName : 'Current Location';
            
            $user->update([
                'latitude' => $latitude,
                'longitude' => $longitude,
                'location_name' => $finalLocationName,
                'location_updated_at' => now(),
                'share_location' => true,
                'show_in_discovery' => true,
            ]);

            // Update component properties
            $this->latitude = $latitude;
            $this->longitude = $longitude;
            $this->location_name = $finalLocationName;

            $this->alert = 'Location updated successfully!';
            $this->error = '';
            
            // Refresh crew list
            $this->getAllCrew();
        } catch (\Exception $e) {
            $this->error = 'Failed to update location: ' . $e->getMessage();
            \Log::error('Location update error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        // Ensure allCrew is always initialized
        $this->ensureAllCrewInitialized();
        
        $this->getAllCrew();
        $this->loadOnlineCrew();

        return view('livewire.crew-discovery')
            ->layout('layouts.app');
    }
}
