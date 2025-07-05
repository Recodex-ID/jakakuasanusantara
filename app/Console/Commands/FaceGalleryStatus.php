<?php

namespace App\Console\Commands;

use App\Services\FaceApiService;
use Illuminate\Console\Command;

class FaceGalleryStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'face:status 
                            {--counters : Show API usage counters}
                            {--faces : Show enrolled faces list}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show face gallery status and statistics';

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
        
        $this->info("📊 Face Gallery Status Report");
        $this->info("Gallery ID: {$galleryId}");
        $this->info("Generated at: " . now()->format('Y-m-d H:i:s'));
        $this->newLine();

        // Show API counters if requested
        if ($this->option('counters')) {
            $this->showApiCounters();
        }

        // Check gallery existence and get faces
        $this->checkGalleryStatus($galleryId);

        // Show faces list if requested
        if ($this->option('faces')) {
            $this->showEnrolledFaces();
        }

        return 0;
    }

    /**
     * Show API usage counters
     */
    private function showApiCounters(): void
    {
        $this->info('🔢 API Usage Counters:');
        
        try {
            $response = $this->faceApiService->getCounters();
            
            if ($response['status'] === '200' && isset($response['remaining_limit'])) {
                $limits = $response['remaining_limit'];
                
                $headers = ['Resource', 'Remaining', 'Status'];
                $rows = [
                    ['API Hits', $limits['n_api_hits'] ?? 'Unknown', $this->getStatusIndicator($limits['n_api_hits'] ?? 0, 100)],
                    ['Face Enrollments', $limits['n_face'] ?? 'Unknown', $this->getStatusIndicator($limits['n_face'] ?? 0, 50)],
                    ['Face Galleries', $limits['n_facegallery'] ?? 'Unknown', $this->getStatusIndicator($limits['n_facegallery'] ?? 0, 1)],
                ];
                
                $this->table($headers, $rows);
            } else {
                $this->error('Failed to fetch API counters: ' . ($response['status_message'] ?? 'Unknown error'));
            }
        } catch (\Exception $e) {
            $this->error('Error fetching API counters: ' . $e->getMessage());
        }
        
        $this->newLine();
    }

    /**
     * Check gallery status
     */
    private function checkGalleryStatus(string $galleryId): void
    {
        $this->info('🏛️  Gallery Status:');
        
        try {
            // Check if gallery exists
            $response = $this->faceApiService->getMyFaceGalleries();
            
            if ($response['status'] !== '200') {
                $this->error('❌ Cannot connect to Face API: ' . ($response['status_message'] ?? 'Unknown error'));
                return;
            }

            $existingGalleries = $response['facegallery_id'] ?? [];
            
            if (!in_array($galleryId, $existingGalleries)) {
                $this->error("❌ Gallery '{$galleryId}' does not exist!");
                $this->info('Available galleries: ' . (empty($existingGalleries) ? 'None' : implode(', ', $existingGalleries)));
                $this->info("Run 'php artisan face:init-gallery' to create it.");
                return;
            }

            $this->success("✅ Gallery '{$galleryId}' is active");

            // Get faces count
            $facesResponse = $this->faceApiService->listAllFaces();
            
            if ($facesResponse['status'] === '200') {
                $faces = $facesResponse['faces'] ?? [];
                $facesCount = count($faces);
                
                $this->info("📈 Statistics:");
                $this->info("• Total enrolled faces: {$facesCount}");
                
                if ($facesCount > 0) {
                    // Group by enrollment date (if available)
                    $this->info("• Status: Ready for face recognition");
                    
                    // Show face quality distribution if available
                    $qualityDistribution = $this->analyzeImageQuality($faces);
                    if (!empty($qualityDistribution)) {
                        $this->info("• Image quality distribution:");
                        foreach ($qualityDistribution as $quality => $count) {
                            $this->info("  - {$quality}: {$count} faces");
                        }
                    }
                } else {
                    $this->warn("• Status: No faces enrolled yet");
                    $this->info("• Next step: Start enrolling employee faces");
                }
            } else {
                $this->warn('Could not fetch face statistics: ' . ($facesResponse['status_message'] ?? 'Unknown error'));
            }
            
        } catch (\Exception $e) {
            $this->error('Error checking gallery status: ' . $e->getMessage());
        }
        
        $this->newLine();
    }

    /**
     * Show enrolled faces list
     */
    private function showEnrolledFaces(): void
    {
        $this->info('👥 Enrolled Faces:');
        
        try {
            $response = $this->faceApiService->listAllFaces();
            
            if ($response['status'] === '200') {
                $faces = $response['faces'] ?? [];
                
                if (empty($faces)) {
                    $this->warn('No faces enrolled yet.');
                    return;
                }

                $headers = ['User ID', 'User Name', 'Quality', 'Status'];
                $rows = [];
                
                foreach ($faces as $face) {
                    $userId = $face['user_id'] ?? 'Unknown';
                    $userName = $face['user_name'] ?? 'Unknown';
                    $quality = isset($face['quality']) ? number_format($face['quality'] * 100, 1) . '%' : 'Unknown';
                    $status = '✅ Enrolled';
                    
                    $rows[] = [$userId, $userName, $quality, $status];
                }
                
                $this->table($headers, $rows);
                $this->info("Total: " . count($faces) . " faces enrolled");
            } else {
                $this->error('Failed to fetch faces: ' . ($response['status_message'] ?? 'Unknown error'));
            }
        } catch (\Exception $e) {
            $this->error('Error fetching faces: ' . $e->getMessage());
        }
    }

    /**
     * Get status indicator based on remaining count
     */
    private function getStatusIndicator(int $remaining, int $warningThreshold): string
    {
        if ($remaining > $warningThreshold) {
            return '🟢 Good';
        } elseif ($remaining > 0) {
            return '🟡 Low';
        } else {
            return '🔴 Empty';
        }
    }

    /**
     * Analyze image quality distribution
     */
    private function analyzeImageQuality(array $faces): array
    {
        $distribution = [
            'Excellent (90%+)' => 0,
            'Good (70-89%)' => 0,
            'Fair (50-69%)' => 0,
            'Poor (<50%)' => 0,
        ];

        foreach ($faces as $face) {
            if (!isset($face['quality'])) {
                continue;
            }

            $quality = $face['quality'] * 100;
            
            if ($quality >= 90) {
                $distribution['Excellent (90%+)']++;
            } elseif ($quality >= 70) {
                $distribution['Good (70-89%)']++;
            } elseif ($quality >= 50) {
                $distribution['Fair (50-69%)']++;
            } else {
                $distribution['Poor (<50%)']++;
            }
        }

        // Remove empty categories
        return array_filter($distribution);
    }

    /**
     * Write a success message to the console.
     */
    private function success(string $message): void
    {
        $this->line("<fg=green>{$message}</>");
    }
}