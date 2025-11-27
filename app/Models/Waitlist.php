<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Waitlist extends Model
{
    protected $fillable = [
        'email',
        'first_name',
        'last_name',
        'role',
        'status',
        'notes',
        'approved_at',
        'invited_at',
        'source',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'invited_at' => 'datetime',
    ];
}
