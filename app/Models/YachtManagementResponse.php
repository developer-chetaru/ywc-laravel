<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YachtManagementResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'yacht_id',
        'yacht_review_id',
        'user_id',
        'response',
        'is_approved',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
    ];

    public function yacht()
    {
        return $this->belongsTo(Yacht::class);
    }

    public function yachtReview()
    {
        return $this->belongsTo(YachtReview::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

