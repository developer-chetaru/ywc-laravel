<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CertificateType extends Model
{
    use HasFactory;
    use SoftDeletes;

     protected $fillable = [
        'name',
        'is_active',
    ];
  
  	public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    // Many-to-many with issuers through pivot table
    public function issuers()
    {
        return $this->belongsToMany(
            CertificateIssuer::class,
            'certificate_type_issuer',
            'certificate_type_id',
            'certificate_issuer_id'
        );
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($certificateType) {
            $certificateType->issuers()->detach();
        });
    }
  
}