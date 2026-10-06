<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function scan(Request $request, string $token): View
    {
        return view('employee.scan', [
            'attendanceSession' => AttendanceSession::query()->where('token', $token)->first(),
            'employee' => $request->user()->employee,
        ]);
    }

    public function point(Request $request, string $token): JsonResponse
    {
        $employee = $request->user()->employee;
        if ($employee === null || ! $employee->is_active) {
            return response()->json(['message' => 'Votre compte salarié n’est pas actif.'], 403);
        }

        $attendanceSession = AttendanceSession::query()->where('token', $token)->first();
        if ($attendanceSession === null || ! $attendanceSession->isOpen()) {
            return response()->json(['message' => 'Ce QR code est expiré ou a été remplacé.'], 422);
        }

        $validated = $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'accuracy_meters' => ['nullable', 'numeric', 'min:0', 'max:100000'],
        ]);

        $alreadyPointed = AttendanceRecord::query()
            ->where('employee_id', $employee->id)
            ->where('attendance_session_id', $attendanceSession->id)
            ->exists();
        if ($alreadyPointed) {
            return response()->json(['message' => 'Vous avez déjà pointé sur ce QR code.'], 422);
        }

        if ($attendanceSession->type === 'departure') {
            $hasArrival = AttendanceRecord::query()
                ->where('employee_id', $employee->id)
                ->where('type', 'arrival')
                ->whereDate('pointed_at', today())
                ->exists();
            if (! $hasArrival) {
                return response()->json(['message' => 'Un départ ne peut pas être enregistré sans arrivée aujourd’hui.'], 422);
            }
        }

        AttendanceRecord::create([
            'employee_id' => $employee->id,
            'attendance_session_id' => $attendanceSession->id,
            'type' => $attendanceSession->type,
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'accuracy_meters' => isset($validated['accuracy_meters']) ? (int) round($validated['accuracy_meters']) : null,
            'pointed_at' => now(),
        ]);

        return response()->json([
            'message' => $attendanceSession->type === 'arrival' ? 'Arrivée enregistrée.' : 'Départ enregistré.',
            'pointed_at' => now()->format('H:i:s'),
        ]);
    }
}
