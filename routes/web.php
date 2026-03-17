<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\AbsenceController;

Route::get('/', [ShiftController::class, 'index'])->name('shifts.index');
Route::post('/shifts/generate-day', [ShiftController::class, 'generateDay'])->name('shifts.generate_day');
Route::post('/shifts/generate-range', [ShiftController::class, 'generateRange'])->name('shifts.generate_range');
Route::post('/absences', [AbsenceController::class, 'store'])->name('absences.store');
