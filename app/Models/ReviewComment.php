<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReviewComment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reviewable_type',
        'reviewable_id',
        'review_id',
        'user_id',
        'parent_id',
        'comment',
        'is_approved',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
    ];

    public function reviewable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(ReviewComment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(ReviewComment::class, 'parent_id');
    }
}

