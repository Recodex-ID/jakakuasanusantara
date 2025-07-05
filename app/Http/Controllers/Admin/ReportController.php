<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceLog;
use App\Models\Employee;
use App\Models\Location;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }

    public function attendance(Request $request)
    {
        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'employee_ids' => 'array',
            'employee_ids.*' => 'exists:employees,id',
            'location_ids' => 'array',
            'location_ids.*' => 'exists:locations,id',
            'export_format' => 'in:html,excel,pdf',
        ]);

        $query = Attendance::with(['employee.user', 'location'])
            ->whereBetween('date', [$request->date_from, $request->date_to]);

        if ($request->has('employee_ids') && !empty($request->employee_ids)) {
            $query->whereIn('employee_id', $request->employee_ids);
        }

        if ($request->has('location_ids') && !empty($request->location_ids)) {
            $query->whereIn('location_id', $request->location_ids);
        }

        $attendances = $query->orderBy('date', 'desc')
            ->orderBy('employee_id')
            ->get();

        $stats = [
            'total_records' => $attendances->count(),
            'present_count' => $attendances->where('status', 'present')->count(),
            'absent_count' => $attendances->where('status', 'absent')->count(),
            'late_count' => $attendances->where('status', 'late')->count(),
            'early_leave_count' => $attendances->where('status', 'early_leave')->count(),
        ];

        $employees = Employee::with('user')->where('status', 'active')->get();
        $locations = Location::where('status', 'active')->get();

        if ($request->export_format === 'excel') {
            return $this->exportToExcel($attendances, $request);
        } elseif ($request->export_format === 'pdf') {
            return $this->exportToPdf($attendances, $request);
        }

        return view('admin.reports.attendance', compact(
            'attendances',
            'stats',
            'employees',
            'locations',
            'request'
        ));
    }

    public function summary(Request $request)
    {
        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'group_by' => 'in:employee,location,date',
        ]);

        $groupBy = $request->get('group_by', 'employee');
        
        $query = Attendance::with(['employee.user', 'location'])
            ->whereBetween('date', [$request->date_from, $request->date_to]);

        switch ($groupBy) {
            case 'employee':
                $summary = $this->getSummaryByEmployee($query);
                break;
            case 'location':
                $summary = $this->getSummaryByLocation($query);
                break;
            case 'date':
                $summary = $this->getSummaryByDate($query);
                break;
        }

        return view('admin.reports.summary', compact('summary', 'groupBy', 'request'));
    }

    public function analytics(Request $request)
    {
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

        $attendanceData = $this->getAttendanceChartData($startDate, $endDate, $period);
        $locationData = $this->getLocationAttendanceData($startDate, $endDate);
        $statusData = $this->getStatusDistributionData($startDate, $endDate);

        return view('admin.reports.analytics', compact(
            'attendanceData',
            'locationData',
            'statusData',
            'period',
            'startDate',
            'endDate'
        ));
    }

    private function getSummaryByEmployee($query)
    {
        return $query->get()
            ->groupBy('employee_id')
            ->map(function ($attendances) {
                $employee = $attendances->first()->employee;
                return [
                    'employee' => $employee,
                    'total' => $attendances->count(),
                    'present' => $attendances->where('status', 'present')->count(),
                    'absent' => $attendances->where('status', 'absent')->count(),
                    'late' => $attendances->where('status', 'late')->count(),
                    'early_leave' => $attendances->where('status', 'early_leave')->count(),
                    'attendance_rate' => round(($attendances->whereIn('status', ['present', 'late'])->count() / $attendances->count()) * 100, 2),
                ];
            });
    }

    private function getSummaryByLocation($query)
    {
        return $query->get()
            ->groupBy('location_id')
            ->map(function ($attendances) {
                $location = $attendances->first()->location;
                return [
                    'location' => $location,
                    'total' => $attendances->count(),
                    'present' => $attendances->where('status', 'present')->count(),
                    'absent' => $attendances->where('status', 'absent')->count(),
                    'late' => $attendances->where('status', 'late')->count(),
                    'early_leave' => $attendances->where('status', 'early_leave')->count(),
                    'attendance_rate' => round(($attendances->whereIn('status', ['present', 'late'])->count() / $attendances->count()) * 100, 2),
                ];
            });
    }

    private function getSummaryByDate($query)
    {
        return $query->get()
            ->groupBy('date')
            ->map(function ($attendances, $date) {
                return [
                    'date' => $date,
                    'total' => $attendances->count(),
                    'present' => $attendances->where('status', 'present')->count(),
                    'absent' => $attendances->where('status', 'absent')->count(),
                    'late' => $attendances->where('status', 'late')->count(),
                    'early_leave' => $attendances->where('status', 'early_leave')->count(),
                    'attendance_rate' => round(($attendances->whereIn('status', ['present', 'late'])->count() / $attendances->count()) * 100, 2),
                ];
            })
            ->sortByDesc('date');
    }

    private function getAttendanceChartData($startDate, $endDate, $period)
    {
        $format = $period === 'year' ? '%Y-%m' : '%Y-%m-%d';
        
        return Attendance::select(
                DB::raw("DATE_FORMAT(date, '$format') as period"),
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN status IN ('present', 'late') THEN 1 ELSE 0 END) as present"),
                DB::raw("SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent")
            )
            ->whereBetween('date', [$startDate, $endDate])
            ->groupBy('period')
            ->orderBy('period')
            ->get();
    }

    private function getLocationAttendanceData($startDate, $endDate)
    {
        return Attendance::select(
                'locations.name',
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN status IN ('present', 'late') THEN 1 ELSE 0 END) as present")
            )
            ->join('locations', 'attendances.location_id', '=', 'locations.id')
            ->whereBetween('date', [$startDate, $endDate])
            ->groupBy('locations.id', 'locations.name')
            ->get();
    }

    private function getStatusDistributionData($startDate, $endDate)
    {
        return Attendance::select(
                'status',
                DB::raw('COUNT(*) as count')
            )
            ->whereBetween('date', [$startDate, $endDate])
            ->groupBy('status')
            ->get();
    }

    private function exportToExcel($attendances, $request)
    {
        // Implementation would require a package like Laravel Excel
        // For now, return a simple CSV response
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="attendance_report.csv"',
        ];

        $callback = function () use ($attendances) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, [
                'Date',
                'Employee ID',
                'Employee Name',
                'Location',
                'Check In',
                'Check Out',
                'Status',
                'Notes'
            ]);

            foreach ($attendances as $attendance) {
                fputcsv($file, [
                    $attendance->date->format('Y-m-d'),
                    $attendance->employee->employee_id,
                    $attendance->employee->user->name,
                    $attendance->location->name,
                    $attendance->check_in?->format('H:i'),
                    $attendance->check_out?->format('H:i'),
                    ucfirst($attendance->status),
                    $attendance->notes,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportToPdf($attendances, $request)
    {
        // Implementation would require a package like DomPDF or similar
        // For now, return HTML that can be printed as PDF
        
        return view('admin.reports.attendance-pdf', compact('attendances', 'request'));
    }
}
