<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationSimpleTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_has_correct_role(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $employee = User::factory()->create(['role' => 'employee']);

        $this->assertEquals('admin', $admin->role);
        $this->assertEquals('employee', $employee->role);
    }

    public function test_user_employee_relationship(): void
    {
        $user = User::factory()->create(['role' => 'employee']);
        $employee = Employee::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(Employee::class, $user->employee);
        $this->assertEquals($employee->id, $user->employee->id);
    }

    public function test_authenticated_admin_gets_redirected_from_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        
        $response = $this->actingAs($user)->get('/dashboard');
        
        // Admin gets redirected to their specific dashboard
        $response->assertStatus(302);
    }

    public function test_authenticated_employee_gets_redirected_from_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'employee']);
        Employee::factory()->create(['user_id' => $user->id]);
        
        $response = $this->actingAs($user)->get('/dashboard');
        
        // Employee gets redirected to their specific dashboard  
        $response->assertStatus(302);
    }

    public function test_guest_cannot_access_dashboard(): void
    {
        $response = $this->get('/dashboard');
        
        $response->assertRedirect('/login');
    }

    public function test_user_authentication_factory(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'employee'
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'employee'
        ]);
    }

    public function test_user_can_be_created_with_different_roles(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $employee = User::factory()->create(['role' => 'employee']);

        $this->assertNotEquals($admin->role, $employee->role);
        $this->assertTrue(in_array($admin->role, ['admin', 'employee']));
        $this->assertTrue(in_array($employee->role, ['admin', 'employee']));
    }

    public function test_user_model_has_required_attributes(): void
    {
        $user = User::factory()->create();

        $this->assertNotNull($user->name);
        $this->assertNotNull($user->email);
        $this->assertNotNull($user->username);
        $this->assertNotNull($user->role);
        $this->assertNotNull($user->password);
    }

    public function test_employee_model_creation(): void
    {
        $user = User::factory()->create(['role' => 'employee']);
        $employee = Employee::factory()->create(['user_id' => $user->id]);

        $this->assertNotNull($employee->employee_id);
        $this->assertNotNull($employee->work_start_time);
        $this->assertNotNull($employee->work_end_time);
        $this->assertIsArray($employee->work_days);
    }
}