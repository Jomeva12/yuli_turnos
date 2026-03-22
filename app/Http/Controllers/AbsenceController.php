<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AbsenceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'type' => 'required|in:vacation,incapacity,calamity',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        Absence::create($validated);

        return back()->with('success', 'Novedad registrada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Absence $absence)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Absence $absence)
    {
        //
    }

    /**
     * Remove all absences for a specific month.
     */
    public function clearMonth(Request $request)
    {
        $monthStr = $request->input('month', date('Y-m'));
        $carbonMonth = \Carbon\Carbon::parse($monthStr);

        Absence::whereYear('start_date', $carbonMonth->year)
               ->whereMonth('start_date', $carbonMonth->month)
               ->orWhere(function($query) use ($carbonMonth) {
                   $query->whereYear('end_date', $carbonMonth->year)
                         ->whereMonth('end_date', $carbonMonth->month);
               })
               ->delete();

        return back()->with('success', "Todas las novedades de " . $carbonMonth->translatedFormat('F Y') . " han sido eliminadas.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Absence $absence)
    {
        //
    }
}
