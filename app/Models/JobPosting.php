<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobPosting extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'posted_by',
        'title',
        'description',
        'position',
        'vessel_name',
        'vessel_type',
        'vessel_flag',
        'location',
        'salary_min',
        'salary_max',
        'contract_duration',
        'required_certificates',
        'required_skills',
        'additional_requirements',
        'start_date',
        'application_deadline',
        'status',
        'views_count',
        'applications_count',
    ];

    protected $casts = [
        'start_date' => 'date',
        'application_deadline' => 'date',
        'required_certificates' => 'array',
        'required_skills' => 'array',
        'salary_min' => 'decimal:2',
        'salary_max' => 'decimal:2',
    ];

    /**
     * Get the user who posted this job
     */
    public function postedBy()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    /**
     * Check if job is still open for applications
     */
    public function isOpen(): bool
    {
        return $this->status === 'open' 
            && (!$this->application_deadline || $this->application_deadline->isFuture());
    }

    /**
     * Check if application deadline is approaching (within 7 days)
     */
    public function deadlineApproaching(): bool
    {
        return $this->application_deadline 
            && $this->application_deadline->isFuture() 
            && $this->application_deadline->lte(now()->addDays(7));
    }

    /**
     * Increment views count
     */
    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    /**
     * Get salary range as formatted string
     */
    public function getSalaryRangeAttribute(): string
    {
        if (!$this->salary_min && !$this->salary_max) {
            return 'Negotiable';
        }

        if ($this->salary_min && $this->salary_max) {
            return '$' . number_format($this->salary_min) . ' - $' . number_format($this->salary_max);
        }

        if ($this->salary_min) {
            return 'From $' . number_format($this->salary_min);
        }

        return 'Up to $' . number_format($this->salary_max);
    }

    /**
     * Scope for open jobs only
     */
    public function scopeOpen($query)
    {
        return $query->where('status', 'open')
            ->where(function($q) {
                $q->whereNull('application_deadline')
                  ->orWhere('application_deadline', '>', now());
            });
    }

    /**
     * Scope for jobs posted by user
     */
    public function scopePostedByUser($query, $userId)
    {
        return $query->where('posted_by', $userId);
    }
}
