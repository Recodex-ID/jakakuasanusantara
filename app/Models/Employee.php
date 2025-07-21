<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'employee_id',
        'location_id',
        'department',
        'position',
        'phone',
        'address',
        'date_of_birth',
        'gender',
        'status',
        'work_start_time',
        'work_end_time',
        'late_tolerance_minutes',
        'work_days',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'work_days' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function attendanceLogs(): HasMany
    {
        return $this->hasMany(AttendanceLog::class);
    }

    public function isFaceEnrolled(): bool
    {
        try {
            $faceApiService = app(\App\Services\FaceApiService::class);
            $response = $faceApiService->listAllFaces();

            if (isset($response['status']) && $response['status'] === '200' && isset($response['faces'])) {
                $enrolledFaces = array_column($response['faces'], 'user_id');
                return in_array($this->employee_id, $enrolledFaces);
            }
        } catch (\Exception $e) {
            // If face API is unavailable, return false to be safe
        }

        return false;
    }

    // Work schedule helper methods
    public function isWorkDay(\Carbon\Carbon $date): bool
    {
        $dayOfWeek = $date->dayOfWeek; // 0=Sunday, 1=Monday, ..., 6=Saturday
        return in_array((string) $dayOfWeek, $this->work_days ?? []);
    }

    public function isLate(\Carbon\Carbon $checkInTime): bool
    {
        if (!$this->work_start_time) {
            return false;
        }

        $workStart = $this->parseTimeField($this->work_start_time)
            ->setDateFrom($checkInTime);

        $tolerance = $this->late_tolerance_minutes ?? 15;
        $lateThreshold = $workStart->addMinutes($tolerance);

        return $checkInTime->greaterThan($lateThreshold);
    }

    public function determineAttendanceStatus(\Carbon\Carbon $checkInTime): string
    {
        if (!$this->isWorkDay($checkInTime)) {
            return 'weekend';
        }

        if ($this->isLate($checkInTime)) {
            return 'late';
        }

        return 'present';
    }

    public function getWorkingHours(): array
    {
        return [
            'start' => $this->work_start_time ?? '09:00',
            'end' => $this->work_end_time ?? '17:00',
            'tolerance' => $this->late_tolerance_minutes ?? 15,
            'work_days' => $this->work_days ?? ['1', '2', '3', '4', '5']
        ];
    }

    public function getWorkDaysNames(): array
    {
        $days = [
            '0' => 'Sunday',
            '1' => 'Monday',
            '2' => 'Tuesday',
            '3' => 'Wednesday',
            '4' => 'Thursday',
            '5' => 'Friday',
            '6' => 'Saturday'
        ];

        return collect($this->work_days ?? [])
            ->unique()
            ->map(fn($day) => $days[$day] ?? $day)
            ->toArray();
    }

    public function isWithinWorkingHours(\Carbon\Carbon $time): bool
    {
        if (!$this->work_start_time || !$this->work_end_time) {
            return true; // Default to allow if no schedule set
        }

        $workStart = $this->parseTimeField($this->work_start_time)
            ->setDateFrom($time);
        $workEnd = $this->parseTimeField($this->work_end_time)
            ->setDateFrom($time);

        return $time->between($workStart, $workEnd);
    }

    /**
     * Parse time field that could be in H:i or H:i:s format
     */
    private function parseTimeField(string $timeString): \Carbon\Carbon
    {
        // Try H:i:s format first (database format)
        try {
            return \Carbon\Carbon::createFromFormat('H:i:s', $timeString);
        } catch (\InvalidArgumentException $e) {
            // Fallback to H:i format
            try {
                return \Carbon\Carbon::createFromFormat('H:i', $timeString);
            } catch (\InvalidArgumentException $e) {
                // If both fail, use Carbon's general parse
                return \Carbon\Carbon::parse($timeString);
            }
        }
    }
}
