<?php

namespace App\Services;

use App\Models\AttendanceLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SecurityService
{
    public function logSuspiciousActivity(string $activity, array $data = [], ?int $userId = null): void
    {
        Log::warning('Suspicious activity detected', [
            'activity' => $activity,
            'user_id' => $userId ?? auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now(),
            'data' => $data,
        ]);
    }

    public function validateImageIntegrity(string $base64Image): bool
    {
        try {
            $decoded = base64_decode($base64Image, true);
            
            if ($decoded === false) {
                return false;
            }

            $imageInfo = getimagesizefromstring($decoded);
            
            if ($imageInfo === false) {
                return false;
            }

            $allowedTypes = [IMAGETYPE_JPEG, IMAGETYPE_PNG];
            if (!in_array($imageInfo[2], $allowedTypes)) {
                return false;
            }

            $maxSize = 5 * 1024 * 1024; // 5MB
            if (strlen($decoded) > $maxSize) {
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Image validation failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);
            return false;
        }
    }

    public function detectMultipleLocationAttempts(int $employeeId, array $currentLocation): bool
    {
        $recentLogs = AttendanceLog::where('employee_id', $employeeId)
            ->where('action_time', '>=', now()->subMinutes(30))
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->orderBy('action_time', 'desc')
            ->take(5)
            ->get();

        if ($recentLogs->count() < 2) {
            return false;
        }

        $suspiciousDistances = 0;
        $maxReasonableDistance = 1000; // 1km in 30 minutes

        foreach ($recentLogs as $log) {
            $distance = $this->calculateDistance(
                $currentLocation['latitude'],
                $currentLocation['longitude'],
                $log->latitude,
                $log->longitude
            );

            if ($distance > $maxReasonableDistance) {
                $suspiciousDistances++;
            }
        }

        return $suspiciousDistances >= 2;
    }

    public function validateAttendanceTime(): array
    {
        $now = now();
        $hour = $now->hour;
        $isWorkingHours = $hour >= 6 && $hour <= 22; // 6 AM to 10 PM

        if (!$isWorkingHours) {
            return [
                'valid' => false,
                'message' => 'Attendance recording is only allowed during working hours (6 AM - 10 PM).',
                'current_time' => $now->format('H:i'),
            ];
        }

        return ['valid' => true];
    }

    public function sanitizeUserInput(array $input): array
    {
        $sanitized = [];
        
        foreach ($input as $key => $value) {
            if (is_string($value)) {
                $sanitized[$key] = strip_tags(trim($value));
            } else {
                $sanitized[$key] = $value;
            }
        }

        return $sanitized;
    }

    public function checkConsecutiveFailedAttempts(int $employeeId): bool
    {
        $failedAttempts = AttendanceLog::where('employee_id', $employeeId)
            ->where('face_verified', false)
            ->where('action_time', '>=', now()->subHour())
            ->count();

        return $failedAttempts >= 5;
    }

    public function encryptSensitiveData(string $data): string
    {
        return encrypt($data);
    }

    public function decryptSensitiveData(string $encryptedData): string
    {
        return decrypt($encryptedData);
    }

    public function hashLocationCoordinates(float $latitude, float $longitude): string
    {
        return hash('sha256', $latitude . '|' . $longitude);
    }

    public function validateApiRequest(Request $request): array
    {
        $errors = [];

        if (!$request->hasHeader('User-Agent')) {
            $errors[] = 'Missing User-Agent header';
        }

        if (!$request->hasHeader('Accept')) {
            $errors[] = 'Missing Accept header';
        }

        $ipAddress = $request->ip();
        if (in_array($ipAddress, ['127.0.0.1', '::1']) && app()->environment('production')) {
            $errors[] = 'Local requests not allowed in production';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    private function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000; // meters

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}