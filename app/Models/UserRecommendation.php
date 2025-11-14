<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserRecommendation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'recommender_id',
        'recommendation',
        'position',
        'yacht_name',
        'duration_months',
        'work_start_date',
        'work_end_date',
        'would_work_again',
    ];

    protected $casts = [
        'work_start_date' => 'date',
        'work_end_date' => 'date',
        'would_work_again' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function recommender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recommender_id');
    }
}
