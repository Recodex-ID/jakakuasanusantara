<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Location;
use App\Models\User;
use App\Services\FaceApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    protected FaceApiService $faceApiService;

    public function __construct(FaceApiService $faceApiService)
    {
        $this->faceApiService = $faceApiService;
    }

    public function index()
    {
        $employees = Employee::with(['user', 'location'])->paginate(15);

        // Check face enrollment status for each employee
        $enrolledFaces = $this->getEnrolledFaces();

        foreach ($employees as $employee) {
            $employee->face_enrolled = in_array($employee->employee_id, $enrolledFaces);
        }

        return view('admin.employees.index', compact('employees'));
    }

    private function getEnrolledFaces(): array
    {
        try {
            $response = $this->faceApiService->listAllFaces();

            if (isset($response['status']) && $response['status'] === '200' && isset($response['faces'])) {
                return array_column($response['faces'], 'user_id');
            }
        } catch (\Exception $e) {
            // If face API is unavailable, return empty array
        }

        return [];
    }

    public function create()
    {
        $locations = Location::where('status', 'active')->get();
        return view('admin.employees.create', compact('locations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8',
            'department' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'role' => 'required|in:employee,admin',
            'location_id' => 'nullable|exists:locations,id',
            'work_start_time' => 'nullable|date_format:H:i',
            'work_end_time' => 'nullable|date_format:H:i|after:work_start_time',
            'late_tolerance_minutes' => 'nullable|integer|min:0|max:60',
            'work_days' => 'nullable|array',
            'work_days.*' => 'in:0,1,2,3,4,5,6',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'role' => $request->role,
            ]);

            $employee = Employee::create([
                'user_id' => $user->id,
                'employee_id' => $this->generateEmployeeId($request->role),
                'location_id' => $request->location_id,
                'department' => $request->department,
                'position' => $request->position,
                'phone' => $request->phone,
                'address' => $request->address,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'work_start_time' => $request->work_start_time ?: '09:00',
                'work_end_time' => $request->work_end_time ?: '17:00',
                'late_tolerance_minutes' => $request->late_tolerance_minutes ?: 15,
                'work_days' => $request->work_days ?: ['1', '2', '3', '4', '5'],
            ]);

        });

        return redirect()->route('admin.employees.index')
            ->with('success', 'Employee created successfully.');
    }

    public function show(Employee $employee)
    {
        $employee->load(['user', 'location']);

        // Get recent attendances
        $recentAttendances = $employee->attendances()
            ->with('location')
            ->latest()
            ->take(5)
            ->get();

        // Get attendance statistics
        $attendanceStats = [
            'this_month' => $employee->attendances()
                ->whereMonth('date', now()->month)
                ->whereYear('date', now()->year)
                ->count(),
            'total_present' => $employee->attendances()
                ->whereNotNull('check_in')
                ->count(),
            'total_absent' => $employee->attendances()
                ->whereNull('check_in')
                ->count(),
            'total_late' => $employee->attendances()
                ->where('status', 'late')
                ->count(),
        ];

        return view('admin.employees.show', compact('employee', 'recentAttendances', 'attendanceStats'));
    }

    public function edit(Employee $employee)
    {
        $employee->load(['user', 'location']);
        $locations = Location::where('status', 'active')->get();

        return view('admin.employees.edit', compact('employee', 'locations'));
    }

    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($employee->user_id)],
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($employee->user_id)],
            'password' => 'nullable|string|min:8',
            'employee_id' => ['required', 'string', Rule::unique('employees')->ignore($employee->id)],
            'department' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'role' => 'required|in:employee,admin',
            'status' => 'required|in:active,inactive',
            'location_id' => 'nullable|exists:locations,id',
            'work_start_time' => 'nullable|date_format:H:i',
            'work_end_time' => 'nullable|date_format:H:i|after:work_start_time',
            'late_tolerance_minutes' => 'nullable|integer|min:0|max:60',
            'work_days' => 'nullable|array',
            'work_days.*' => 'in:0,1,2,3,4,5,6',
        ]);

        DB::transaction(function () use ($request, $employee) {
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'username' => $request->username,
                'role' => $request->role,
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $employee->user->update($userData);

            $employee->update([
                'employee_id' => $request->employee_id,
                'location_id' => $request->location_id,
                'department' => $request->department,
                'position' => $request->position,
                'phone' => $request->phone,
                'address' => $request->address,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'status' => $request->status,
                'work_start_time' => $request->work_start_time,
                'work_end_time' => $request->work_end_time,
                'late_tolerance_minutes' => $request->late_tolerance_minutes,
                'work_days' => $request->work_days ?: [],
            ]);

        });

        return redirect()->route('admin.employees.index')
            ->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee)
    {
        DB::transaction(function () use ($employee) {
            // Delete face from face recognition system
            try {
                $this->faceApiService->deleteEmployeeFace($employee->employee_id);
            } catch (\Exception) {
                // Continue with deletion even if face API fails
            }

            // Delete user (will cascade delete employee)
            $employee->user->delete();
        });

        return redirect()->route('admin.employees.index')
            ->with('success', 'Employee deleted successfully.');
    }

    private function generateEmployeeId(string $role): string
    {
        $prefix = $role === 'admin' ? 'ADM' : 'EMP';
        $year = now()->format('Y');
        
        $lastEmployee = Employee::join('users', 'employees.user_id', '=', 'users.id')
            ->where('users.role', $role)
            ->whereYear('employees.created_at', $year)
            ->orderBy('employees.created_at', 'desc')
            ->first();

        if ($lastEmployee) {
            $lastNumber = (int) substr($lastEmployee->employee_id, -4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix . $year . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

}
