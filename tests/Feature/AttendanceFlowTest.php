<?php

namespace Tests\Feature;

use App\Models\AttendanceSession;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_can_register_arrival_then_departure_with_location(): void
    {
        $user = User::factory()->create(['role' => 'employee']);
        $employee = Employee::factory()->create(['user_id' => $user->id]);
        $arrival = AttendanceSession::factory()->create(['type' => 'arrival', 'expires_at' => now()->addMinutes(5)]);
        $departure = AttendanceSession::factory()->create(['type' => 'departure', 'expires_at' => now()->addMinutes(5)]);

        $this->actingAs($user)
            ->postJson(route('attendance.point', $arrival->token), ['latitude' => 12.3714, 'longitude' => -1.5197, 'accuracy_meters' => 12])
            ->assertOk()
            ->assertJsonPath('message', 'Arrivée enregistrée.');

        $this->actingAs($user)
            ->postJson(route('attendance.point', $departure->token), ['latitude' => 12.3714, 'longitude' => -1.5197, 'accuracy_meters' => 12])
            ->assertOk()
            ->assertJsonPath('message', 'Départ enregistré.');

        $this->assertDatabaseCount('attendance_records', 2);
        $this->assertDatabaseHas('attendance_records', ['employee_id' => $employee->id, 'type' => 'departure']);
    }

    public function test_departure_is_rejected_without_arrival(): void
    {
        $user = User::factory()->create(['role' => 'employee']);
        Employee::factory()->create(['user_id' => $user->id]);
        $departure = AttendanceSession::factory()->create(['type' => 'departure', 'expires_at' => now()->addMinutes(5)]);

        $this->actingAs($user)
            ->postJson(route('attendance.point', $departure->token), ['latitude' => 12.3714, 'longitude' => -1.5197])
            ->assertUnprocessable()
            ->assertJsonPath('message', 'Un départ ne peut pas être enregistré sans arrivée aujourd’hui.');
    }
}
