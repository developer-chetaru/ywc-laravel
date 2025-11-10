<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'type',
        'file_path',
        'file_type',
        'file_size',
        'dob',
		'is_preview',
        'issue_date',
        'expiry_date',
        'version',
        'uploaded_by',
        'updated_by',
        'status',
    ];

    /* 🔗 Relationships */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function passportDetail()
    {
        return $this->hasOne(PassportDetail::class);
    }

    public function idvisaDetail()
    {
        return $this->hasOne(IdvisaDetail::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function otherDocument()
    {
        return $this->hasOne(OtherDocument::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
  
  	/* 🔹 Helper functions for status */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}
