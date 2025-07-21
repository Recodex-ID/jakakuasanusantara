<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceLog extends Model
{
    use HasFactory;
    protected $fillable = [
        'attendance_id',
        'employee_id',
        'location_id',
        'action',
        'action_time',
        'method',
        'latitude',
        'longitude',
        'face_similarity',
        'face_verified',
        'face_api_response',
        'notes',
    ];

    protected $casts = [
        'action_time' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'face_similarity' => 'float',
        'face_verified' => 'boolean',
        'face_api_response' => 'array',
    ];

    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
}
