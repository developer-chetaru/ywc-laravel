<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Notifications\CustomResetPasswordNotification;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, HasProfilePhoto, TwoFactorAuthenticatable, HasRoles, HasApiTokens;
	
	protected $guard_name = 'api';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'profile_photo_path',
		'profile_url',
        'qrcode',
        'otp',
        'otp_expires_at',
        'dob',
        'phone',
        'gender',
        'nationality',
        'marital_status',
        'birth_country',
        'birth_province',
        'status',
        'is_active',
        'token',
        // Crew profile fields
        'years_experience',
        'current_yacht',
        'languages',
        'certifications',
        'specializations',
        'interests',
        'availability_status',
        'availability_message',
        'looking_to_meet',
        'looking_for_work',
        'sea_service_time_months',
        'previous_yachts',
        'rating',
        'total_reviews',
        // Location tracking fields
        'latitude',
        'longitude',
        'location_name',
        'location_updated_at',
        'location_privacy',
        'share_location',
        'auto_hide_at_sea',
        'is_online',
        'last_seen_at',
        'visibility',
        'show_in_discovery',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'otp',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'otp_expires_at' => 'datetime',
        'dob' => 'date',
        'password' => 'hashed',
        'location_updated_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'languages' => 'array',
        'certifications' => 'array',
        'specializations' => 'array',
        'interests' => 'array',
        'previous_yachts' => 'array',
        'looking_to_meet' => 'boolean',
        'looking_for_work' => 'boolean',
        'share_location' => 'boolean',
        'auto_hide_at_sea' => 'boolean',
        'is_online' => 'boolean',
        'show_in_discovery' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'rating' => 'decimal:2',
    ];

    protected $appends = ['profile_photo_url'];

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPasswordNotification(
            $token,
            config('app.name'),
        ));
    }



    public function getNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
  
	public function subscriptions()
    {
        return $this->hasMany(\App\Models\Subscription::class);
    }
  
  	public function latestSubscription()
    {
        return $this->hasOne(Subscription::class)->latestOfMany();
    }

    public function activeSubscription()
    {
        return $this->subscriptions()
            ->where('status', 'active')
            ->whereDate('end_date', '>=', now())
            ->latest('end_date')
            ->first();
    }

    public function hasActiveSubscription(): bool
    {
        return $this->activeSubscription() !== null;
    }
  
  	public function documents()
    {
        return $this->hasMany(Document::class);
    }
  
    public function itineraryRoutes()
    {
        return $this->hasMany(ItineraryRoute::class);
    }

    public function crewAssignments()
    {
        return $this->hasMany(ItineraryRouteCrew::class);
    }

    public function routeReviews()
    {
        return $this->hasMany(ItineraryRouteReview::class);
    }

    public function yachtReviews()
    {
        return $this->hasMany(YachtReview::class);
    }

    public function marinaReviews()
    {
        return $this->hasMany(MarinaReview::class);
    }

    public function reviewVotes()
    {
        return $this->hasMany(ReviewVote::class);
    }

    public function reviewComments()
    {
        return $this->hasMany(ReviewComment::class);
    }

    // Crew Discovery & Networking Relationships

    // Connections
    public function connections()
    {
        return $this->hasMany(UserConnection::class, 'user_id');
    }

    public function connectedTo()
    {
        return $this->hasMany(UserConnection::class, 'connected_user_id');
    }

    public function acceptedConnections()
    {
        return $this->connections()->where('status', 'accepted');
    }

    public function pendingConnections()
    {
        return $this->connections()->where('status', 'pending');
    }

    // Endorsements
    public function endorsements()
    {
        return $this->hasMany(UserEndorsement::class);
    }

    public function givenEndorsements()
    {
        return $this->hasMany(UserEndorsement::class, 'endorser_id');
    }

    // Recommendations
    public function recommendations()
    {
        return $this->hasMany(UserRecommendation::class);
    }

    public function givenRecommendations()
    {
        return $this->hasMany(UserRecommendation::class, 'recommender_id');
    }

    // Rallies
    public function organizedRallies()
    {
        return $this->hasMany(Rally::class, 'organizer_id');
    }

    public function rallyAttendances()
    {
        return $this->hasMany(RallyAttendee::class);
    }

    public function rallyComments()
    {
        return $this->hasMany(RallyComment::class);
    }

    // Messages
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function unreadMessages()
    {
        return $this->receivedMessages()->where('is_read', false);
    }

    // Helper methods
    public function updateLocation(float $latitude, float $longitude, ?string $locationName = null): void
    {
        $this->update([
            'latitude' => $latitude,
            'longitude' => $longitude,
            'location_name' => $locationName,
            'location_updated_at' => now(),
        ]);
    }

    public function setOnline(): void
    {
        $this->update([
            'is_online' => true,
            'last_seen_at' => now(),
        ]);
    }

    public function setOffline(): void
    {
        $this->update([
            'is_online' => false,
            'last_seen_at' => now(),
        ]);
    }

    public function getDistanceTo(float $latitude, float $longitude): float
    {
        if (!$this->latitude || !$this->longitude) {
            return 0;
        }

        $earthRadius = 6371; // Earth's radius in kilometers

        $latFrom = deg2rad($this->latitude);
        $lonFrom = deg2rad($this->longitude);
        $latTo = deg2rad($latitude);
        $lonTo = deg2rad($longitude);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos($latFrom) * cos($latTo) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
