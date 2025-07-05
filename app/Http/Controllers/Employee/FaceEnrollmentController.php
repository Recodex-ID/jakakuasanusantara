<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Rules\ValidBase64Image;
use App\Services\FaceApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FaceEnrollmentController extends Controller
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

        // Check if face is already enrolled
        $faceEnrolled = $employee->isFaceEnrolled();

        return view('employee.face-enrollment.index', compact('employee', 'faceEnrolled'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'face_image' => ['required', new ValidBase64Image()],
        ]);

        $employee = Auth::user()->employee;

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee profile not found.'
            ], 404);
        }

        try {
            // Check if already enrolled
            if ($employee->isFaceEnrolled()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your face is already enrolled in the system.'
                ], 422);
            }

            $response = $this->faceApiService->enrollEmployeeFace(
                $employee->employee_id,
                $employee->user->name,
                $request->face_image
            );

            if (isset($response['status']) && $response['status'] === '200') {
                Log::info('Face enrollment successful', [
                    'employee_id' => $employee->id,
                    'employee_name' => $employee->user->name,
                    'api_response' => $response
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Face enrolled successfully! You can now use face recognition for attendance.',
                    'response' => $response
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Face enrollment failed. Please ensure your face is clearly visible and try again.',
                'api_response' => $response
            ], 422);

        } catch (\Exception $e) {
            Log::error('Face enrollment error', [
                'employee_id' => $employee->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Face enrollment service is currently unavailable. Please try again later.'
            ], 500);
        }
    }

    public function status()
    {
        $employee = Auth::user()->employee;

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee profile not found.'
            ], 404);
        }

        $faceEnrolled = $employee->isFaceEnrolled();

        return response()->json([
            'success' => true,
            'face_enrolled' => $faceEnrolled
        ]);
    }

    public function destroy()
    {
        $employee = Auth::user()->employee;

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee profile not found.'
            ], 404);
        }

        try {
            $response = $this->faceApiService->deleteEmployeeFace($employee->employee_id);

            if (isset($response['status']) && $response['status'] === '200') {
                Log::info('Face deletion successful', [
                    'employee_id' => $employee->id,
                    'employee_name' => $employee->user->name,
                    'api_response' => $response
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Face data deleted successfully.',
                    'response' => $response
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete face data.',
                'api_response' => $response
            ], 422);

        } catch (\Exception $e) {
            Log::error('Face deletion error', [
                'employee_id' => $employee->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Face enrollment service is currently unavailable. Please try again later.'
            ], 500);
        }
    }
}