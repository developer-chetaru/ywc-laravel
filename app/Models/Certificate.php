<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{

    protected $fillable = [
        'document_id',
        'certificate_type_id',
        'certificate_issuer_id',
        'certificate_number',
        'dob',
        'issue_date',
        'expiry_date',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function type()
    {
        return $this->belongsTo(CertificateType::class, 'certificate_type_id');
    }

    public function issuer()
    {
        return $this->belongsTo(CertificateIssuer::class, 'certificate_issuer_id');
    }
}
