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

        // Filter by month if provided
        if ($request->has('month') && $request->month) {
            $monthYear = explode('-', $request->month);
            $year = $monthYear[0];
            $month = $monthYear[1];

            $query->whereYear('date', $year)
                  ->whereMonth('date', $month);
        }

        // Filter by employee if provided
        if ($request->has('employee_id') && $request->employee_id) {
            $query->where('employee_id', $request->employee_id);
        }

        $attendances = $query->orderBy('date', 'desc')
            ->orderBy('check_in', 'desc')
            ->paginate(20);

        // Calculate statistics
        $stats = [];
        if (($request->has('month') && $request->month) || ($request->has('employee_id') && $request->employee_id)) {
            $statsQuery = Attendance::query();

            if ($request->has('month') && $request->month) {
                $monthYear = explode('-', $request->month);
                $year = $monthYear[0];
                $month = $monthYear[1];

                $statsQuery->whereYear('date', $year)
                          ->whereMonth('date', $month);
            }

            if ($request->has('employee_id') && $request->employee_id) {
                $statsQuery->where('employee_id', $request->employee_id);
            }

            $filteredAttendances = $statsQuery->get();

            $stats = [
                'total' => $filteredAttendances->count(),
                'present' => $filteredAttendances->where('status', 'present')->count(),
                'late' => $filteredAttendances->where('status', 'late')->count(),
                'absent' => $filteredAttendances->where('status', 'absent')->count(),
            ];
        }

        // Get employees for dropdown
        $employees = Employee::with('user')->where('status', 'active')->get();

        return view('admin.attendances.index', compact('attendances', 'stats', 'employees'));
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
}
