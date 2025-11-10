<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

  	protected $guard_name = 'web';
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'guard_name',
      	'status',
    ];

    /**
     * A role can have many users.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'model_has_roles', 'role_id', 'model_id');
    }
}
