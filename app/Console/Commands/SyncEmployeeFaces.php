<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Services\FaceApiService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncEmployeeFaces extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'face:sync-employees 
                            {--check : Only check sync status without making changes}
                            {--employee= : Sync specific employee by ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync employee face enrollment status with Face API';

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
        
        $this->info("🔄 Face Enrollment Sync");
        $this->info("Gallery ID: {$galleryId}");
        $this->newLine();

        // Get enrolled faces from API
        $this->info('Fetching enrolled faces from Face API...');
        
        try {
            $response = $this->faceApiService->listAllFaces();
            
            if ($response['status'] !== '200') {
                $this->error('Failed to fetch faces from API: ' . ($response['status_message'] ?? 'Unknown error'));
                return 1;
            }

            $apiFaces = $response['faces'] ?? [];
            $this->info('Found ' . count($apiFaces) . ' faces in API gallery');

        } catch (\Exception $e) {
            $this->error('Error fetching faces from API: ' . $e->getMessage());
            return 1;
        }

        // Create lookup array for faster searching
        $enrolledUserIds = array_column($apiFaces, 'user_id');

        // Get employees from database
        if ($this->option('employee')) {
            $employees = Employee::where('employee_id', $this->option('employee'))->get();
            if ($employees->isEmpty()) {
                $this->error('Employee not found: ' . $this->option('employee'));
                return 1;
            }
        } else {
            $employees = Employee::with('user')->get();
        }

        $this->info('Found ' . $employees->count() . ' employees in database');
        $this->newLine();

        // Analysis arrays
        $correctlyMarked = [];
        $incorrectlyMarked = [];
        $notEnrolledButMarked = [];
        $enrolledButNotMarked = [];

        // Analyze each employee
        foreach ($employees as $employee) {
            $isEnrolledInApi = in_array($employee->employee_id, $enrolledUserIds);
            $isMarkedInDb = $employee->face_enrolled;

            if ($isEnrolledInApi && $isMarkedInDb) {
                $correctlyMarked[] = $employee;
            } elseif (!$isEnrolledInApi && !$isMarkedInDb) {
                $correctlyMarked[] = $employee;
            } elseif (!$isEnrolledInApi && $isMarkedInDb) {
                $notEnrolledButMarked[] = $employee;
            } elseif ($isEnrolledInApi && !$isMarkedInDb) {
                $enrolledButNotMarked[] = $employee;
            }
        }

        // Show analysis results
        $this->showSyncAnalysis($correctlyMarked, $notEnrolledButMarked, $enrolledButNotMarked);

        // If only checking, stop here
        if ($this->option('check')) {
            return 0;
        }

        // Fix inconsistencies
        $fixed = 0;

        if (!empty($notEnrolledButMarked)) {
            $this->info('Fixing employees marked as enrolled but not in API...');
            foreach ($notEnrolledButMarked as $employee) {
                $employee->update(['face_enrolled' => false]);
                $this->line("  ✅ {$employee->user->name} ({$employee->employee_id}) - unmarked");
                $fixed++;
            }
        }

        if (!empty($enrolledButNotMarked)) {
            $this->info('Fixing employees enrolled in API but not marked in database...');
            foreach ($enrolledButNotMarked as $employee) {
                $employee->update(['face_enrolled' => true]);
                $this->line("  ✅ {$employee->user->name} ({$employee->employee_id}) - marked as enrolled");
                $fixed++;
            }
        }

        // Summary
        $this->newLine();
        if ($fixed > 0) {
            $this->success("✅ Sync completed! Fixed {$fixed} inconsistencies.");
            
            Log::info('Employee face sync completed', [
                'total_employees' => $employees->count(),
                'fixes_applied' => $fixed,
                'gallery_id' => $galleryId
            ]);
        } else {
            $this->success("✅ All employee face statuses are already in sync!");
        }

        return 0;
    }

    /**
     * Show sync analysis results
     */
    private function showSyncAnalysis(array $correctlyMarked, array $notEnrolledButMarked, array $enrolledButNotMarked): void
    {
        $this->info('📊 Sync Analysis:');

        // Summary table
        $headers = ['Status', 'Count', 'Description'];
        $rows = [
            ['✅ Correct', count($correctlyMarked), 'Database status matches API'],
            ['❌ DB: Enrolled, API: Not found', count($notEnrolledButMarked), 'Marked as enrolled but not in Face API'],
            ['❌ DB: Not enrolled, API: Found', count($enrolledButNotMarked), 'Found in Face API but not marked'],
        ];

        $this->table($headers, $rows);

        // Show details for inconsistencies
        if (!empty($notEnrolledButMarked)) {
            $this->warn('Employees marked as enrolled but not found in Face API:');
            foreach ($notEnrolledButMarked as $employee) {
                $this->line("  • {$employee->user->name} ({$employee->employee_id})");
            }
            $this->newLine();
        }

        if (!empty($enrolledButNotMarked)) {
            $this->warn('Employees found in Face API but not marked as enrolled:');
            foreach ($enrolledButNotMarked as $employee) {
                $this->line("  • {$employee->user->name} ({$employee->employee_id})");
            }
            $this->newLine();
        }

        if (empty($notEnrolledButMarked) && empty($enrolledButNotMarked)) {
            $this->success('🎉 All employee face statuses are perfectly synchronized!');
            $this->newLine();
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