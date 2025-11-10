<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertificateTypeIssuer extends Model
{
    use HasFactory;

    protected $table = 'certificate_type_issuer';
    public $timestamps = true;
    
    protected $fillable = [
        'certificate_type_id',
        'certificate_issuer_id',
    ];

    // Optional: if you want relationships on pivot
    public function certificateType()
    {
        return $this->belongsTo(CertificateType::class, 'certificate_type_id');
    }

    public function certificateIssuer()
    {
        return $this->belongsTo(CertificateIssuer::class, 'certificate_issuer_id');
    }
}
