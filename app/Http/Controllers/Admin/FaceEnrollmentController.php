<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Services\FaceApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FaceEnrollmentController extends Controller
{
    protected FaceApiService $faceApiService;

    public function __construct(FaceApiService $faceApiService)
    {
        $this->faceApiService = $faceApiService;
    }

    /**
     * Show the face enrollment form for an employee
     */
    public function show(Employee $employee)
    {
        $employee->load(['user', 'location']);

        return view('admin.face-enrollment.show', compact('employee'));
    }

    /**
     * Process face enrollment for an employee
     */
    public function store(Request $request, Employee $employee)
    {
        $request->validate([
            'face_image' => 'required|string',
        ]);

        try {
            $startTime = microtime(true);
            Log::info('Face enrollment started', [
                'employee_id' => $employee->employee_id,
                'timestamp' => now()
            ]);

            $response = $this->faceApiService->enrollEmployeeFace(
                $employee->employee_id,
                $employee->user->name,
                $request->face_image
            );

            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000); // in milliseconds

            if ($response['status'] === '200') {
                // Update employee face_enrolled status
                $employee->update(['face_enrolled' => true]);

                Log::info('Employee face enrolled successfully', [
                    'employee_id' => $employee->employee_id,
                    'employee_name' => $employee->user->name,
                    'gallery_id' => $this->faceApiService->getGalleryId(),
                    'processing_time_ms' => $processingTime
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Face enrolled successfully.',
                    'processing_time_ms' => $processingTime,
                    'data' => $response
                ]);
            } else {
                Log::warning('Face enrollment failed', [
                    'employee_id' => $employee->employee_id,
                    'response' => $response
                ]);

                return response()->json([
                    'success' => false,
                    'message' => $response['status_message'] ?? 'Face enrollment failed.',
                    'data' => $response
                ], 422);
            }
        } catch (\Exception $e) {
            Log::error('Face enrollment exception', [
                'employee_id' => $employee->employee_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred during face enrollment.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check face enrollment status for an employee
     */
    public function status(Employee $employee)
    {
        try {
            // Check if face is enrolled in the API
            $response = $this->faceApiService->listAllFaces();
            
            if ($response['status'] === '200') {
                $faces = $response['faces'] ?? [];
                $isEnrolledInApi = collect($faces)->contains('user_id', $employee->employee_id);
                
                return response()->json([
                    'success' => true,
                    'enrolled_in_api' => $isEnrolledInApi,
                    'enrolled_in_db' => $employee->face_enrolled,
                    'synchronized' => $isEnrolledInApi === $employee->face_enrolled,
                    'gallery_id' => $this->faceApiService->getGalleryId()
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Could not check enrollment status',
                    'error' => $response['status_message'] ?? 'Unknown error'
                ], 422);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error checking enrollment status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete employee's face enrollment
     */
    public function destroy(Employee $employee)
    {
        try {
            $response = $this->faceApiService->deleteEmployeeFace($employee->employee_id);
            
            if ($response['status'] === '200') {
                // Update employee face_enrolled status
                $employee->update(['face_enrolled' => false]);

                Log::info('Employee face deleted successfully', [
                    'employee_id' => $employee->employee_id,
                    'employee_name' => $employee->user->name,
                    'gallery_id' => $this->faceApiService->getGalleryId()
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Face enrollment deleted successfully.'
                ]);
            } elseif ($response['status'] === '415') {
                // Face not found in API, but update database status anyway
                if ($employee->face_enrolled) {
                    $employee->update(['face_enrolled' => false]);
                    Log::info('Employee face status corrected (was incorrectly marked as enrolled)', [
                        'employee_id' => $employee->employee_id
                    ]);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Face enrollment status updated (face was not found in API).'
                ]);
            } else {
                Log::warning('Face deletion failed', [
                    'employee_id' => $employee->employee_id,
                    'response' => $response
                ]);

                return response()->json([
                    'success' => false,
                    'message' => $response['status_message'] ?? 'Failed to delete face enrollment.',
                    'data' => $response
                ], 422);
            }
        } catch (\Exception $e) {
            Log::error('Face deletion exception', [
                'employee_id' => $employee->employee_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting face enrollment.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}