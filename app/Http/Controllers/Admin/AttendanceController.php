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

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::with(['employee.user', 'location']);

        if ($request->has('date_from') && $request->date_from) {
            $query->where('date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->where('date', '<=', $request->date_to);
        }

        if ($request->has('employee_id') && $request->employee_id) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->has('location_id') && $request->location_id) {
            $query->where('location_id', $request->location_id);
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $attendances = $query->orderBy('date', 'desc')
            ->orderBy('check_in', 'desc')
            ->paginate(20);

        $employees = Employee::with('user')
            ->where('status', 'active')
            ->get();
        
        $locations = Location::where('status', 'active')->get();

        return view('admin.attendances.index', compact('attendances', 'employees', 'locations'));
    }

    public function create()
    {
        $employees = Employee::with('user')
            ->where('status', 'active')
            ->get();
        
        $locations = Location::where('status', 'active')->get();

        return view('admin.attendances.create', compact('employees', 'locations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'location_id' => 'required|exists:locations,id',
            'date' => 'required|date',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i|after:check_in',
            'status' => 'required|in:present,absent,late,early_leave',
            'notes' => 'nullable|string',
        ]);

        $existingAttendance = Attendance::where('employee_id', $request->employee_id)
            ->where('location_id', $request->location_id)
            ->where('date', $request->date)
            ->first();

        if ($existingAttendance) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Attendance record already exists for this employee, location, and date.');
        }

        DB::transaction(function () use ($request) {
            $attendance = Attendance::create($request->all());

            if ($request->check_in) {
                AttendanceLog::create([
                    'attendance_id' => $attendance->id,
                    'employee_id' => $request->employee_id,
                    'location_id' => $request->location_id,
                    'action' => 'check_in',
                    'action_time' => Carbon::parse($request->date . ' ' . $request->check_in),
                    'method' => 'manual',
                    'notes' => 'Manual check-in by admin',
                ]);
            }

            if ($request->check_out) {
                AttendanceLog::create([
                    'attendance_id' => $attendance->id,
                    'employee_id' => $request->employee_id,
                    'location_id' => $request->location_id,
                    'action' => 'check_out',
                    'action_time' => Carbon::parse($request->date . ' ' . $request->check_out),
                    'method' => 'manual',
                    'notes' => 'Manual check-out by admin',
                ]);
            }
        });

        return redirect()->route('admin.attendances.index')
            ->with('success', 'Attendance record created successfully.');
    }

    public function show(Attendance $attendance)
    {
        $attendance->load(['employee.user', 'location', 'attendanceLogs' => function ($query) {
            $query->orderBy('action_time');
        }]);

        return view('admin.attendances.show', compact('attendance'));
    }

    public function edit(Attendance $attendance)
    {
        $attendance->load(['employee.user', 'location']);
        $employees = Employee::with('user')
            ->where('status', 'active')
            ->get();
        
        $locations = Location::where('status', 'active')->get();

        return view('admin.attendances.edit', compact('attendance', 'employees', 'locations'));
    }

    public function update(Request $request, Attendance $attendance)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'location_id' => 'required|exists:locations,id',
            'date' => 'required|date',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i|after:check_in',
            'status' => 'required|in:present,absent,late,early_leave',
            'notes' => 'nullable|string',
        ]);

        $existingAttendance = Attendance::where('employee_id', $request->employee_id)
            ->where('location_id', $request->location_id)
            ->where('date', $request->date)
            ->where('id', '!=', $attendance->id)
            ->first();

        if ($existingAttendance) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Attendance record already exists for this employee, location, and date.');
        }

        $attendance->update($request->all());

        return redirect()->route('admin.attendances.index')
            ->with('success', 'Attendance record updated successfully.');
    }

    public function destroy(Attendance $attendance)
    {
        $attendance->delete();

        return redirect()->route('admin.attendances.index')
            ->with('success', 'Attendance record deleted successfully.');
    }

    public function monitor(Request $request)
    {
        $today = Carbon::today();
        
        $todayAttendances = Attendance::with(['employee.user', 'location'])
            ->where('date', $today)
            ->orderBy('check_in', 'desc')
            ->get();

        $recentLogs = AttendanceLog::with(['employee.user', 'location'])
            ->where('action_time', '>=', $today)
            ->orderBy('action_time', 'desc')
            ->take(20)
            ->get();

        $stats = [
            'total_today' => $todayAttendances->count(),
            'present_today' => $todayAttendances->where('status', 'present')->count(),
            'late_today' => $todayAttendances->where('status', 'late')->count(),
            'absent_today' => $todayAttendances->where('status', 'absent')->count(),
        ];

        return view('admin.attendances.monitor', compact('todayAttendances', 'recentLogs', 'stats'));
    }

    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'attendance_ids' => 'required|array',
            'attendance_ids.*' => 'exists:attendances,id',
            'bulk_action' => 'required|in:mark_present,mark_absent,mark_late,delete',
        ]);

        $attendanceIds = $request->attendance_ids;
        $action = $request->bulk_action;

        switch ($action) {
            case 'mark_present':
                Attendance::whereIn('id', $attendanceIds)->update(['status' => 'present']);
                $message = 'Selected attendances marked as present.';
                break;
            
            case 'mark_absent':
                Attendance::whereIn('id', $attendanceIds)->update(['status' => 'absent']);
                $message = 'Selected attendances marked as absent.';
                break;
            
            case 'mark_late':
                Attendance::whereIn('id', $attendanceIds)->update(['status' => 'late']);
                $message = 'Selected attendances marked as late.';
                break;
            
            case 'delete':
                Attendance::whereIn('id', $attendanceIds)->delete();
                $message = 'Selected attendances deleted.';
                break;
        }

        return redirect()->route('admin.attendances.index')
            ->with('success', $message);
    }
}
