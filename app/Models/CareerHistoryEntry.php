<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class CareerHistoryEntry extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'vessel_name',
        'position_title',
        'role',
        'vessel_type',
        'vessel_flag',
        'vessel_length_meters',
        'gross_tonnage',
        'start_date',
        'end_date',
        'employment_type',
        'position_rank',
        'department',
        'employer_company',
        'supervisor_name',
        'supervisor_contact',
        'key_duties',
        'notable_achievements',
        'departure_reason',
        'reference_document_id',
        'contract_document_id',
        'signoff_document_id',
        'visible_on_profile',
        'display_order',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'vessel_length_meters' => 'decimal:2',
        'visible_on_profile' => 'boolean',
        'display_order' => 'integer',
    ];

    /**
     * Get the user who owns this career entry
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the reference document
     */
    public function referenceDocument(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'reference_document_id');
    }

    /**
     * Get the contract document
     */
    public function contractDocument(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'contract_document_id');
    }

    /**
     * Get the sign-off document
     */
    public function signoffDocument(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'signoff_document_id');
    }

    /**
     * Check if this is a current position
     */
    public function isCurrentPosition(): bool
    {
        return $this->end_date === null;
    }

    /**
     * Calculate duration in months
     */
    public function getDurationInMonths(): int
    {
        $endDate = $this->end_date ?? Carbon::today();
        return $this->start_date->diffInMonths($endDate);
    }

    /**
     * Get formatted duration string (e.g., "2 years 3 months")
     */
    public function getFormattedDuration(): string
    {
        $endDate = $this->end_date ?? Carbon::today();
        
        // Calculate total months first
        $totalMonths = $this->start_date->diffInMonths($endDate);
        
        // Calculate years and remaining months (ensure integers)
        $years = floor($totalMonths / 12);
        $months = $totalMonths % 12;

        $parts = [];
        if ($years > 0) {
            $parts[] = (int)$years . ' ' . ($years === 1 ? 'year' : 'years');
        }
        if ($months > 0) {
            $parts[] = (int)$months . ' ' . ($months === 1 ? 'month' : 'months');
        }

        return $parts ? implode(' ', $parts) : 'Less than 1 month';
    }

    /**
     * Calculate sea service days for this entry
     * Sea service is calculated as the number of days between start and end date
     */
    public function getSeaServiceDays(): int
    {
        $endDate = $this->end_date ?? Carbon::today();
        return $this->start_date->diffInDays($endDate) + 1; // +1 to include both start and end days
    }

    /**
     * Check if this entry qualifies for sea service
     * Some positions may not count (e.g., shore-based)
     */
    public function qualifiesForSeaService(): bool
    {
        // All vessel positions qualify for sea service
        return !empty($this->vessel_name) && !empty($this->position_title);
    }
}
