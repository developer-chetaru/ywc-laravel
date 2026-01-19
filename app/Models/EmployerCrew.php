<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployerCrew extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'employer_crew';

    protected $fillable = [
        'employer_id',
        'crew_id',
        'position',
        'vessel_name',
        'vessel_imo',
        'contract_start_date',
        'contract_end_date',
        'status',
        'notes',
        'added_by',
    ];

    protected $casts = [
        'contract_start_date' => 'date',
        'contract_end_date' => 'date',
    ];

    /**
     * Get the employer (user with employer role)
     */
    public function employer()
    {
        return $this->belongsTo(User::class, 'employer_id');
    }

    /**
     * Get the crew member (user)
     */
    public function crew()
    {
        return $this->belongsTo(User::class, 'crew_id');
    }

    /**
     * Get the user who added this relationship
     */
    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    /**
     * Check if contract is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active' 
            && (!$this->contract_end_date || $this->contract_end_date->isFuture());
    }

    /**
     * Check if contract is expired
     */
    public function isExpired(): bool
    {
        return $this->contract_end_date && $this->contract_end_date->isPast();
    }

    /**
     * Get contract duration in days
     */
    public function getContractDurationAttribute(): ?int
    {
        if (!$this->contract_start_date || !$this->contract_end_date) {
            return null;
        }

        return $this->contract_start_date->diffInDays($this->contract_end_date);
    }

    /**
     * Get days remaining in contract
     */
    public function getDaysRemainingAttribute(): ?int
    {
        if (!$this->contract_end_date || $this->contract_end_date->isPast()) {
            return 0;
        }

        return now()->diffInDays($this->contract_end_date);
    }
}
