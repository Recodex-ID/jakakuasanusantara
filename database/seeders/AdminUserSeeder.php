<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default admin user
        User::firstOrCreate(
            ['email' => 'zachranraze@recodex.id'],
            [
                'name' => 'Zachran Razendra',
                'email' => 'zachranraze@recodex.id',
                'username' => 'zachranraze',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Create demo employee user
        $employeeUser = User::firstOrCreate(
            ['email' => 'employee@jakakuasanusantara.web.id'],
            [
                'name' => 'Bang Demo',
                'email' => 'employee@jakakuasanusantara.web.id',
                'username' => 'employee',
                'password' => Hash::make('password123'),
                'role' => 'employee',
                'email_verified_at' => now(),
            ]
        );

        // Create employee profile for demo user
        if ($employeeUser && !$employeeUser->employee) {
            $employeeUser->employee()->create([
                'employee_id' => 'EMP001',
                'location_id' => 1,
                'department' => 'Information Technology',
                'position' => 'Software Developer',
                'phone' => '081234567890',
                'address' => 'Jakarta, Indonesia',
                'date_of_birth' => '1990-01-01',
                'gender' => 'male',
                'status' => 'active',
                'work_start_time' => '08:00:00',
                'work_end_time' => '17:00:00',
                'late_tolerance_minutes' => 15,
                'work_days' => ['1', '2', '3', '4', '5'], // Monday to Friday
            ]);
        }

        // Create additional admin user
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@jakakuasanusantara.web.id'],
            [
                'name' => 'Admin Manager',
                'email' => 'admin@jakakuasanusantara.web.id',
                'username' => 'adminmanager',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Create admin employee profile
        if ($adminUser && !$adminUser->employee) {
            $adminUser->employee()->create([
                'employee_id' => 'ADM001',
                'location_id' => 1,
                'department' => 'Human Resources',
                'position' => 'HR Manager',
                'phone' => '081234567891',
                'address' => 'Jakarta, Indonesia',
                'date_of_birth' => '1985-06-15',
                'gender' => 'female',
                'status' => 'active',
                'work_start_time' => '08:00:00',
                'work_end_time' => '17:00:00',
                'late_tolerance_minutes' => 10,
                'work_days' => ['1', '2', '3', '4', '5'], // Monday to Friday
            ]);
        }

        // Create additional employee user
        $employee2User = User::firstOrCreate(
            ['email' => 'john.doe@jakakuasanusantara.web.id'],
            [
                'name' => 'John Doe',
                'email' => 'john.doe@jakakuasanusantara.web.id',
                'username' => 'johndoe',
                'password' => Hash::make('password123'),
                'role' => 'employee',
                'email_verified_at' => now(),
            ]
        );

        // Create employee profile for second employee
        if ($employee2User && !$employee2User->employee) {
            $employee2User->employee()->create([
                'employee_id' => 'EMP002',
                'location_id' => 2,
                'department' => 'Finance',
                'position' => 'Accountant',
                'phone' => '081234567892',
                'address' => 'Bandung, Indonesia',
                'date_of_birth' => '1992-03-20',
                'gender' => 'male',
                'status' => 'active',
                'work_start_time' => '09:00:00',
                'work_end_time' => '18:00:00',
                'late_tolerance_minutes' => 20,
                'work_days' => ['1', '2', '3', '4', '5'], // Monday to Friday
            ]);
        }
    }
}
