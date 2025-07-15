<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $employee = Auth::user()->employee;
        
        if (!$employee) {
            return redirect()->route('dashboard')
                ->with('error', 'Employee profile not found.');
        }

        $employee->load(['user', 'location']);

        $todayAttendance = $this->getTodayAttendance($employee);
        $monthlyStats = $this->getThisMonthStats($employee);
        $recentAttendances = $this->getRecentAttendances($employee);
        $assignedLocation = $employee->location && $employee->location->status === 'active' ? $employee->location : null;
        
        // Check if face is enrolled
        $faceEnrolled = $employee->isFaceEnrolled();

        return view('employee.dashboard', compact(
            'employee',
            'todayAttendance',
            'monthlyStats',
            'recentAttendances',
            'assignedLocation',
            'faceEnrolled'
        ));
    }

    public function attendanceStats(Request $request)
    {
        $employee = Auth::user()->employee;
        $period = $request->get('period', 'month');

        switch ($period) {
            case 'week':
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
                break;
            case 'month':
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;
            case 'year':
                $startDate = Carbon::now()->startOfYear();
                $endDate = Carbon::now()->endOfYear();
                break;
            default:
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
        }

        $attendances = Attendance::where('employee_id', $employee->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $stats = [
            'period' => $period,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'total_days' => $attendances->count(),
            'present_days' => $attendances->whereIn('status', ['present', 'late'])->count(),
            'absent_days' => $attendances->where('status', 'absent')->count(),
            'late_days' => $attendances->where('status', 'late')->count(),
            'early_leave_days' => $attendances->where('status', 'early_leave')->count(),
            'attendance_rate' => $attendances->count() > 0 
                ? round(($attendances->whereIn('status', ['present', 'late'])->count() / $attendances->count()) * 100, 2)
                : 0,
        ];

        return response()->json($stats);
    }

    private function getTodayAttendance($employee)
    {
        return Attendance::with(['location'])
            ->where('employee_id', $employee->id)
            ->where('date', Carbon::today())
            ->first();
    }

    private function getThisMonthStats($employee)
    {
        $thisMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $attendances = Attendance::where('employee_id', $employee->id)
            ->whereBetween('date', [$thisMonth, $endOfMonth])
            ->get();

        $workingDays = $this->calculateWorkingDays($thisMonth, $endOfMonth);

        // Calculate expected working days based on employee's schedule
        $expectedWorkingDays = $this->calculateExpectedWorkingDays($employee, $thisMonth, $endOfMonth);
        
        return [
            'total_days' => $attendances->count(),
            'present' => $attendances->whereIn('status', ['present', 'late'])->count(),
            'absent' => $attendances->where('status', 'absent')->count(),
            'late' => $attendances->where('status', 'late')->count(),
            'expected_days' => $expectedWorkingDays,
            'attendance_rate' => $expectedWorkingDays > 0 
                ? round(($attendances->whereIn('status', ['present', 'late'])->count() / $expectedWorkingDays) * 100, 2) . '%'
                : '0%',
        ];
    }

    private function getRecentAttendances($employee)
    {
        return Attendance::with(['location'])
            ->where('employee_id', $employee->id)
            ->orderBy('date', 'desc')
            ->take(10)
            ->get();
    }

    private function calculateWorkingDays($startDate, $endDate)
    {
        $workingDays = 0;
        $current = $startDate->copy();

        while ($current->lte($endDate)) {
            // Skip weekends (Saturday = 6, Sunday = 0)
            if ($current->dayOfWeek !== 0 && $current->dayOfWeek !== 6) {
                $workingDays++;
            }
            $current->addDay();
        }

        return $workingDays;
    }

    private function calculateExpectedWorkingDays($employee, $startDate, $endDate)
    {
        $workingDays = 0;
        $current = $startDate->copy();
        $employeeWorkDays = $employee->work_days ?? ['1', '2', '3', '4', '5'];

        while ($current->lte($endDate)) {
            // Check if current day is in employee's work days
            if (in_array((string)$current->dayOfWeek, $employeeWorkDays)) {
                $workingDays++;
            }
            $current->addDay();
        }

        return $workingDays;
    }
}
