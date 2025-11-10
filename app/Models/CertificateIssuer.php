<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CertificateIssuer extends Model
{
    use HasFactory;
    use SoftDeletes;

     protected $fillable = [
        'name',
        'is_active',
    ];
  
  
  	public function certificates()
    {
        return $this->hasMany(Certificate::class, 'certificate_issuer_id');
    }
  
  	// Many-to-many with certificate types
    public function certificateTypes()
    {
        return $this->belongsToMany(
            CertificateType::class,
            'certificate_type_issuer',
            'certificate_issuer_id',
            'certificate_type_id'
        );
    }
}