<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\AbsenceController;

use App\Http\Controllers\EmployeeController;

Route::get('/', [ShiftController::class, 'index'])->name('shifts.index');
Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
Route::post('/employees/toggle-area', [EmployeeController::class, 'toggleArea'])->name('employees.toggle_area');
Route::post('/shifts/generate-day', [ShiftController::class, 'generateDay'])->name('shifts.generate_day');
Route::post('/shifts/generate-range', [ShiftController::class, 'generateRange'])->name('shifts.generate_range');
Route::post('/absences', [AbsenceController::class, 'store'])->name('absences.store');
