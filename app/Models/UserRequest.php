<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use TeamTeaTime\Forum\Models\Thread;
use App\Models\User;

class UserRequest extends Model
{
    protected $fillable = [
        'user_id',
        'thread_id',
        'message',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function thread(): BelongsTo
    {
        return $this->belongsTo(Thread::class);
    }
}
