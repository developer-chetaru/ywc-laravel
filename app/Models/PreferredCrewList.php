<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PreferredCrewList extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'crew_user_id',
        'yacht_id',
        'times_worked_together',
        'captain_rating',
        'notes',
        'is_favorite',
        'notify_when_available',
        'priority_access',
        'first_hired_at',
        'last_hired_at',
        'last_worked_at',
    ];

    protected $casts = [
        'is_favorite' => 'boolean',
        'notify_when_available' => 'boolean',
        'priority_access' => 'boolean',
        'first_hired_at' => 'datetime',
        'last_hired_at' => 'datetime',
        'last_worked_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class); // Captain
    }

    public function crew()
    {
        return $this->belongsTo(User::class, 'crew_user_id');
    }

    public function yacht()
    {
        return $this->belongsTo(Yacht::class);
    }
}
