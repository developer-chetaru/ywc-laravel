<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PassportDetail extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'document_id',
        'passport_number',
      	'dob',
        'issue_date',
        'expiry_date',
        'nationality',
        'country_code',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }
}
