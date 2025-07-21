<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Employee;
use App\Models\Location;
use App\Models\Attendance;
use App\Models\AttendanceLog;
use App\Services\FaceApiService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ModuleTestsSuite extends TestCase
{
    use RefreshDatabase;

    // Authentication Module Tests
    public function test_user_authentication_roles(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $employee = User::factory()->create(['role' => 'employee']);

        $this->assertEquals('admin', $admin->role);
        $this->assertEquals('employee', $employee->role);
    }

    public function test_user_model_relationships(): void
    {
        $user = User::factory()->create(['role' => 'employee']);
        $employee = Employee::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(Employee::class, $user->employee);
        $this->assertEquals($employee->id, $user->employee->id);
    }

    // Face Recognition Module Tests
    public function test_face_api_service_initialization(): void
    {
        $faceApiService = app(FaceApiService::class);
        $this->assertInstanceOf(FaceApiService::class, $faceApiService);
        $this->assertNotEmpty($faceApiService->getGalleryId());
    }

    public function test_face_verification_response(): void
    {
        Http::fake([
            '*/facegallery/verify-face' => Http::response([
                'risetai' => [
                    'status' => '200',
                    'verified' => true,
                    'similarity' => 0.95
                ]
            ], 200)
        ]);

        $faceApiService = app(FaceApiService::class);
        $response = $faceApiService->verifyEmployeeFace('EMP001', base64_encode('fake_image'));

        $this->assertEquals('200', $response['status']);
        $this->assertTrue($response['verified']);
        $this->assertGreaterThan(0.9, $response['similarity']);
    }

    public function test_face_similarity_threshold(): void
    {
        $faceApiService = app(FaceApiService::class);
        
        $this->assertTrue($faceApiService->isAboveThreshold(0.85));
        $this->assertFalse($faceApiService->isAboveThreshold(0.60));
    }

    // Geolocation Module Tests
    public function test_location_model_creation(): void
    {
        $location = Location::factory()->create([
            'name' => 'Test Office',
            'latitude' => -6.2088,
            'longitude' => 106.8456,
            'radius_meters' => 100
        ]);

        $this->assertEquals('Test Office', $location->name);
        $this->assertEquals(-6.2088, $location->latitude);
        $this->assertEquals(106.8456, $location->longitude);
        $this->assertEquals(100, $location->radius_meters);
    }

    public function test_distance_calculation(): void
    {
        // Mock distance calculation test
        $lat1 = -6.2088;
        $lon1 = 106.8456;
        $lat2 = -6.2078;
        $lon2 = 106.8466;

        $earthRadius = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;

        $this->assertIsFloat($distance);
        $this->assertLessThan(200, $distance);
    }

    public function test_location_employee_relationship(): void
    {
        $location = Location::factory()->create();
        $employee = Employee::factory()->create(['location_id' => $location->id]);

        $this->assertTrue($location->employees()->exists());
        $this->assertEquals($employee->id, $location->employees()->first()->id);
    }

    // Database Module Tests
    public function test_database_tables_exist(): void
    {
        $tables = ['users', 'employees', 'locations', 'attendances', 'attendance_logs'];
        
        foreach ($tables as $table) {
            $this->assertTrue(Schema::hasTable($table));
        }
    }

    public function test_user_table_structure(): void
    {
        $columns = ['id', 'name', 'email', 'username', 'role', 'password'];
        
        foreach ($columns as $column) {
            $this->assertTrue(Schema::hasColumn('users', $column));
        }
    }

    public function test_employee_table_structure(): void
    {
        $columns = ['id', 'user_id', 'employee_id', 'location_id', 'work_start_time', 'work_end_time'];
        
        foreach ($columns as $column) {
            $this->assertTrue(Schema::hasColumn('employees', $column));
        }
    }

    public function test_attendance_workflow(): void
    {
        $user = User::factory()->create(['role' => 'employee']);
        $location = Location::factory()->create();
        $employee = Employee::factory()->create([
            'user_id' => $user->id,
            'location_id' => $location->id
        ]);

        $attendance = Attendance::factory()->create([
            'employee_id' => $employee->id,
            'location_id' => $location->id,
            'date' => Carbon::today()
        ]);

        $attendanceLog = AttendanceLog::factory()->create([
            'attendance_id' => $attendance->id,
            'employee_id' => $employee->id,
            'location_id' => $location->id,
            'action' => 'check_in'
        ]);

        $this->assertDatabaseHas('attendances', [
            'employee_id' => $employee->id,
            'location_id' => $location->id
        ]);

        $this->assertDatabaseHas('attendance_logs', [
            'attendance_id' => $attendance->id,
            'action' => 'check_in'
        ]);
    }

    public function test_factory_relationships(): void
    {
        $user = User::factory()->create(['role' => 'employee']);
        $location = Location::factory()->create();
        $employee = Employee::factory()->create([
            'user_id' => $user->id,
            'location_id' => $location->id
        ]);

        $this->assertInstanceOf(User::class, $employee->user);
        $this->assertInstanceOf(Location::class, $employee->location);
        $this->assertEquals($user->id, $employee->user->id);
        $this->assertEquals($location->id, $employee->location->id);
    }

    public function test_json_data_storage(): void
    {
        $apiResponse = [
            'status' => '200',
            'verified' => true,
            'similarity' => 0.95
        ];

        $attendanceLog = AttendanceLog::factory()->create([
            'face_api_response' => $apiResponse
        ]);

        $retrieved = AttendanceLog::find($attendanceLog->id);
        $this->assertEquals($apiResponse, $retrieved->face_api_response);
    }

    public function test_unique_constraints(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'username' => 'testuser'
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);
        User::factory()->create([
            'email' => 'test@example.com',
            'username' => 'testuser2'
        ]);
    }

    public function test_employee_face_enrollment_status(): void
    {
        $employee1 = Employee::factory()->create();
        $employee2 = Employee::factory()->create();

        // Test the isFaceEnrolled method exists
        $this->assertIsBool($employee1->isFaceEnrolled());
        $this->assertIsBool($employee2->isFaceEnrolled());
    }

    public function test_location_status_filtering(): void
    {
        $activeLocation = Location::factory()->active()->create();
        $inactiveLocation = Location::factory()->inactive()->create();

        $this->assertEquals('active', $activeLocation->status);
        $this->assertEquals('inactive', $inactiveLocation->status);
    }

    public function test_attendance_status_types(): void
    {
        $presentAttendance = Attendance::factory()->present()->create();
        $lateAttendance = Attendance::factory()->late()->create();
        $absentAttendance = Attendance::factory()->absent()->create();

        $this->assertEquals('present', $presentAttendance->status);
        $this->assertEquals('late', $lateAttendance->status);
        $this->assertEquals('absent', $absentAttendance->status);
    }
}