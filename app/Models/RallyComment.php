<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RallyComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'rally_id',
        'user_id',
        'parent_id',
        'comment',
        'is_pinned',
        'likes_count',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
    ];

    public function rally(): BelongsTo
    {
        return $this->belongsTo(Rally::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(RallyComment::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(RallyComment::class, 'parent_id');
    }

    public function incrementLikes(): void
    {
        $this->increment('likes_count');
    }
}
