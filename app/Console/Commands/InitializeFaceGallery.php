<?php

namespace App\Console\Commands;

use App\Services\FaceApiService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class InitializeFaceGallery extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'face:init-gallery 
                            {--force : Force create even if gallery already exists}
                            {--check : Only check if gallery exists without creating}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize the face gallery in Biznet Face API';

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
        $galleryId = $this->faceApiService->getGalleryId();
        
        $this->info("Face Gallery Configuration:");
        $this->info("Gallery ID: {$galleryId}");
        $this->info("API URL: " . config('services.biznet_face.url'));
        $this->info("Similarity Threshold: " . config('services.biznet_face.similarity_threshold'));
        $this->newLine();

        // Check if we only want to verify gallery existence
        if ($this->option('check')) {
            return $this->checkGalleryExists($galleryId);
        }

        // Check existing galleries first
        $this->info('Checking existing galleries...');
        try {
            $response = $this->faceApiService->getMyFaceGalleries();
            
            if ($response['status'] !== '200') {
                $this->error('Failed to fetch existing galleries: ' . ($response['status_message'] ?? 'Unknown error'));
                return 1;
            }

            $existingGalleries = $response['facegallery_id'] ?? [];
            $this->info('Existing galleries: ' . (empty($existingGalleries) ? 'None' : implode(', ', $existingGalleries)));

            if (in_array($galleryId, $existingGalleries)) {
                if (!$this->option('force')) {
                    $this->warn("Gallery '{$galleryId}' already exists!");
                    
                    if (!$this->confirm('Do you want to proceed anyway?')) {
                        $this->info('Operation cancelled.');
                        return 0;
                    }
                } else {
                    $this->warn("Gallery '{$galleryId}' already exists, but --force option is used.");
                }
            }

        } catch (\Exception $e) {
            $this->error('Error checking existing galleries: ' . $e->getMessage());
            
            if (!$this->confirm('Continue with gallery creation anyway?')) {
                return 1;
            }
        }

        // Create the gallery
        $this->info("Creating face gallery '{$galleryId}'...");
        
        try {
            $response = $this->faceApiService->initializeGallery();
            
            if ($response['status'] === '200') {
                $this->success("✅ Face gallery '{$galleryId}' created successfully!");
                
                // Log the success
                Log::info('Face gallery initialized successfully', [
                    'gallery_id' => $galleryId,
                    'response' => $response
                ]);
                
                // Show next steps
                $this->newLine();
                $this->info('🎉 Your face recognition system is ready!');
                $this->info('Next steps:');
                $this->info('1. Start enrolling employee faces through the admin panel');
                $this->info('2. Configure attendance locations if needed');
                $this->info('3. Test face verification with enrolled employees');
                
                return 0;
                
            } else {
                $this->error("❌ Failed to create face gallery: " . ($response['status_message'] ?? 'Unknown error'));
                $this->error("Status: " . $response['status']);
                
                // Log the error
                Log::error('Face gallery initialization failed', [
                    'gallery_id' => $galleryId,
                    'response' => $response
                ]);
                
                // Show common solutions
                $this->newLine();
                $this->info('💡 Common solutions:');
                $this->info('• Check if your API token is valid and active');
                $this->info('• Verify you have remaining gallery quota');
                $this->info('• Ensure gallery ID contains only valid characters (alphanumeric, dots, underscores, hyphens)');
                $this->info('• Check your internet connection');
                
                return 1;
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Error creating face gallery: " . $e->getMessage());
            
            // Log the exception
            Log::error('Face gallery initialization exception', [
                'gallery_id' => $galleryId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return 1;
        }
    }

    /**
     * Check if gallery exists without creating it
     */
    private function checkGalleryExists(string $galleryId): int
    {
        $this->info('Checking if gallery exists...');
        
        try {
            $response = $this->faceApiService->getMyFaceGalleries();
            
            if ($response['status'] !== '200') {
                $this->error('Failed to fetch galleries: ' . ($response['status_message'] ?? 'Unknown error'));
                return 1;
            }

            $existingGalleries = $response['facegallery_id'] ?? [];
            
            if (in_array($galleryId, $existingGalleries)) {
                $this->success("✅ Gallery '{$galleryId}' exists!");
                
                // Get gallery details
                $this->info('Getting gallery details...');
                $facesResponse = $this->faceApiService->listAllFaces();
                
                if ($facesResponse['status'] === '200') {
                    $faces = $facesResponse['faces'] ?? [];
                    $this->info("📊 Gallery statistics:");
                    $this->info("• Enrolled faces: " . count($faces));
                    
                    if (!empty($faces)) {
                        $this->info("• Recent enrollments:");
                        foreach (array_slice($faces, 0, 5) as $face) {
                            $userId = $face['user_id'] ?? 'Unknown';
                            $userName = $face['user_name'] ?? 'Unknown';
                            $this->info("  - {$userName} ({$userId})");
                        }
                        
                        if (count($faces) > 5) {
                            $this->info("  ... and " . (count($faces) - 5) . " more");
                        }
                    }
                } else {
                    $this->warn('Could not fetch gallery details: ' . ($facesResponse['status_message'] ?? 'Unknown error'));
                }
                
                return 0;
            } else {
                $this->warn("❌ Gallery '{$galleryId}' does not exist!");
                $this->info('Available galleries: ' . (empty($existingGalleries) ? 'None' : implode(', ', $existingGalleries)));
                $this->info("Run 'php artisan face:init-gallery' to create it.");
                return 1;
            }
            
        } catch (\Exception $e) {
            $this->error('Error checking gallery: ' . $e->getMessage());
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