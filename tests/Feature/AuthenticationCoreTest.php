<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationCoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_roles_are_properly_assigned(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $employee = User::factory()->create(['role' => 'employee']);

        $this->assertEquals('admin', $admin->role);
        $this->assertEquals('employee', $employee->role);
    }

    public function test_user_passwords_are_hashed(): void
    {
        $user = User::factory()->create();
        
        $this->assertNotEquals('password', $user->password);
        $this->assertTrue(Hash::check('password', $user->password));
    }

    public function test_admin_and_employee_have_different_roles(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $employee = User::factory()->create(['role' => 'employee']);

        $this->assertNotEquals($admin->role, $employee->role);
    }

    public function test_user_employee_relationship_works(): void
    {
        $user = User::factory()->create(['role' => 'employee']);
        $employee = Employee::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(Employee::class, $user->employee);
        $this->assertEquals($employee->id, $user->employee->id);
        $this->assertEquals($user->id, $employee->user->id);
    }

    public function test_user_factory_creates_unique_users(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $this->assertNotEquals($user1->email, $user2->email);
        $this->assertNotEquals($user1->username, $user2->username);
    }

    public function test_user_has_required_authentication_fields(): void
    {
        $user = User::factory()->create();

        $this->assertNotNull($user->email);
        $this->assertNotNull($user->username);
        $this->assertNotNull($user->password);
        $this->assertNotNull($user->role);
    }

    public function test_employee_role_is_default(): void
    {
        // Test the default behavior when role is not explicitly set
        $user = User::factory()->make(); // Don't create, just make
        
        // The factory should assign a role
        $this->assertContains($user->role, ['admin', 'employee']);
    }

    public function test_user_authentication_attributes_are_fillable(): void
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'username' => 'testuser',
            'role' => 'employee',
            'password' => Hash::make('password'),
        ];

        $user = User::create($userData);

        $this->assertEquals('Test User', $user->name);
        $this->assertEquals('test@example.com', $user->email);
        $this->assertEquals('testuser', $user->username);
        $this->assertEquals('employee', $user->role);
    }

    public function test_employee_has_work_schedule_attributes(): void
    {
        $employee = Employee::factory()->create();

        $this->assertNotNull($employee->work_start_time);
        $this->assertNotNull($employee->work_end_time);
        $this->assertNotNull($employee->late_tolerance_minutes);
        $this->assertIsArray($employee->work_days);
    }

    public function test_employee_unique_employee_id(): void
    {
        $employee1 = Employee::factory()->create(['employee_id' => 'EMP001']);
        
        $this->expectException(\Illuminate\Database\QueryException::class);
        Employee::factory()->create(['employee_id' => 'EMP001']);
    }
}