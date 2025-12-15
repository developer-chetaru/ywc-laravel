<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TrainingUserCertification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'certification_id',
        'provider_course_id',
        'issue_date',
        'expiry_date',
        'status',
        'certificate_number',
        'issuing_authority',
        'certificate_document_path',
        'notes',
        'is_auto_tracked',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'is_auto_tracked' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function ($certification) {
            // Auto-update status based on expiry date
            if ($certification->expiry_date) {
                $now = Carbon::now();
                $expiry = Carbon::parse($certification->expiry_date);
                $daysUntilExpiry = $now->diffInDays($expiry, false);

                if ($daysUntilExpiry < 0) {
                    $certification->status = 'expired';
                } elseif ($daysUntilExpiry <= 30) {
                    $certification->status = 'expiring_soon';
                } else {
                    $certification->status = 'valid';
                }
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function certification()
    {
        return $this->belongsTo(TrainingCertification::class, 'certification_id');
    }

    public function providerCourse()
    {
        return $this->belongsTo(TrainingProviderCourse::class, 'provider_course_id');
    }

    public function reminders()
    {
        return $this->hasMany(TrainingCertificationReminder::class, 'user_certification_id');
    }

    // Check if certification is expiring soon (within 3 months)
    public function isExpiringSoon()
    {
        if (!$this->expiry_date) {
            return false;
        }
        return Carbon::parse($this->expiry_date)->diffInMonths(now()) <= 3;
    }

    // Check if certification is expired
    public function isExpired()
    {
        if (!$this->expiry_date) {
            return false;
        }
        return Carbon::parse($this->expiry_date)->isPast();
    }
}
