<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class HrDashboardController extends Controller
{
    public function index(): View
    {
        $sessions = AttendanceSession::query()
            ->whereNull('closed_at')
            ->where('expires_at', '>', now())
            ->latest('created_at')
            ->get();

        $records = AttendanceRecord::query()
            ->with(['employee', 'attendanceSession'])
            ->whereDate('pointed_at', today())
            ->latest('pointed_at')
            ->get();

        return view('hr.dashboard', [
            'sessions' => $sessions,
            'records' => $records,
            'activeEmployeesCount' => Employee::query()->where('is_active', true)->count(),
        ]);
    }

    public function createSession(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'in:arrival,departure'],
            'validity_minutes' => ['required', 'integer', 'min:1', 'max:60'],
        ]);

        AttendanceSession::query()
            ->where('type', $validated['type'])
            ->whereNull('closed_at')
            ->where('expires_at', '>', now())
            ->update(['closed_at' => now()]);

        $session = AttendanceSession::create([
            'token' => Str::random(48),
            'type' => $validated['type'],
            'starts_at' => now(),
            'expires_at' => now()->addMinutes((int) $validated['validity_minutes']),
        ]);

        return to_route('hr.dashboard')->with('generated_session', $session->token);
    }

    public function closeSession(AttendanceSession $attendanceSession): RedirectResponse
    {
        if ($attendanceSession->closed_at === null) {
            $attendanceSession->update(['closed_at' => now()]);
        }

        return to_route('hr.dashboard')->with('status', 'Le QR code a été désactivé.');
    }
}
