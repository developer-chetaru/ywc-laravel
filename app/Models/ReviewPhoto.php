<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'reviewable_type',
        'reviewable_id',
        'review_id',
        'photo_path',
        'caption',
        'order',
    ];

    public function reviewable()
    {
        return $this->morphTo();
    }
}

