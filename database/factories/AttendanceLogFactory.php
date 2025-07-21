<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\AttendanceLog;
use App\Models\Employee;
use App\Models\Location;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AttendanceLog>
 */
class AttendanceLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'attendance_id' => Attendance::factory(),
            'employee_id' => Employee::factory(),
            'location_id' => Location::factory(),
            'action' => fake()->randomElement(['check_in', 'check_out']),
            'action_time' => fake()->dateTimeBetween('-30 days', 'now'),
            'method' => 'face_recognition',
            'latitude' => fake()->latitude(-6.3, -6.1),
            'longitude' => fake()->longitude(106.7, 106.9),
            'face_similarity' => fake()->randomFloat(2, 0.75, 0.99),
            'face_verified' => fake()->boolean(90),
            'face_api_response' => [
                'status' => '200',
                'verified' => true,
                'similarity' => fake()->randomFloat(2, 0.75, 0.99),
                'user_id' => 'EMP' . fake()->numberBetween(1, 999),
            ],
        ];
    }

    /**
     * Indicate that this is a check-in log.
     */
    public function checkIn(): static
    {
        return $this->state(fn (array $attributes) => [
            'action' => 'check_in',
        ]);
    }

    /**
     * Indicate that this is a check-out log.
     */
    public function checkOut(): static
    {
        return $this->state(fn (array $attributes) => [
            'action' => 'check_out',
        ]);
    }

    /**
     * Indicate that face verification failed.
     */
    public function faceVerificationFailed(): static
    {
        return $this->state(fn (array $attributes) => [
            'face_verified' => false,
            'face_similarity' => fake()->randomFloat(2, 0.10, 0.70),
            'face_api_response' => [
                'status' => '200',
                'verified' => false,
                'similarity' => fake()->randomFloat(2, 0.10, 0.70),
                'user_id' => null,
            ],
        ]);
    }
}