<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceLog;
use App\Models\Employee;
use App\Models\Location;
use App\Rules\ValidBase64Image;
use App\Rules\ValidCoordinates;
use App\Services\FaceApiService;
use App\Services\SecurityService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AttendanceController extends Controller
{
    protected FaceApiService $faceApiService;
    protected SecurityService $securityService;

    public function __construct(FaceApiService $faceApiService, SecurityService $securityService)
    {
        $this->faceApiService = $faceApiService;
        $this->securityService = $securityService;
    }

    public function index()
    {
        $employee = Auth::user()->employee;

        if (!$employee) {
            return redirect()->route('dashboard')
                ->with('error', 'Employee profile not found.');
        }

        // Check if face is enrolled
        $faceEnrolled = $employee->isFaceEnrolled();
        
        if (!$faceEnrolled) {
            return redirect()->route('employee.face-enrollment.index')
                ->with('warning', 'You must register your face before recording attendance.');
        }

        $locations = $employee->location && $employee->location->status === 'active' 
            ? collect([$employee->location]) 
            : collect([]);
        $todayAttendance = $this->getTodayAttendance($employee);

        return view('employee.attendance.index', compact('locations', 'todayAttendance'));
    }

    public function record(Request $request)
    {
        $request->validate([
            'location_id' => 'required|exists:locations,id',
            'action' => 'required|in:check_in,check_out',
            'face_image' => ['required', new ValidBase64Image()],
            'latitude' => ['required', new ValidCoordinates()],
            'longitude' => ['required', new ValidCoordinates()],
        ]);

        // Security validations
        $timeValidation = $this->securityService->validateAttendanceTime();
        if (!$timeValidation['valid']) {
            return response()->json([
                'success' => false,
                'message' => $timeValidation['message']
            ], 422);
        }

        $apiValidation = $this->securityService->validateApiRequest($request);
        if (!$apiValidation['valid']) {
            $this->securityService->logSuspiciousActivity('Invalid API request', $apiValidation['errors']);
            return response()->json([
                'success' => false,
                'message' => 'Invalid request format.'
            ], 422);
        }

        $employee = Auth::user()->employee;
        $location = Location::find($request->location_id);

        // Double check face enrollment before recording
        if (!$employee->isFaceEnrolled()) {
            return response()->json([
                'success' => false,
                'message' => 'You must register your face before recording attendance.'
            ], 403);
        }

        if (!$employee->location || $employee->location->id !== $location->id) {
            $this->securityService->logSuspiciousActivity('Unauthorized location access', [
                'employee_id' => $employee->id,
                'location_id' => $request->location_id,
            ]);
            return response()->json([
                'success' => false,
                'message' => 'You are not assigned to this location.'
            ], 403);
        }

        // Check for consecutive failed attempts
        if ($this->securityService->checkConsecutiveFailedAttempts($employee->id)) {
            return response()->json([
                'success' => false,
                'message' => 'Too many failed face verification attempts. Please contact your administrator.'
            ], 429);
        }

        // Check for suspicious location jumping
        if (
            $this->securityService->detectMultipleLocationAttempts($employee->id, [
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ])
        ) {
            $this->securityService->logSuspiciousActivity('Suspicious location jumping detected', [
                'employee_id' => $employee->id,
                'current_location' => ['lat' => $request->latitude, 'lng' => $request->longitude],
            ]);
        }

        $locationValidation = $this->validateLocation(
            $request->latitude,
            $request->longitude,
            $location
        );

        if (!$locationValidation['valid']) {
            return response()->json([
                'success' => false,
                'message' => "You are {$locationValidation['distance']}m away from the location. Required within {$location->radius_meters}m.",
                'distance' => $locationValidation['distance']
            ], 422);
        }

        try {
            $faceVerification = $this->verifyFace($employee, $request->face_image);

            if (!$faceVerification['success']) {
                return response()->json($faceVerification, 422);
            }

            DB::transaction(function () use ($request, $employee, $location, $faceVerification) {
                $today = Carbon::today();
                $now = Carbon::now();

                // Determine attendance status based on employee's work schedule
                $attendanceStatus = $employee->determineAttendanceStatus($now);
                
                $attendance = Attendance::firstOrCreate(
                    [
                        'employee_id' => $employee->id,
                        'location_id' => $location->id,
                        'date' => $today,
                    ],
                    [
                        'status' => $attendanceStatus,
                    ]
                );

                if ($request->action === 'check_in') {
                    if ($attendance->check_in) {
                        throw new \Exception('You have already checked in today.');
                    }

                    // Update status based on check-in time
                    $checkInStatus = $employee->determineAttendanceStatus($now);
                    
                    $attendance->update([
                        'check_in' => $now,
                        'check_in_lat' => $request->latitude,
                        'check_in_lng' => $request->longitude,
                        'status' => $checkInStatus,
                    ]);
                } else {
                    if (!$attendance->check_in) {
                        throw new \Exception('You must check in first before checking out.');
                    }

                    if ($attendance->check_out) {
                        throw new \Exception('You have already checked out today.');
                    }

                    $attendance->update([
                        'check_out' => $now,
                        'check_out_lat' => $request->latitude,
                        'check_out_lng' => $request->longitude,
                    ]);
                }

                AttendanceLog::create([
                    'attendance_id' => $attendance->id,
                    'employee_id' => $employee->id,
                    'location_id' => $location->id,
                    'action' => $request->action,
                    'action_time' => $now,
                    'method' => 'face_recognition',
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'face_similarity' => $faceVerification['similarity'],
                    'face_verified' => true,
                    'face_api_response' => $faceVerification['api_response'],
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => ucfirst(str_replace('_', ' ', $request->action)) . ' recorded successfully.',
                'face_verification' => $faceVerification,
                'attendance' => $this->getTodayAttendance($employee)
            ]);

        } catch (\Exception $e) {
            Log::error('Attendance recording failed', [
                'employee_id' => $employee->id,
                'location_id' => $location->id,
                'action' => $request->action,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function history(Request $request)
    {
        $employee = Auth::user()->employee;

        $query = Attendance::with(['location'])
            ->where('employee_id', $employee->id);

        if ($request->has('month') && $request->month) {
            $month = Carbon::parse($request->month);
            $query->whereYear('date', $month->year)
                ->whereMonth('date', $month->month);
        } else {
            $query->whereMonth('date', Carbon::now()->month)
                ->whereYear('date', Carbon::now()->year);
        }

        $attendances = $query->orderBy('date', 'desc')->get();

        $stats = [
            'total_days' => $attendances->count(),
            'present_days' => $attendances->whereIn('status', ['present', 'late'])->count(),
            'absent_days' => $attendances->where('status', 'absent')->count(),
            'late_days' => $attendances->where('status', 'late')->count(),
        ];

        // Handle export request
        if ($request->has('export') && $request->export === 'csv') {
            return $this->exportToCsv($attendances, $employee);
        }

        return view('employee.attendance.history', compact('attendances', 'stats'));
    }

    public function show(Attendance $attendance)
    {
        $employee = Auth::user()->employee;

        if ($attendance->employee_id !== $employee->id) {
            abort(403, 'Unauthorized access to attendance record.');
        }

        $attendance->load([
            'location',
            'attendanceLogs' => function ($query) {
                $query->orderBy('action_time');
            }
        ]);

        return view('employee.attendance.show', compact('attendance'));
    }

    private function exportToCsv($attendances, $employee)
    {
        $filename = 'my_attendance_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($attendances, $employee) {
            $file = fopen('php://output', 'w');

            // CSV Headers
            fputcsv($file, [
                'Attendance Report - ' . $employee->user->name
            ]);
            fputcsv($file, []); // Empty row

            fputcsv($file, [
                'Date',
                'Day',
                'Location',
                'Check In',
                'Check Out',
                'Working Hours',
                'Status'
            ]);

            foreach ($attendances as $attendance) {
                $workingHours = '';
                if ($attendance->check_in) {
                    if ($attendance->check_out) {
                        $hours = $attendance->check_in->diffInHours($attendance->check_out, true);
                        $workingHours = sprintf('%.2f hours', $hours);
                    } elseif ($attendance->date->isToday()) {
                        $workingHours = 'Currently working';
                    } else {
                        $workingHours = 'Incomplete';
                    }
                }

                fputcsv($file, [
                    $attendance->date->format('Y-m-d'),
                    $attendance->date->format('l'),
                    $attendance->location->name,
                    $attendance->check_in ? $attendance->check_in->format('H:i:s') : '',
                    $attendance->check_out ? $attendance->check_out->format('H:i:s') : '',
                    $workingHours,
                    ucfirst($attendance->status)
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function getTodayAttendance($employee)
    {
        return Attendance::with(['location'])
            ->where('employee_id', $employee->id)
            ->where('date', Carbon::today())
            ->first();
    }

    private function validateLocation($userLat, $userLng, $location)
    {
        $distance = $this->calculateDistance(
            $userLat,
            $userLng,
            $location->latitude,
            $location->longitude
        );

        return [
            'valid' => $distance <= $location->radius_meters,
            'distance' => round($distance, 2),
            'radius' => $location->radius_meters,
        ];
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
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

    private function verifyFace($employee, $faceImage)
    {
        try {
            // Use employee ID for verification with the configured gallery
            $response = $this->faceApiService->verifyEmployeeFace(
                $employee->employee_id,
                $faceImage
            );

            if ($response['status'] === '200' && isset($response['verified']) && $response['verified']) {
                return [
                    'success' => true,
                    'similarity' => $response['similarity'] ?? 0,
                    'gallery_id' => $this->faceApiService->getGalleryId(),
                    'api_response' => $response
                ];
            }

            return [
                'success' => false,
                'message' => 'Face verification failed. Please ensure your face is clearly visible and try again.',
                'api_response' => $response
            ];

        } catch (\Exception $e) {
            Log::error('Face verification error', [
                'employee_id' => $employee->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Face verification service is currently unavailable. Please try again later.'
            ];
        }
    }
}
