<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Location;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attendance>
 */
class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $date = fake()->dateTimeBetween('-30 days', 'now');
        $checkIn = Carbon::parse($date)->setTime(8, fake()->numberBetween(0, 59));
        $checkOut = $checkIn->copy()->addHours(fake()->numberBetween(7, 9))->addMinutes(fake()->numberBetween(0, 59));

        return [
            'employee_id' => Employee::factory(),
            'location_id' => Location::factory(),
            'date' => $date,
            'check_in' => $checkIn,
            'check_out' => fake()->boolean(80) ? $checkOut : null,
            'check_in_lat' => fake()->latitude(-6.3, -6.1),
            'check_in_lng' => fake()->longitude(106.7, 106.9),
            'check_out_lat' => fake()->boolean(80) ? fake()->latitude(-6.3, -6.1) : null,
            'check_out_lng' => fake()->boolean(80) ? fake()->longitude(106.7, 106.9) : null,
            'status' => fake()->randomElement(['present', 'late', 'absent']),
        ];
    }

    /**
     * Indicate that the attendance is for today.
     */
    public function today(): static
    {
        return $this->state(fn (array $attributes) => [
            'date' => Carbon::today(),
        ]);
    }

    /**
     * Indicate that the employee is present.
     */
    public function present(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'present',
        ]);
    }

    /**
     * Indicate that the employee is late.
     */
    public function late(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'late',
        ]);
    }

    /**
     * Indicate that the employee is absent.
     */
    public function absent(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'absent',
            'check_in' => null,
            'check_out' => null,
            'check_in_lat' => null,
            'check_in_lng' => null,
            'check_out_lat' => null,
            'check_out_lng' => null,
        ]);
    }
}