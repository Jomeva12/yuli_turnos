<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\AbsenceController;

use App\Http\Controllers\EmployeeController;

Route::get('/', [ShiftController::class, 'index'])->name('shifts.index');
Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
Route::post('/employees/toggle-area', [EmployeeController::class, 'toggleArea'])->name('employees.toggle_area');
Route::post('/absences/clear-month', [AbsenceController::class, 'clearMonth'])->name('absences.clear_month');
Route::post('/shifts/generate-day', [ShiftController::class, 'generateDay'])->name('shifts.generate_day');
Route::post('/shifts/generate-range', [ShiftController::class, 'generateRange'])->name('shifts.generate_range');
Route::post('/shifts/generate-month', [ShiftController::class, 'generateMonth'])->name('shifts.generate_month');
Route::post('/shifts/clear-month', [ShiftController::class, 'clearMonth'])->name('shifts.clear_month');
Route::get('/shifts/timeline/{date?}', [ShiftController::class, 'timeline'])->name('shifts.timeline');
Route::post('/absences', [AbsenceController::class, 'store'])->name('absences.store');
