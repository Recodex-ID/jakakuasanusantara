<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceLog;
use App\Models\Employee;
use App\Models\FaceGallery;
use App\Models\Location;
use App\Services\FaceApiService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AttendanceController extends Controller
{
    protected FaceApiService $faceApiService;

    public function __construct(FaceApiService $faceApiService)
    {
        $this->faceApiService = $faceApiService;
    }

    public function index()
    {
        $employee = Auth::user()->employee;
        
        if (!$employee) {
            return redirect()->route('dashboard')
                ->with('error', 'Employee profile not found.');
        }

        $locations = $employee->locations()->where('status', 'active')->get();
        $todayAttendance = $this->getTodayAttendance($employee);

        return view('employee.attendance.index', compact('locations', 'todayAttendance'));
    }

    public function record(Request $request)
    {
        $request->validate([
            'location_id' => 'required|exists:locations,id',
            'action' => 'required|in:check_in,check_out',
            'face_image' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $employee = Auth::user()->employee;
        $location = Location::find($request->location_id);

        if (!$employee->locations->contains($location)) {
            return response()->json([
                'success' => false,
                'message' => 'You are not assigned to this location.'
            ], 403);
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

                $attendance = Attendance::firstOrCreate(
                    [
                        'employee_id' => $employee->id,
                        'location_id' => $location->id,
                        'date' => $today,
                    ],
                    [
                        'status' => 'present',
                    ]
                );

                if ($request->action === 'check_in') {
                    if ($attendance->check_in) {
                        throw new \Exception('You have already checked in today.');
                    }
                    
                    $attendance->update([
                        'check_in' => $now,
                        'check_in_lat' => $request->latitude,
                        'check_in_lng' => $request->longitude,
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

        return view('employee.attendance.history', compact('attendances', 'stats'));
    }

    public function show(Attendance $attendance)
    {
        $employee = Auth::user()->employee;

        if ($attendance->employee_id !== $employee->id) {
            abort(403, 'Unauthorized access to attendance record.');
        }

        $attendance->load(['location', 'attendanceLogs' => function ($query) {
            $query->orderBy('action_time');
        }]);

        return view('employee.attendance.show', compact('attendance'));
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
            $availableGalleries = FaceGallery::whereHas('location', function ($query) use ($employee) {
                $query->whereIn('id', $employee->locations->pluck('id'));
            })->where('status', 'active')->get();

            if ($availableGalleries->isEmpty()) {
                return [
                    'success' => false,
                    'message' => 'No face galleries available for your assigned locations.'
                ];
            }

            foreach ($availableGalleries as $gallery) {
                $response = $this->faceApiService->verifyFace(
                    $employee->nik,
                    $gallery->gallery_id,
                    $faceImage
                );

                if ($response['status'] === '200' && isset($response['verified']) && $response['verified']) {
                    return [
                        'success' => true,
                        'similarity' => $response['similarity'] ?? 0,
                        'gallery_id' => $gallery->gallery_id,
                        'api_response' => $response
                    ];
                }
            }

            return [
                'success' => false,
                'message' => 'Face verification failed. Please ensure your face is clearly visible and try again.'
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
