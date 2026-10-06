<?php

namespace Tests\Feature;

use App\Models\AttendanceSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HrAttendanceManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_hr_can_generate_a_qr_and_replace_the_previous_one_of_the_same_type(): void
    {
        $hr = User::factory()->create(['role' => 'hr']);
        $previousSession = AttendanceSession::factory()->create(['type' => 'arrival', 'expires_at' => now()->addMinutes(10)]);

        $this->actingAs($hr)
            ->post(route('hr.sessions.create'), ['type' => 'arrival', 'validity_value' => 5, 'duration_unit' => 'minutes'])
            ->assertRedirect(route('hr.dashboard'));

        $this->assertNotNull($previousSession->fresh()->closed_at);
        $this->assertDatabaseCount('attendance_sessions', 2);
        $this->assertDatabaseHas('attendance_sessions', ['type' => 'arrival', 'closed_at' => null]);
    }

    public function test_hr_can_generate_a_qr_with_an_hour_based_duration(): void
    {
        $hr = User::factory()->create(['role' => 'hr']);

        $this->actingAs($hr)
            ->post(route('hr.sessions.create'), ['type' => 'departure', 'validity_value' => 2, 'duration_unit' => 'hours'])
            ->assertRedirect(route('hr.dashboard'));

        $session = AttendanceSession::query()->sole();

        $this->assertTrue($session->expires_at->equalTo($session->starts_at->copy()->addHours(2)));
    }

    public function test_hr_cannot_generate_a_qr_for_more_than_twenty_four_hours(): void
    {
        $hr = User::factory()->create(['role' => 'hr']);

        $this->actingAs($hr)
            ->from(route('hr.dashboard'))
            ->post(route('hr.sessions.create'), ['type' => 'arrival', 'validity_value' => 25, 'duration_unit' => 'hours'])
            ->assertRedirect(route('hr.dashboard'))
            ->assertSessionHasErrors('validity_value');
    }

    public function test_hr_can_deactivate_an_active_qr(): void
    {
        $hr = User::factory()->create(['role' => 'hr']);
        $session = AttendanceSession::factory()->create(['expires_at' => now()->addMinutes(5)]);

        $this->actingAs($hr)
            ->delete(route('hr.sessions.close', $session))
            ->assertRedirect(route('hr.dashboard'));

        $this->assertNotNull($session->fresh()->closed_at);
    }
}
