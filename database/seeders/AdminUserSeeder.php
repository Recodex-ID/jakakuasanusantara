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
                'name' => 'Demo Employee',
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
                'department' => 'Information Technology',
                'position' => 'Software Developer',
                'phone' => '081234567890',
                'address' => 'Jakarta, Indonesia',
                'date_of_birth' => '1990-01-01',
                'gender' => 'male',
                'status' => 'active',
            ]);
        }
    }
}
