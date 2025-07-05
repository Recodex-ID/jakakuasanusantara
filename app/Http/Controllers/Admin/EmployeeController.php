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

    public function index(Request $request)
    {
        $query = Employee::with(['user', 'locations']);

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('employee_id', 'like', "%{$search}%")
                    ->orWhere('full_name', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%");
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }

        $employees = $query->paginate(15);

        return view('admin.employees.index', compact('employees'));
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
            'employee_id' => 'required|string|unique:employees',
            'full_name' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'locations' => 'array',
            'locations.*' => 'exists:locations,id',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'role' => 'employee',
            ]);

            $employee = Employee::create([
                'user_id' => $user->id,
                'employee_id' => $request->employee_id,
                'full_name' => $request->full_name,
                'department' => $request->department,
                'position' => $request->position,
                'phone' => $request->phone,
                'address' => $request->address,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
            ]);

            if ($request->has('locations')) {
                $employee->locations()->attach($request->locations);
            }
        });

        return redirect()->route('admin.employees.index')
            ->with('success', 'Employee created successfully.');
    }

    public function show(Employee $employee)
    {
        $employee->load(['user', 'locations']);

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
                ->whereNotNull('check_in')
                ->whereRaw('TIME(check_in) > "09:00:00"')
                ->count(),
        ];

        return view('admin.employees.show', compact('employee', 'recentAttendances', 'attendanceStats'));
    }

    public function edit(Employee $employee)
    {
        $employee->load(['user', 'locations']);
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
            'full_name' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'status' => 'required|in:active,inactive',
            'locations' => 'array',
            'locations.*' => 'exists:locations,id',
        ]);

        DB::transaction(function () use ($request, $employee) {
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'username' => $request->username,
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $employee->user->update($userData);

            $employee->update([
                'employee_id' => $request->employee_id,
                'full_name' => $request->full_name,
                'department' => $request->department,
                'position' => $request->position,
                'phone' => $request->phone,
                'address' => $request->address,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'status' => $request->status,
            ]);

            $employee->locations()->sync($request->locations ?? []);
        });

        return redirect()->route('admin.employees.index')
            ->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee)
    {
        $employee->user->delete();

        return redirect()->route('admin.employees.index')
            ->with('success', 'Employee deleted successfully.');
    }

    public function showEnrollFace(Employee $employee)
    {
        $employee->load(['user', 'locations']);

        return view('admin.employees.enroll-face', compact('employee'));
    }

    public function enrollFace(Request $request, Employee $employee)
    {
        $request->validate([
            'face_image' => 'required|string',
            'gallery_id' => 'required|string',
        ]);

        try {
            $response = $this->faceApiService->enrollFace(
                $employee->full_name,
                $request->gallery_id,
                $request->face_image
            );

            if ($response['status'] === '200') {
                return response()->json([
                    'success' => true,
                    'message' => 'Face enrolled successfully.',
                    'data' => $response
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $response['status_message'] ?? 'Face enrollment failed.',
                    'data' => $response
                ], 422);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during face enrollment.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
