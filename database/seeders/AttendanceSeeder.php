<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\AttendanceLog;
use App\Models\Employee;
use App\Models\Location;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employee::all();
        $locations = Location::all();

        if ($employees->isEmpty() || $locations->isEmpty()) {
            $this->command->warn('No employees or locations found. Please run EmployeeSeeder and LocationSeeder first.');
            return;
        }

        // Generate attendance for the last 30 days
        $startDate = Carbon::now()->subDays(30);
        $endDate = Carbon::now();

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $this->generateDailyAttendance($employees, $locations, $date->copy());
        }
    }

    private function generateDailyAttendance($employees, $locations, Carbon $date)
    {
        // Skip weekends (Saturday = 6, Sunday = 0)
        if ($date->dayOfWeek === 0 || $date->dayOfWeek === 6) {
            return;
        }

        foreach ($employees as $employee) {
            // Check if employee should work on this day
            if (!$employee->isWorkDay($date)) {
                continue;
            }

            // 90% chance of attendance
            if (rand(1, 100) > 90) {
                // Create absent record
                $this->createAttendanceRecord($employee, $locations->random(), $date, 'absent');
                continue;
            }

            $location = $locations->random();

            // Determine if employee is late (20% chance)
            $isLate = rand(1, 100) <= 20;

            // Get employee work schedule
            $workStartTime = $employee->work_start_time ?? '09:00:00';
            $workEndTime = $employee->work_end_time ?? '17:00:00';
            
            // Handle both H:i and H:i:s formats
            $workStart = str_contains($workStartTime, ':') && substr_count($workStartTime, ':') === 1
                ? Carbon::createFromFormat('H:i', $workStartTime)->setDateFrom($date)
                : Carbon::createFromFormat('H:i:s', $workStartTime)->setDateFrom($date);
                
            $workEnd = str_contains($workEndTime, ':') && substr_count($workEndTime, ':') === 1
                ? Carbon::createFromFormat('H:i', $workEndTime)->setDateFrom($date)
                : Carbon::createFromFormat('H:i:s', $workEndTime)->setDateFrom($date);

            if ($isLate) {
                // Late check-in: 5-60 minutes after work start
                $lateMinutes = rand(5, 60);
                $checkIn = $workStart->copy()->addMinutes($lateMinutes);
                $status = 'late';
            } else {
                // On-time check-in: 0-30 minutes before work start
                $earlyMinutes = rand(0, 30);
                $checkIn = $workStart->copy()->subMinutes($earlyMinutes);
                $status = 'present';
            }

            // Check-out time: usually around work end time with some variation
            $checkOut = null;
            if (rand(1, 100) <= 85) { // 85% chance of check-out
                $checkOutVariation = rand(-30, 60); // -30 to +60 minutes variation
                $checkOut = $workEnd->copy()->addMinutes($checkOutVariation);

                // Ensure check-out is after check-in
                if ($checkOut->lte($checkIn)) {
                    $checkOut = $checkIn->copy()->addHours(rand(4, 8));
                }
            }

            $this->createAttendanceRecord($employee, $location, $date, $status, $checkIn, $checkOut);
        }
    }

    private function createAttendanceRecord(Employee $employee, Location $location, Carbon $date, string $status, ?Carbon $checkIn = null, ?Carbon $checkOut = null)
    {
        $attendance = Attendance::create([
            'employee_id' => $employee->id,
            'location_id' => $location->id,
            'date' => $date->format('Y-m-d'),
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'status' => $status,
            'notes' => $this->generateNotes($status),
        ]);

        // Create attendance logs
        if ($checkIn) {
            AttendanceLog::create([
                'attendance_id' => $attendance->id,
                'employee_id' => $employee->id,
                'location_id' => $location->id,
                'action' => 'check_in',
                'action_time' => $checkIn,
                'method' => $this->getRandomMethod(),
                'notes' => 'Check-in recorded',
            ]);
        }

        if ($checkOut) {
            AttendanceLog::create([
                'attendance_id' => $attendance->id,
                'employee_id' => $employee->id,
                'location_id' => $location->id,
                'action' => 'check_out',
                'action_time' => $checkOut,
                'method' => $this->getRandomMethod(),
                'notes' => 'Check-out recorded',
            ]);
        }
    }

    private function generateNotes(string $status): ?string
    {
        $notes = [
            'present' => [
                null,
                'Regular attendance',
                'On time',
                'Good performance',
            ],
            'late' => [
                'Traffic jam',
                'Public transport delay',
                'Personal matter',
                'Overslept',
                'Family emergency',
            ],
            'absent' => [
                'Sick leave',
                'Personal leave',
                'Family emergency',
                'Medical appointment',
                'Annual leave',
            ],
        ];

        $statusNotes = $notes[$status] ?? [null];
        return $statusNotes[array_rand($statusNotes)];
    }

    private function getRandomMethod(): string
    {
        $methods = ['face_recognition', 'manual'];
        return $methods[array_rand($methods)];
    }
}