<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attendance extends Model
{
    protected $fillable = [
        'employee_id',
        'location_id',
        'date',
        'check_in',
        'check_out',
        'status',
        'notes',
        'check_in_lat',
        'check_in_lng',
        'check_out_lat',
        'check_out_lng',
    ];

    protected $casts = [
        'date' => 'date',
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'check_in_lat' => 'decimal:8',
        'check_in_lng' => 'decimal:8',
        'check_out_lat' => 'decimal:8',
        'check_out_lng' => 'decimal:8',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function attendanceLogs(): HasMany
    {
        return $this->hasMany(AttendanceLog::class);
    }
}
