<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingProviderResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'review_id',
        'provider_id',
        'response_text',
    ];

    public function review()
    {
        return $this->belongsTo(TrainingCourseReview::class, 'review_id');
    }

    public function provider()
    {
        return $this->belongsTo(TrainingProvider::class, 'provider_id');
    }
}
