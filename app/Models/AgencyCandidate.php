<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgencyCandidate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'agency_id',
        'candidate_id',
        'status',
        'desired_position',
        'desired_vessel_type',
        'desired_salary_min',
        'desired_salary_max',
        'available_from',
        'notes',
        'tags',
        'priority',
        'added_by',
    ];

    protected $casts = [
        'available_from' => 'date',
        'tags' => 'array',
        'desired_salary_min' => 'decimal:2',
        'desired_salary_max' => 'decimal:2',
    ];

    /**
     * Get the agency (recruitment agency user)
     */
    public function agency()
    {
        return $this->belongsTo(User::class, 'agency_id');
    }

    /**
     * Get the candidate (crew member user)
     */
    public function candidate()
    {
        return $this->belongsTo(User::class, 'candidate_id');
    }

    /**
     * Get the user who added this candidate
     */
    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    /**
     * Check if candidate is available
     */
    public function isAvailable(): bool
    {
        return $this->status === 'active' 
            && (!$this->available_from || $this->available_from->isPast() || $this->available_from->isToday());
    }

    /**
     * Get candidate's documents
     */
    public function getDocumentsAttribute()
    {
        return $this->candidate->documents ?? collect();
    }

    /**
     * Calculate match score with a job posting
     */
    public function matchScore(JobPosting $job): int
    {
        $score = 0;

        // Position match (40 points)
        if (stripos($this->desired_position, $job->position) !== false || 
            stripos($job->position, $this->desired_position) !== false) {
            $score += 40;
        }

        // Vessel type match (20 points)
        if ($this->desired_vessel_type && $job->vessel_type &&
            stripos($this->desired_vessel_type, $job->vessel_type) !== false) {
            $score += 20;
        }

        // Salary match (20 points)
        if ($this->desired_salary_min && $job->salary_max) {
            if ($this->desired_salary_min <= $job->salary_max) {
                $score += 20;
            }
        } else {
            $score += 10; // Partial points if salary not specified
        }

        // Availability match (20 points)
        if ($this->available_from && $job->start_date) {
            if ($this->available_from->lte($job->start_date)) {
                $score += 20;
            }
        } else {
            $score += 10;
        }

        return min($score, 100);
    }
}
