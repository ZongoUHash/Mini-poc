<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\EmployeePortalController;
use App\Http\Controllers\HrDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (! auth()->check()) {
        return to_route('login');
    }

    return to_route(auth()->user()->role === 'hr' ? 'hr.dashboard' : 'employee.portal');
});

Route::middleware('guest')->group(function (): void {
    Route::get('/connexion', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/connexion', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::post('/deconnexion', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth')->name('logout');

Route::middleware(['auth', 'role:hr'])->prefix('rh')->name('hr.')->group(function (): void {
    Route::get('/', [HrDashboardController::class, 'index'])->name('dashboard');
    Route::post('/sessions', [HrDashboardController::class, 'createSession'])->name('sessions.create');
    Route::delete('/sessions/{attendanceSession}', [HrDashboardController::class, 'closeSession'])->name('sessions.close');
});

Route::middleware(['auth', 'role:employee'])->group(function (): void {
    Route::get('/salarié', [EmployeePortalController::class, 'index'])->name('employee.portal');
    Route::get('/pointer/{token}', [AttendanceController::class, 'scan'])->name('attendance.scan');
    Route::post('/pointer/{token}', [AttendanceController::class, 'point'])->name('attendance.point');
});
