<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FaceGallery extends Model
{
    protected $fillable = [
        'gallery_id',
        'name',
        'description',
        'location_id',
        'status',
    ];

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
}
