<?php

namespace Tests\Feature;

use App\Models\AttendanceSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicAttendanceBoardTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_board_shows_only_active_qr_sessions(): void
    {
        $activeSession = AttendanceSession::factory()->create([
            'type' => 'arrival',
            'expires_at' => now()->addMinutes(5),
        ]);
        $expiredSession = AttendanceSession::factory()->create([
            'type' => 'departure',
            'expires_at' => now()->subMinute(),
        ]);
        $closedSession = AttendanceSession::factory()->create([
            'type' => 'departure',
            'expires_at' => now()->addMinutes(5),
            'closed_at' => now(),
        ]);

        $this->get(route('attendance.board'))
            ->assertOk()
            ->assertSee('QR de pointage arrivée')
            ->assertSee($activeSession->token)
            ->assertDontSee($expiredSession->token)
            ->assertDontSee($closedSession->token);
    }

    public function test_public_board_status_only_returns_active_sessions(): void
    {
        $activeSession = AttendanceSession::factory()->create(['type' => 'arrival', 'expires_at' => now()->addMinutes(5)]);
        AttendanceSession::factory()->create(['type' => 'departure', 'expires_at' => now()->subMinute()]);

        $this->getJson(route('attendance.board.sessions'))
            ->assertOk()
            ->assertJsonCount(1, 'sessions')
            ->assertJsonPath('sessions.0.type', $activeSession->type);
    }
}
