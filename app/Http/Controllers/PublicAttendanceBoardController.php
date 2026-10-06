<?php

namespace App\Http\Controllers;

use App\Models\AttendanceSession;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class PublicAttendanceBoardController extends Controller
{
    public function index(): View
    {
        $sessions = $this->activeSessions();

        return view('public.attendance-board', [
            'sessions' => $sessions,
            'boardSignature' => $this->signature($sessions),
        ]);
    }

    public function sessions(): JsonResponse
    {
        $sessions = $this->activeSessions();

        return response()->json([
            'signature' => $this->signature($sessions),
            'sessions' => $sessions->map(fn (AttendanceSession $session): array => [
                'type' => $session->type,
                'expires_at' => $session->expires_at->toIso8601String(),
            ])->values(),
        ]);
    }

    /**
     * @return Collection<int, AttendanceSession>
     */
    private function activeSessions(): Collection
    {
        return AttendanceSession::query()
            ->whereNull('closed_at')
            ->where('expires_at', '>', now())
            ->orderBy('type')
            ->get();
    }

    /**
     * @param  Collection<int, AttendanceSession>  $sessions
     */
    private function signature(Collection $sessions): string
    {
        return hash('sha256', $sessions->map(fn (AttendanceSession $session): string => $session->token.$session->expires_at->timestamp)->join('|'));
    }
}
