<?php

namespace App\Console\Commands;

use App\Services\FaceApiService;
use Illuminate\Console\Command;

class TestFaceApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'face:test 
                            {--full : Run full test including image upload}
                            {--image= : Path to test image file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Face API connection and functionality';

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
        $this->info("🧪 Face API Connection Test");
        $this->info("API URL: " . config('services.biznet_face.url'));
        $this->info("Gallery ID: " . $this->faceApiService->getGalleryId());
        $this->newLine();

        $allTestsPassed = true;

        // Test 1: API Connection & Authentication
        $allTestsPassed &= $this->testApiConnection();

        // Test 2: Gallery Status
        $allTestsPassed &= $this->testGalleryStatus();

        // Test 3: Full functionality test (if requested)
        if ($this->option('full')) {
            $allTestsPassed &= $this->testFullFunctionality();
        }

        // Summary
        $this->newLine();
        if ($allTestsPassed) {
            $this->success("🎉 All tests passed! Your Face API is working correctly.");
        } else {
            $this->error("❌ Some tests failed. Please check the errors above.");
            return 1;
        }

        return 0;
    }

    /**
     * Test API connection and authentication
     */
    private function testApiConnection(): bool
    {
        $this->info("🔗 Test 1: API Connection & Authentication");
        
        try {
            $response = $this->faceApiService->getCounters();
            
            // Debug the response structure
            if (empty($response)) {
                $this->error("  ❌ Empty response from API");
                return false;
            }
            
            if (isset($response['errors'])) {
                $this->error("  ❌ API Error: " . ($response['errors']['message'] ?? 'Unknown error'));
                return false;
            }
            
            if (isset($response['status']) && $response['status'] === '200') {
                $this->success("  ✅ API connection successful");
                
                if (isset($response['remaining_limit'])) {
                    $limits = $response['remaining_limit'];
                    $this->info("  📊 API Quotas:");
                    $this->info("     • API Hits: " . ($limits['n_api_hits'] ?? 'Unknown'));
                    $this->info("     • Face Enrollments: " . ($limits['n_face'] ?? 'Unknown'));
                    $this->info("     • Face Galleries: " . ($limits['n_facegallery'] ?? 'Unknown'));
                }
                
                return true;
            } else {
                $this->error("  ❌ API authentication failed");
                $this->error("     Status: " . $response['status']);
                $this->error("     Message: " . ($response['status_message'] ?? 'Unknown error'));
                
                if ($response['status'] === '401') {
                    $this->warn("  💡 Check your BIZNET_FACE_API_TOKEN in .env file");
                }
                
                return false;
            }
        } catch (\Exception $e) {
            $this->error("  ❌ Connection error: " . $e->getMessage());
            $this->warn("  💡 Check your BIZNET_FACE_API_URL and internet connection");
            return false;
        }
    }

    /**
     * Test gallery status
     */
    private function testGalleryStatus(): bool
    {
        $this->newLine();
        $this->info("🏛️  Test 2: Gallery Status");
        
        try {
            $galleryId = $this->faceApiService->getGalleryId();
            
            // Check if gallery exists
            $response = $this->faceApiService->getMyFaceGalleries();
            
            if ($response['status'] !== '200') {
                $this->error("  ❌ Failed to fetch galleries");
                $this->error("     Status: " . $response['status']);
                $this->error("     Message: " . ($response['status_message'] ?? 'Unknown error'));
                return false;
            }

            $existingGalleries = $response['facegallery_id'] ?? [];
            
            if (in_array($galleryId, $existingGalleries)) {
                $this->success("  ✅ Gallery '{$galleryId}' exists");
                
                // Get faces count
                $facesResponse = $this->faceApiService->listAllFaces();
                if ($facesResponse['status'] === '200') {
                    $facesCount = count($facesResponse['faces'] ?? []);
                    $this->info("  📊 Enrolled faces: {$facesCount}");
                } else {
                    $this->warn("  ⚠️  Could not fetch face count");
                }
                
                return true;
            } else {
                $this->error("  ❌ Gallery '{$galleryId}' does not exist");
                $this->info("  💡 Run 'php artisan face:init-gallery' to create it");
                return false;
            }
            
        } catch (\Exception $e) {
            $this->error("  ❌ Gallery test error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Test full functionality with image upload
     */
    private function testFullFunctionality(): bool
    {
        $this->newLine();
        $this->info("🖼️  Test 3: Full Functionality Test");
        
        $imagePath = $this->option('image');
        
        if (!$imagePath) {
            $this->warn("  ⚠️  No test image provided. Skipping image tests.");
            $this->info("  💡 Use --image=/path/to/face.jpg to test image functionality");
            return true;
        }

        if (!file_exists($imagePath)) {
            $this->error("  ❌ Test image not found: {$imagePath}");
            return false;
        }

        try {
            // Encode image to base64
            $this->info("  🔄 Encoding test image...");
            $base64Image = $this->faceApiService->encodeImageToBase64($imagePath);
            
            if (!$this->faceApiService->validateBase64Image($base64Image)) {
                $this->error("  ❌ Invalid image format");
                return false;
            }
            
            $this->success("  ✅ Image encoded successfully");

            // Test face identification (1:N)
            $this->info("  🔄 Testing face identification...");
            $identifyResponse = $this->faceApiService->identifyEmployeeFace($base64Image);
            
            if ($identifyResponse['status'] === '200') {
                $this->success("  ✅ Face identification test successful");
                if (isset($identifyResponse['user_id'])) {
                    $this->info("     • Identified user: " . $identifyResponse['user_id']);
                    $this->info("     • Confidence: " . ($identifyResponse['confidence_level'] ?? 'Unknown'));
                } else {
                    $this->info("     • No matching face found (expected for test image)");
                }
            } elseif ($identifyResponse['status'] === '411') {
                $this->success("  ✅ Face identification working (no match found)");
            } else {
                $this->error("  ❌ Face identification test failed");
                $this->error("     Status: " . $identifyResponse['status']);
                $this->error("     Message: " . ($identifyResponse['status_message'] ?? 'Unknown error'));
                return false;
            }

            // Test verification with a dummy user (this will fail but tests the endpoint)
            $this->info("  🔄 Testing face verification endpoint...");
            $verifyResponse = $this->faceApiService->verifyEmployeeFace('test_user_123', $base64Image);
            
            if (in_array($verifyResponse['status'], ['200', '411', '415'])) {
                $this->success("  ✅ Face verification endpoint working");
                if ($verifyResponse['status'] === '415') {
                    $this->info("     • User not found (expected for test)");
                } else {
                    $this->info("     • Verification result: " . ($verifyResponse['verified'] ? 'Verified' : 'Not verified'));
                }
            } else {
                $this->error("  ❌ Face verification test failed");
                $this->error("     Status: " . $verifyResponse['status']);
                $this->error("     Message: " . ($verifyResponse['status_message'] ?? 'Unknown error'));
                return false;
            }

            return true;
            
        } catch (\Exception $e) {
            $this->error("  ❌ Full functionality test error: " . $e->getMessage());
            return false;
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