<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtherDocument extends Model
{

    // Mass assignable fields
    protected $fillable = [
        'document_id',
        'doc_name',
        'doc_number',
      	'dob',
        'issue_date',
        'expiry_date',
        'file_path',
    ];

    // Optional: cast dates automatically
    protected $casts = [
        'issue_date'  => 'date',
        'expiry_date' => 'date',
    ];


    public function document()
    {
        return $this->belongsTo(Document::class);
    }
}