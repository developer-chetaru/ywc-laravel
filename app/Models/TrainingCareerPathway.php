<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TrainingCareerPathway extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'starting_position',
        'target_position',
        'description',
        'certification_sequence',
        'estimated_timeline_months',
        'estimated_total_cost',
        'career_benefits',
        'specialized_tracks',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'certification_sequence' => 'array',
        'specialized_tracks' => 'array',
        'is_active' => 'boolean',
        'estimated_timeline_months' => 'integer',
        'estimated_total_cost' => 'decimal:2',
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function ($pathway) {
            if (empty($pathway->slug)) {
                $pathway->slug = Str::slug($pathway->name);
            }
        });
    }

    // Get certifications in sequence
    public function getCertifications()
    {
        if (!$this->certification_sequence) {
            return collect();
        }
        return TrainingCertification::whereIn('id', $this->certification_sequence)
            ->orderByRaw('FIELD(id, ' . implode(',', $this->certification_sequence) . ')')
            ->get();
    }
}
