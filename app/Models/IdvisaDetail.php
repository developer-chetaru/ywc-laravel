<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IdvisaDetail extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'document_id',
        'document_name',
        'document_number',
        'issue_country',
        'country_code',
        'visa_type',
        'place_of_issue',
        'dob',
        'issue_date',
        'expiry_date',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }
}
