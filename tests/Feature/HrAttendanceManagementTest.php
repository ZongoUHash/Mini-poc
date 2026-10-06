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
            ->post(route('hr.sessions.create'), ['type' => 'arrival', 'validity_minutes' => 5])
            ->assertRedirect(route('hr.dashboard'));

        $this->assertNotNull($previousSession->fresh()->closed_at);
        $this->assertDatabaseCount('attendance_sessions', 2);
        $this->assertDatabaseHas('attendance_sessions', ['type' => 'arrival', 'closed_at' => null]);
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
