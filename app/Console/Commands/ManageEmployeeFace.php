<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Services\FaceApiService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ManageEmployeeFace extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'face:manage 
                            {action : Action to perform: list, delete, verify}
                            {employee? : Employee ID (required for delete and verify)}
                            {--image= : Path to image file (required for verify)}
                            {--force : Force action without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage individual employee faces (list, delete, verify)';

    protected FaceApiService $faceApiService;

    /**
     * Create a new command instance.
     */
    public function __construct(FaceApiService $faceApiService)
    {
        parent::__construct();
        $this->faceApiService = $faceApiService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');
        
        switch ($action) {
            case 'list':
                return $this->listFaces();
            case 'delete':
                return $this->deleteFace();
            case 'verify':
                return $this->verifyFace();
            default:
                $this->error("Invalid action: {$action}");
                $this->info('Available actions: list, delete, verify');
                return 1;
        }
    }

    /**
     * List all enrolled faces
     */
    private function listFaces(): int
    {
        $this->info("👥 Enrolled Faces in Gallery: " . $this->faceApiService->getGalleryId());
        $this->newLine();

        try {
            $response = $this->faceApiService->listAllFaces();
            
            if ($response['status'] !== '200') {
                $this->error('Failed to fetch faces: ' . ($response['status_message'] ?? 'Unknown error'));
                return 1;
            }

            $faces = $response['faces'] ?? [];
            
            if (empty($faces)) {
                $this->warn('No faces enrolled yet.');
                $this->info('💡 Start enrolling faces through the admin panel or use face enrollment features.');
                return 0;
            }

            // Get employee data for cross-reference
            $employees = Employee::pluck('face_enrolled', 'employee_id')->toArray();

            $headers = ['User ID', 'User Name', 'Quality', 'DB Status', 'Actions'];
            $rows = [];
            
            foreach ($faces as $face) {
                $userId = $face['user_id'] ?? 'Unknown';
                $userName = $face['user_name'] ?? 'Unknown';
                $quality = isset($face['quality']) ? number_format($face['quality'] * 100, 1) . '%' : 'N/A';
                
                // Check database status
                $dbStatus = isset($employees[$userId]) && $employees[$userId] ? '✅ Marked' : '❌ Not marked';
                
                $actions = 'verify, delete';
                
                $rows[] = [$userId, $userName, $quality, $dbStatus, $actions];
            }
            
            $this->table($headers, $rows);
            $this->info("Total: " . count($faces) . " faces enrolled");
            
            // Show usage examples
            $this->newLine();
            $this->info('💡 Usage examples:');
            $this->info('  • Delete face: php artisan face:manage delete EMPLOYEE_ID');
            $this->info('  • Verify face: php artisan face:manage verify EMPLOYEE_ID --image=/path/to/photo.jpg');

            return 0;
            
        } catch (\Exception $e) {
            $this->error('Error fetching faces: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Delete an employee's face
     */
    private function deleteFace(): int
    {
        $employeeId = $this->argument('employee');
        
        if (!$employeeId) {
            $this->error('Employee ID is required for delete action');
            $this->info('Usage: php artisan face:manage delete EMPLOYEE_ID');
            return 1;
        }

        // Find employee in database
        $employee = Employee::where('employee_id', $employeeId)->first();
        
        if (!$employee) {
            $this->error("Employee not found: {$employeeId}");
            return 1;
        }

        $this->info("🗑️  Deleting face for: {$employee->user->name} ({$employeeId})");
        
        // Confirm action
        if (!$this->option('force')) {
            if (!$this->confirm('Are you sure you want to delete this employee\'s face data?')) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        try {
            $response = $this->faceApiService->deleteEmployeeFace($employeeId);
            
            if ($response['status'] === '200') {
                $this->success("✅ Face deleted successfully for {$employee->user->name}");
                
                // Update database status
                $employee->update(['face_enrolled' => false]);
                $this->info('📝 Database status updated');
                
                Log::info('Employee face deleted via command', [
                    'employee_id' => $employeeId,
                    'employee_name' => $employee->user->name,
                    'gallery_id' => $this->faceApiService->getGalleryId()
                ]);
                
                return 0;
                
            } elseif ($response['status'] === '415') {
                $this->warn("⚠️  Face not found in API for {$employee->user->name}");
                
                // Update database status anyway
                if ($employee->face_enrolled) {
                    $employee->update(['face_enrolled' => false]);
                    $this->info('📝 Database status updated (was incorrectly marked as enrolled)');
                }
                
                return 0;
                
            } else {
                $this->error('Failed to delete face: ' . ($response['status_message'] ?? 'Unknown error'));
                $this->error('Status: ' . $response['status']);
                return 1;
            }
            
        } catch (\Exception $e) {
            $this->error('Error deleting face: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Verify an employee's face
     */
    private function verifyFace(): int
    {
        $employeeId = $this->argument('employee');
        $imagePath = $this->option('image');
        
        if (!$employeeId) {
            $this->error('Employee ID is required for verify action');
            $this->info('Usage: php artisan face:manage verify EMPLOYEE_ID --image=/path/to/photo.jpg');
            return 1;
        }

        if (!$imagePath) {
            $this->error('Image path is required for verify action');
            $this->info('Usage: php artisan face:manage verify EMPLOYEE_ID --image=/path/to/photo.jpg');
            return 1;
        }

        if (!file_exists($imagePath)) {
            $this->error("Image file not found: {$imagePath}");
            return 1;
        }

        // Find employee in database
        $employee = Employee::where('employee_id', $employeeId)->first();
        
        if (!$employee) {
            $this->error("Employee not found: {$employeeId}");
            return 1;
        }

        $this->info("🔍 Verifying face for: {$employee->user->name} ({$employeeId})");
        $this->info("Image: {$imagePath}");
        $this->newLine();

        try {
            // Encode image
            $this->info('🔄 Processing image...');
            $base64Image = $this->faceApiService->encodeImageToBase64($imagePath);
            
            if (!$this->faceApiService->validateBase64Image($base64Image)) {
                $this->error('❌ Invalid image format. Please use JPG or PNG.');
                return 1;
            }

            // Verify face
            $this->info('🔄 Verifying with Face API...');
            $response = $this->faceApiService->verifyEmployeeFace($employeeId, $base64Image);
            
            $this->newLine();
            $this->info('📊 Verification Results:');
            $this->info("Status: " . $response['status']);
            $this->info("Message: " . ($response['status_message'] ?? 'No message'));
            
            if ($response['status'] === '200') {
                $verified = $response['verified'] ?? false;
                $similarity = $response['similarity'] ?? 0;
                $threshold = config('services.biznet_face.similarity_threshold');
                
                $this->info("Similarity: " . number_format($similarity * 100, 2) . "%");
                $this->info("Threshold: " . number_format($threshold * 100, 2) . "%");
                $this->info("Result: " . ($verified ? '✅ VERIFIED' : '❌ NOT VERIFIED'));
                
                if (isset($response['masker'])) {
                    $this->info("Mask detected: " . ($response['masker'] ? 'Yes' : 'No'));
                }
                
                if ($verified) {
                    $this->success("🎉 Face verification successful!");
                } else {
                    $this->warn("⚠️  Face verification failed - similarity below threshold");
                }
                
                return 0;
                
            } elseif ($response['status'] === '415') {
                $this->error("❌ Employee face not enrolled in system");
                $this->info("💡 Enroll the employee's face first through the admin panel");
                return 1;
                
            } elseif ($response['status'] === '412') {
                $this->error("❌ No face detected in image");
                $this->info("💡 Please use a clear image with a visible face");
                return 1;
                
            } elseif ($response['status'] === '413') {
                $this->error("❌ Face too small in image");
                $this->info("💡 Please use a higher resolution image or closer face shot");
                return 1;
                
            } else {
                $this->error("❌ Verification failed: " . ($response['status_message'] ?? 'Unknown error'));
                return 1;
            }
            
        } catch (\Exception $e) {
            $this->error('Error during verification: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Write a success message to the console.
     */
    private function success(string $message): void
    {
        $this->line("<fg=green>{$message}</>");
    }
}