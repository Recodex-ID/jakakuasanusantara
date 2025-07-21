<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Employee;
use App\Models\Location;
use App\Models\Attendance;
use App\Models\AttendanceLog;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DatabaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_table_structure(): void
    {
        $this->assertTrue(Schema::hasTable('users'));
        
        $expectedColumns = [
            'id', 'name', 'email', 'username', 'email_verified_at', 'password', 
            'role', 'remember_token', 'created_at', 'updated_at'
        ];
        
        foreach ($expectedColumns as $column) {
            $this->assertTrue(Schema::hasColumn('users', $column));
        }
    }

    public function test_employees_table_structure(): void
    {
        $this->assertTrue(Schema::hasTable('employees'));
        
        $expectedColumns = [
            'id', 'user_id', 'employee_id', 'location_id', 'phone', 
            'work_start_time', 'work_end_time', 'late_tolerance_minutes', 'work_days', 
            'created_at', 'updated_at'
        ];
        
        foreach ($expectedColumns as $column) {
            $this->assertTrue(Schema::hasColumn('employees', $column));
        }
    }

    public function test_locations_table_structure(): void
    {
        $this->assertTrue(Schema::hasTable('locations'));
        
        $expectedColumns = [
            'id', 'name', 'address', 'latitude', 'longitude', 
            'radius_meters', 'status', 'created_at', 'updated_at'
        ];
        
        foreach ($expectedColumns as $column) {
            $this->assertTrue(Schema::hasColumn('locations', $column));
        }
    }

    public function test_attendances_table_structure(): void
    {
        $this->assertTrue(Schema::hasTable('attendances'));
        
        $expectedColumns = [
            'id', 'employee_id', 'location_id', 'date', 'check_in', 'check_out',
            'check_in_lat', 'check_in_lng', 'check_out_lat', 'check_out_lng',
            'status', 'created_at', 'updated_at'
        ];
        
        foreach ($expectedColumns as $column) {
            $this->assertTrue(Schema::hasColumn('attendances', $column));
        }
    }

    public function test_attendance_logs_table_structure(): void
    {
        $this->assertTrue(Schema::hasTable('attendance_logs'));
        
        $expectedColumns = [
            'id', 'attendance_id', 'employee_id', 'location_id', 'action',
            'action_time', 'method', 'latitude', 'longitude', 'face_similarity',
            'face_verified', 'face_api_response', 'created_at', 'updated_at'
        ];
        
        foreach ($expectedColumns as $column) {
            $this->assertTrue(Schema::hasColumn('attendance_logs', $column));
        }
    }

    public function test_user_employee_relationship(): void
    {
        $user = User::factory()->create(['role' => 'employee']);
        $employee = Employee::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(Employee::class, $user->employee);
        $this->assertEquals($employee->id, $user->employee->id);
        
        $this->assertInstanceOf(User::class, $employee->user);
        $this->assertEquals($user->id, $employee->user->id);
    }

    public function test_employee_location_relationship(): void
    {
        $location = Location::factory()->create();
        $employee = Employee::factory()->create(['location_id' => $location->id]);

        $this->assertInstanceOf(Location::class, $employee->location);
        $this->assertEquals($location->id, $employee->location->id);
        
        $this->assertTrue($location->employees()->exists());
        $this->assertEquals($employee->id, $location->employees()->first()->id);
    }

    public function test_employee_attendance_relationship(): void
    {
        $user = User::factory()->create(['role' => 'employee']);
        $location = Location::factory()->create();
        $employee = Employee::factory()->create([
            'user_id' => $user->id,
            'location_id' => $location->id
        ]);
        
        $attendance = Attendance::factory()->create([
            'employee_id' => $employee->id,
            'location_id' => $location->id
        ]);

        $this->assertTrue($employee->attendances()->exists());
        $this->assertEquals($attendance->id, $employee->attendances()->first()->id);
        
        $this->assertInstanceOf(Employee::class, $attendance->employee);
        $this->assertEquals($employee->id, $attendance->employee->id);
    }

    public function test_attendance_attendance_log_relationship(): void
    {
        $user = User::factory()->create(['role' => 'employee']);
        $location = Location::factory()->create();
        $employee = Employee::factory()->create([
            'user_id' => $user->id,
            'location_id' => $location->id
        ]);
        
        $attendance = Attendance::factory()->create([
            'employee_id' => $employee->id,
            'location_id' => $location->id
        ]);
        
        $attendanceLog = AttendanceLog::factory()->create([
            'attendance_id' => $attendance->id,
            'employee_id' => $employee->id,
            'location_id' => $location->id
        ]);

        $this->assertTrue($attendance->attendanceLogs()->exists());
        $this->assertEquals($attendanceLog->id, $attendance->attendanceLogs()->first()->id);
        
        $this->assertInstanceOf(Attendance::class, $attendanceLog->attendance);
        $this->assertEquals($attendance->id, $attendanceLog->attendance->id);
    }

    public function test_location_attendance_relationship(): void
    {
        $location = Location::factory()->create();
        $employee = Employee::factory()->create(['location_id' => $location->id]);
        $attendance = Attendance::factory()->create([
            'employee_id' => $employee->id,
            'location_id' => $location->id
        ]);

        $this->assertTrue($location->attendances()->exists());
        $this->assertEquals($attendance->id, $location->attendances()->first()->id);
        
        $this->assertInstanceOf(Location::class, $attendance->location);
        $this->assertEquals($location->id, $attendance->location->id);
    }

    public function test_user_factory_creates_valid_user(): void
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'role' => 'employee'
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'role' => 'employee'
        ]);
        
        $this->assertNotNull($user->password);
        $this->assertInstanceOf(User::class, $user);
    }

    public function test_employee_factory_creates_valid_employee(): void
    {
        $user = User::factory()->create(['role' => 'employee']);
        $location = Location::factory()->create();
        
        $employee = Employee::factory()->create([
            'user_id' => $user->id,
            'location_id' => $location->id,
            'employee_id' => 'EMP001',
            'phone' => '081234567890'
        ]);

        $this->assertDatabaseHas('employees', [
            'user_id' => $user->id,
            'location_id' => $location->id,
            'employee_id' => 'EMP001',
            'phone' => '081234567890'
        ]);
        
        $this->assertInstanceOf(Employee::class, $employee);
    }

    public function test_location_factory_creates_valid_location(): void
    {
        $location = Location::factory()->create([
            'name' => 'Main Office',
            'address' => '123 Main St',
            'latitude' => -6.2088,
            'longitude' => 106.8456,
            'radius_meters' => 100
        ]);

        $this->assertDatabaseHas('locations', [
            'name' => 'Main Office',
            'address' => '123 Main St',
            'latitude' => -6.2088,
            'longitude' => 106.8456,
            'radius_meters' => 100
        ]);
        
        $this->assertInstanceOf(Location::class, $location);
    }

    public function test_attendance_factory_creates_valid_attendance(): void
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
            'date' => Carbon::today(),
            'status' => 'present'
        ]);

        $this->assertDatabaseHas('attendances', [
            'employee_id' => $employee->id,
            'location_id' => $location->id,
            'date' => Carbon::today(),
            'status' => 'present'
        ]);
        
        $this->assertInstanceOf(Attendance::class, $attendance);
    }

    public function test_attendance_log_factory_creates_valid_log(): void
    {
        $user = User::factory()->create(['role' => 'employee']);
        $location = Location::factory()->create();
        $employee = Employee::factory()->create([
            'user_id' => $user->id,
            'location_id' => $location->id
        ]);
        
        $attendance = Attendance::factory()->create([
            'employee_id' => $employee->id,
            'location_id' => $location->id
        ]);
        
        $attendanceLog = AttendanceLog::factory()->create([
            'attendance_id' => $attendance->id,
            'employee_id' => $employee->id,
            'location_id' => $location->id,
            'action' => 'check_in'
        ]);

        $this->assertDatabaseHas('attendance_logs', [
            'attendance_id' => $attendance->id,
            'employee_id' => $employee->id,
            'location_id' => $location->id,
            'action' => 'check_in'
        ]);
        
        $this->assertInstanceOf(AttendanceLog::class, $attendanceLog);
    }

    public function test_database_constraints_and_foreign_keys(): void
    {
        $user = User::factory()->create(['role' => 'employee']);
        $location = Location::factory()->create();
        $employee = Employee::factory()->create([
            'user_id' => $user->id,
            'location_id' => $location->id
        ]);

        // Test cascade delete behavior
        $attendance = Attendance::factory()->create([
            'employee_id' => $employee->id,
            'location_id' => $location->id
        ]);
        
        $attendanceLog = AttendanceLog::factory()->create([
            'attendance_id' => $attendance->id,
            'employee_id' => $employee->id,
            'location_id' => $location->id
        ]);

        $this->assertDatabaseHas('attendances', ['id' => $attendance->id]);
        $this->assertDatabaseHas('attendance_logs', ['id' => $attendanceLog->id]);
        
        // Delete user should cascade to employee
        $user->delete();
        
        $this->assertDatabaseMissing('employees', ['id' => $employee->id]);
        $this->assertDatabaseMissing('attendances', ['id' => $attendance->id]);
        $this->assertDatabaseMissing('attendance_logs', ['id' => $attendanceLog->id]);
    }

    public function test_database_unique_constraints(): void
    {
        $user1 = User::factory()->create([
            'email' => 'test@example.com',
            'username' => 'testuser'
        ]);
        
        $this->expectException(\Illuminate\Database\QueryException::class);
        User::factory()->create([
            'email' => 'test@example.com',
            'username' => 'testuser2'
        ]);
    }

    public function test_employee_unique_employee_id(): void
    {
        $user1 = User::factory()->create(['role' => 'employee']);
        $user2 = User::factory()->create(['role' => 'employee']);
        
        $location = Location::factory()->create();
        
        Employee::factory()->create([
            'user_id' => $user1->id,
            'location_id' => $location->id,
            'employee_id' => 'EMP001'
        ]);
        
        $this->expectException(\Illuminate\Database\QueryException::class);
        Employee::factory()->create([
            'user_id' => $user2->id,
            'location_id' => $location->id,
            'employee_id' => 'EMP001'
        ]);
    }

    public function test_attendance_unique_per_employee_per_date(): void
    {
        $user = User::factory()->create(['role' => 'employee']);
        $location = Location::factory()->create();
        $employee = Employee::factory()->create([
            'user_id' => $user->id,
            'location_id' => $location->id
        ]);
        
        $date = Carbon::today();
        
        Attendance::factory()->create([
            'employee_id' => $employee->id,
            'location_id' => $location->id,
            'date' => $date
        ]);
        
        $this->expectException(\Illuminate\Database\QueryException::class);
        Attendance::factory()->create([
            'employee_id' => $employee->id,
            'location_id' => $location->id,
            'date' => $date
        ]);
    }

    public function test_database_indexes_performance(): void
    {
        $user = User::factory()->create(['role' => 'employee']);
        $location = Location::factory()->create();
        $employee = Employee::factory()->create([
            'user_id' => $user->id,
            'location_id' => $location->id
        ]);
        
        // Create multiple attendance records
        for ($i = 0; $i < 10; $i++) {
            Attendance::factory()->create([
                'employee_id' => $employee->id,
                'location_id' => $location->id,
                'date' => Carbon::today()->subDays($i)
            ]);
        }
        
        // Test that queries using indexed columns are efficient
        $startTime = microtime(true);
        $attendances = Attendance::where('employee_id', $employee->id)
            ->where('date', '>=', Carbon::today()->subDays(5))
            ->get();
        $endTime = microtime(true);
        
        $this->assertLessThan(0.1, $endTime - $startTime); // Should be fast
        $this->assertGreaterThan(0, $attendances->count());
    }

    public function test_json_column_storage_and_retrieval(): void
    {
        $user = User::factory()->create(['role' => 'employee']);
        $location = Location::factory()->create();
        $employee = Employee::factory()->create([
            'user_id' => $user->id,
            'location_id' => $location->id
        ]);
        
        $attendance = Attendance::factory()->create([
            'employee_id' => $employee->id,
            'location_id' => $location->id
        ]);
        
        $apiResponse = [
            'status' => '200',
            'verified' => true,
            'similarity' => 0.95,
            'face_id' => 'face_123'
        ];
        
        $attendanceLog = AttendanceLog::factory()->create([
            'attendance_id' => $attendance->id,
            'employee_id' => $employee->id,
            'location_id' => $location->id,
            'face_api_response' => $apiResponse
        ]);
        
        $retrievedLog = AttendanceLog::find($attendanceLog->id);
        $this->assertEquals($apiResponse, $retrievedLog->face_api_response);
    }

    public function test_database_transactions_rollback(): void
    {
        $user = User::factory()->create(['role' => 'employee']);
        $location = Location::factory()->create();
        $employee = Employee::factory()->create([
            'user_id' => $user->id,
            'location_id' => $location->id
        ]);
        
        try {
            \DB::transaction(function () use ($employee, $location) {
                $attendance = Attendance::factory()->create([
                    'employee_id' => $employee->id,
                    'location_id' => $location->id
                ]);
                
                // This should cause a rollback
                throw new \Exception('Test rollback');
            });
        } catch (\Exception $e) {
            // Expected exception
        }
        
        $this->assertDatabaseMissing('attendances', ['employee_id' => $employee->id]);
    }

    public function test_soft_deletes_if_implemented(): void
    {
        // Test if soft deletes are implemented on any models
        $user = User::factory()->create(['role' => 'employee']);
        $location = Location::factory()->create();
        $employee = Employee::factory()->create([
            'user_id' => $user->id,
            'location_id' => $location->id
        ]);
        
        // Check if models have soft delete capability
        $this->assertFalse(method_exists($user, 'trashed'));
        $this->assertFalse(method_exists($employee, 'trashed'));
        $this->assertFalse(method_exists($location, 'trashed'));
    }
}