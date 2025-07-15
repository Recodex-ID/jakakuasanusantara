<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Location;
use App\Services\FaceApiService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    protected FaceApiService $faceApiService;

    public function __construct(FaceApiService $faceApiService)
    {
        $this->faceApiService = $faceApiService;
    }

    public function index()
    {
        $stats = $this->getStats();
        $recentActivity = $this->getRecentActivity();
        $chartData = $this->getChartData();

        return view('admin.dashboard', compact('stats', 'recentActivity', 'chartData'));
    }

    private function getStats(): array
    {
        $today = Carbon::today();
        $totalEmployees = Employee::count();
        $totalLocations = Location::count();
        $todayAttendances = Attendance::whereDate('date', $today)->count();
        
        // Get enrolled faces count
        $enrolledEmployees = $this->getEnrolledFacesCount();
        
        // Today's attendance breakdown
        $todayPresent = Attendance::whereDate('date', $today)
            ->whereNotNull('check_in')
            ->count();
        
        $todayLate = Attendance::whereDate('date', $today)
            ->whereNotNull('check_in')
            ->where('status', 'late')
            ->count();
        
        $todayAbsent = $totalEmployees - $todayPresent;
        
        $attendanceRate = $totalEmployees > 0 ? round(($todayPresent / $totalEmployees) * 100) : 0;
        $faceEnrollmentRate = $totalEmployees > 0 ? round(($enrolledEmployees / $totalEmployees) * 100) : 0;

        return [
            'total_employees' => $totalEmployees,
            'total_locations' => $totalLocations,
            'today_attendance' => $todayAttendances,
            'attendance_rate' => $attendanceRate,
            'enrolled_employees' => $enrolledEmployees,
            'face_enrollment_rate' => $faceEnrollmentRate,
            'today_present' => $todayPresent,
            'today_late' => $todayLate,
            'today_absent' => $todayAbsent,
        ];
    }

    private function getEnrolledFacesCount(): int
    {
        $enrolledCount = 0;
        $employees = Employee::all();
        
        foreach ($employees as $employee) {
            if ($employee->isFaceEnrolled()) {
                $enrolledCount++;
            }
        }
        
        return $enrolledCount;
    }

    private function getRecentActivity()
    {
        return Attendance::with(['employee.user', 'location'])
            ->whereDate('date', Carbon::today())
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($attendance) {
                return [
                    'employee_name' => $attendance->employee->user->name,
                    'action' => $attendance->check_out ? 'Checked out' : 'Checked in',
                    'location' => $attendance->location->name,
                    'method' => 'face_recognition', // Assume face recognition for now
                    'time' => $attendance->updated_at->format('H:i'),
                    'icon_color' => $attendance->check_out ? 'red' : 'green',
                ];
            });
    }

    private function getChartData(): array
    {
        $weeklyTrend = $this->getWeeklyTrend();
        $locationUsage = $this->getLocationUsage();

        return [
            'weekly_trend' => $weeklyTrend,
            'location_usage' => $locationUsage,
        ];
    }

    private function getWeeklyTrend(): array
    {
        $weeklyData = [];
        $totalEmployees = Employee::count();
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $attended = Attendance::whereDate('date', $date)
                ->whereNotNull('check_in')
                ->count();
            
            $weeklyData[] = [
                'day' => $date->format('D'),
                'date' => $date->format('M j'),
                'attended' => $attended,
                'total' => $totalEmployees,
            ];
        }
        
        return $weeklyData;
    }

    private function getLocationUsage()
    {
        return Location::withCount(['attendances' => function ($query) {
            $query->whereBetween('date', [Carbon::today()->subDays(6), Carbon::today()]);
        }])
        ->orderBy('attendances_count', 'desc')
        ->get()
        ->map(function ($location) {
            return (object) [
                'name' => $location->name,
                'count' => $location->attendances_count,
            ];
        });
    }
}