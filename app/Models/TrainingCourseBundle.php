<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingCourseBundle extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider_id',
        'name',
        'description',
        'course_ids',
        'bundle_price',
        'bundle_discount_percentage',
        'is_active',
    ];

    protected $casts = [
        'course_ids' => 'array',
        'bundle_price' => 'decimal:2',
        'bundle_discount_percentage' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function provider()
    {
        return $this->belongsTo(TrainingProvider::class, 'provider_id');
    }

    // Get courses in bundle
    public function getCourses()
    {
        if (!$this->course_ids) {
            return collect();
        }
        return TrainingProviderCourse::whereIn('id', $this->course_ids)->get();
    }

    // Calculate total savings
    public function getTotalSavingsAttribute()
    {
        $courses = $this->getCourses();
        $totalRegularPrice = $courses->sum('price');
        return $totalRegularPrice - $this->bundle_price;
    }
}
