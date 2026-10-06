<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\EmployeePortalController;
use App\Http\Controllers\HrDashboardController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/rh');
Route::get('/rh', [HrDashboardController::class, 'index'])->name('hr.dashboard');
Route::post('/rh/sessions', [HrDashboardController::class, 'createSession'])->name('hr.sessions.create');
Route::get('/salarié', [EmployeePortalController::class, 'index'])->name('employee.portal');
Route::post('/salarié', [EmployeePortalController::class, 'select'])->name('employee.select');
Route::post('/salarié/réinitialiser', [EmployeePortalController::class, 'reset'])->name('employee.reset');
Route::get('/pointer/{token}', [AttendanceController::class, 'scan'])->name('attendance.scan');
Route::post('/pointer/{token}', [AttendanceController::class, 'point'])->name('attendance.point');
