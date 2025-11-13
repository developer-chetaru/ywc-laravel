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
        'token'
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
}
