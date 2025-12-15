<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingProviderGallery extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider_id',
        'image_path',
        'category',
        'caption',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function provider()
    {
        return $this->belongsTo(TrainingProvider::class, 'provider_id');
    }
}
