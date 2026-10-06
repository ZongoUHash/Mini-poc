<?php

namespace Database\Factories;

use App\Models\AttendanceSession;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AttendanceSession>
 */
class AttendanceSessionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'token' => fake()->unique()->sha256(),
            'type' => 'arrival',
            'starts_at' => now(),
            'expires_at' => now()->addMinutes(5),
            'closed_at' => null,
        ];
    }
}
