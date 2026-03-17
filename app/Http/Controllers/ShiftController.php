<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $employees = \App\Models\Employee::with('areas')->orderBy('name')->get();
        $month = $request->input('month', date('Y-m'));
        $carbonMonth = \Carbon\Carbon::parse($month);
        $daysInMonth = $carbonMonth->daysInMonth;

        $shifts = \App\Models\Shift::whereYear('date', $carbonMonth->year)
                                   ->whereMonth('date', $carbonMonth->month)
                                   ->get()
                                   ->groupBy('employee_id');

        $absences = \App\Models\Absence::where(function($q) use ($carbonMonth) {
            $q->whereMonth('start_date', $carbonMonth->month)
               ->orWhereMonth('end_date', $carbonMonth->month);
        })->get()->groupBy('employee_id');

        return view('shifts.index', compact('employees', 'month', 'daysInMonth', 'shifts', 'absences'));
    }

    public function generateDay(Request $request)
    {
        $date = $request->input('date', date('Y-m-d'));
        $dayOfWeek = \Carbon\Carbon::parse($date)->dayOfWeek; // 0 (Sun) - 6 (Sat)
        
        $startOfWeek = \Carbon\Carbon::parse($date)->startOfWeek()->format('Y-m-d');
        $endOfWeek = \Carbon\Carbon::parse($date)->endOfWeek()->format('Y-m-d');

        // Eliminar turnos previos de ese día para regenerar
        Shift::where('date', $date)->delete();

        // 1. Obtener empleados activos y ausentes
        $absences = \App\Models\Absence::where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->pluck('employee_id')->toArray();

        $employees = \App\Models\Employee::whereNotIn('id', $absences)->with('areas')->get();

        // 2. Contar turnos partidos de esta semana por empleado
        $weeklySplits = Shift::whereBetween('date', [$startOfWeek, $endOfWeek])
            ->where('type', 'partido')
            ->selectRaw('employee_id, count(*) as count')
            ->groupBy('employee_id')
            ->pluck('count', 'employee_id')
            ->toArray();

        // 3. Obtener la plantilla del día de la base de datos
        // Si no hay plantilla para el día específico (ej. Martes=2), usar default (0)
        $dayTemplates = \App\Models\ShiftTemplate::with('area')->where('day_of_week', $dayOfWeek)->get();
        if ($dayTemplates->isEmpty()) {
            $dayTemplates = \App\Models\ShiftTemplate::with('area')->where('day_of_week', 0)->get();
        }

        // Agrupar las plantillas por área (id)
        $groupedTemplates = $dayTemplates->groupBy('area_id');

        // 4. Asignar por área
        $assignedEmployees = [];

        foreach ($groupedTemplates as $areaId => $templates) {
            $area = \App\Models\Area::find($areaId);
            if (!$area) continue;

            // Encontrar empleados que pertenezcan a esta área y no estén asignados
            $eligibleEmployees = $employees->filter(function($emp) use ($areaId, $assignedEmployees) {
                return !in_array($emp->id, $assignedEmployees) && 
                       $emp->areas->contains('id', $areaId);
            })->shuffle();

            foreach ($templates as $template) {
                // El template puede requerir múltiples personas para el mismo horario
                for ($i = 0; $i < $template->required_count; $i++) {
                    if ($eligibleEmployees->isEmpty()) break;

                    // Filtrar por restricciones de turnos partidos
                    $selectedEmployee = null;
                    $isPartido = $template->type == 'partido';

                    foreach ($eligibleEmployees as $key => $emp) {
                        $splitsCount = $weeklySplits[$emp->id] ?? 0;
                        if ($isPartido && $splitsCount >= 2) {
                            continue; // No puede tomar más partidos
                        }
                        
                        $selectedEmployee = $emp;
                        $eligibleEmployees->forget($key);
                        break;
                    }

                    // Fallback: si todos tienen 2 partidos pero necesitamos cubrirlo, rompemos regla.
                    if (!$selectedEmployee && $eligibleEmployees->isNotEmpty()) {
                        $selectedEmployee = $eligibleEmployees->pop();
                    }

                    if ($selectedEmployee) {
                        Shift::create([
                            'employee_id' => $selectedEmployee->id,
                            'area_id' => $area->id,
                            'date' => $date,
                            'schedule' => $template->schedule,
                            'type' => $template->type
                        ]);
                        
                        $assignedEmployees[] = $selectedEmployee->id;
                        if ($isPartido) {
                            $weeklySplits[$selectedEmployee->id] = ($weeklySplits[$selectedEmployee->id] ?? 0) + 1;
                        }
                    }
                }
            }
        }

        return back()->with('success', 'Turnos generados automáticamente para el día ' . $date);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Shift $shift)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Shift $shift)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Shift $shift)
    {
        //
    }
}
